<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/enviar_correo.php');
include('../../../../includes/logger.php');


if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'fisioterapeuta') {
    header('Location: ../login.php');
    exit();
}

// Obtener pacientes para autocompletado
$sql_pacientes = "SELECT id_usuario, nombre, email, telefono FROM usuarios WHERE rol = 'paciente'AND activo = 1 ORDER BY nombre ASC ";
$stmt_pacientes = $pdo->prepare($sql_pacientes);
$stmt_pacientes->execute();
$pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paciente_id = $_POST['paciente_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $comentario = $_POST['descripcion'];
    $estado = 'confirmado';
    $tipo_cita = $_POST['tipo_cita'] ?? 'Normal';
    $fecha_formateada = date('d-m-Y', strtotime($fecha));

    if ($tipo_cita === 'Bono') {
        $stmt_bono = $pdo->prepare("SELECT id_bono, cantidad FROM bonos WHERE id_usuario = ? AND cantidad > 0 LIMIT 1");
        $stmt_bono->execute([$paciente_id]);
        $bono = $stmt_bono->fetch(PDO::FETCH_ASSOC);

        if (!$bono) {
            $tipo_cita = 'Normal';
        }
    }

    $sql = "INSERT INTO agenda (fisioterapeuta_id, paciente_id, fecha, hora, estado, tipo_cita) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $paciente_id, $fecha, $hora, $estado, $tipo_cita]);
    $cita_id = $pdo->lastInsertId();

    $sql_comentario = "INSERT INTO comentarios_agenda (agenda_id, usuario_id, comentario, fecha_comentario) VALUES (?, ?, ?, NOW())";
    $stmt_comentario = $pdo->prepare($sql_comentario);
    $stmt_comentario->execute([$cita_id, $_SESSION['user_id'], $comentario]);

    if ($tipo_cita === 'Bono' && $bono) {
        $stmt_restar = $pdo->prepare("UPDATE bonos SET cantidad = cantidad - 1 WHERE id_bono = ? AND cantidad > 0");
        $stmt_restar->execute([$bono['id_bono']]);
    }

    $sql_paciente = "SELECT nombre, email FROM usuarios WHERE id_usuario = ?";
    $stmt_paciente = $pdo->prepare($sql_paciente);
    $stmt_paciente->execute([$paciente_id]);
    $paciente = $stmt_paciente->fetch(PDO::FETCH_ASSOC);

    $asunto = "Confirmación de cita en la clínica de fisioterapia";
    $mensajeHtml = "
        <p>Estimado/a <strong>{$paciente['nombre']}</strong>,</p>
        <p>Le informamos que su fisioterapeuta ha programado una cita para usted en nuestra clínica.</p>
        <p><strong>Fecha:</strong> {$fecha_formateada}<br>
        <strong>Hora:</strong> {$hora}</p>
        <p>Por favor, acuda a la clínica a la hora indicada. Si necesita cambiar o cancelar la cita, le rogamos que nos contacte lo antes posible.</p>
        <p>Gracias por confiar en nosotros.<br>
        <strong>Clínica de Fisioterapia PM</strong></p>
    ";

    enviarCorreoGeneral($paciente['email'], $paciente['nombre'], $asunto, $mensajeHtml);

    $accion = "El fisioterapeuta creó una cita para el paciente ID " . $paciente_id ." el día " . $fecha . " a las " . $hora;

    registrarLog($pdo, $_SESSION['user_id'], "Crear_cita_fisio", $accion);

    header("Location: /reservas_proyecto/pages/usuario/fisio/lista_citas/citas_asignadas.php");
    exit();
}
?>

<?php include('../../../../includes/header.php'); ?>

<div class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center text-purple-700 mb-4">Crear Nueva Cita</h2>

        <form action="crear_cita.php" method="POST" class="space-y-4">
            <div>
                <label for="buscador_paciente" class="block text-gray-700 font-medium">Buscar Paciente</label>
                <input type="text" id="buscador_paciente" placeholder="Nombre, teléfono o email"
                    class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                <input type="hidden" name="paciente_id" id="paciente_id" required>
                <ul id="sugerencias" class="mt-1 border border-gray-300 rounded-lg bg-white shadow-md hidden max-h-48 overflow-y-auto"></ul>
            </div>

            <div>
                <label for="fecha" class="block text-gray-700 font-medium">Fecha</label>
                <input type="date" name="fecha" id="fecha" required
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
            </div>

            <div>
                <label for="hora" class="block text-gray-700 font-medium">Hora</label>
                <select name="hora" id="hora" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                    <option value="">Selecciona una hora</option>
                    <?php
                    $horas = ['09:00', '10:00', '11:30', '12:30', '17:00', '18:00', '19:00', '20:00'];
                    foreach ($horas as $hora_opcion) {
                        echo "<option value=\"$hora_opcion\">$hora_opcion</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="tipo_cita" class="block text-sm font-medium text-gray-700 mt-4">Tipo de cita:</label>
                <select name="tipo_cita" id="tipo_cita" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                    <option value="Normal">Normal</option>
                    <option value="Bono" disabled>Bono (0 disponibles)</option>
                </select>
            </div>

            <div>
                <label for="descripcion" class="block text-gray-700 font-medium">Comentario</label>
                <textarea name="descripcion" id="descripcion" rows="4" required
                          class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none"></textarea>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                        class="w-full bg-purple-600 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
                    Crear Cita
                </button>
            </div>
        </form>

        <script>
            const input = document.getElementById('buscador_paciente');
            const hiddenId = document.getElementById('paciente_id');
            const sugerencias = document.getElementById('sugerencias');

            const pacientes = <?= json_encode($pacientes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

            input.addEventListener('input', () => {
                const texto = input.value.toLowerCase().trim();
                sugerencias.innerHTML = '';
                hiddenId.value = '';

                if (texto.length < 2) {
                    sugerencias.classList.add('hidden');
                    return;
                }

                const resultados = pacientes.filter(p =>
                    p.nombre.toLowerCase().includes(texto) ||
                    p.email.toLowerCase().includes(texto) ||
                    p.telefono.includes(texto)
                );

                if (resultados.length === 0) {
                    sugerencias.classList.add('hidden');
                    return;
                }

                resultados.forEach(p => {
                    const item = document.createElement('li');
                    item.className = 'px-4 py-2 hover:bg-purple-100 cursor-pointer';
                    item.textContent = `${p.nombre} – ${p.telefono} – ${p.email}`;
                    item.addEventListener('click', () => {
                        input.value = `${p.nombre} – ${p.telefono}`;
                        hiddenId.value = p.id_usuario;
                        sugerencias.classList.add('hidden');

                        // Actualizar bonos disponibles
                        const bonoOption = document.querySelector('#tipo_cita option[value="Bono"]');
                        const tipoCitaSelect = document.getElementById('tipo_cita');

                        fetch(`consultar_bono.php?paciente_id=${p.id_usuario}`)
                            .then(res => res.json())
                            .then(data => {
                                const bonos = data.bono || 0;
                                bonoOption.textContent = `Bono (${bonos} disponibles)`;
                                bonoOption.disabled = bonos === 0;
                                if (bonos === 0 && tipoCitaSelect.value === 'Bono') {
                                    tipoCitaSelect.value = 'Normal';
                                }
                            })
                            .catch(() => {
                                bonoOption.textContent = 'Bono (0 disponibles)';
                                bonoOption.disabled = true;
                                tipoCitaSelect.value = 'Normal';
                            });
                    });
                    sugerencias.appendChild(item);
                });

                sugerencias.classList.remove('hidden');
            });

            document.addEventListener('click', e => {
                if (!input.contains(e.target) && !sugerencias.contains(e.target)) {
                    sugerencias.classList.add('hidden');
                }
            });

            // Cargar horas disponibles dinámicamente
            document.getElementById('fecha').addEventListener('change', function () {
                const fecha = this.value;
                const horaSelect = document.getElementById('hora');

                if (!fecha) return;

                fetch(`horas_disponibles.php?fecha=${fecha}`)
                    .then(res => res.json())
                    .then(ocupadas => {
                        const todasLasHoras = [
                            '09:00', '10:00', '11:30', '12:30',
                            '17:00', '18:00', '19:00', '20:00'
                        ];

                        horaSelect.innerHTML = '<option value="">Selecciona una hora</option>';

                        todasLasHoras.forEach(hora => {
                            if (!ocupadas.includes(hora)) {
                                const option = document.createElement('option');
                                option.value = hora;
                                option.textContent = hora;
                                horaSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(err => console.error('Error al cargar horas disponibles:', err));
            });
        </script>

        <p class="text-center text-gray-600 text-sm mt-4">
            <a href="../fisio_dashboard.php" class="text-purple-600 hover:underline">Volver atrás</a>
        </p>
    </div>
</div>

<?php include('../../../../includes/footer.php'); ?>
