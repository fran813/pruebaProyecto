<?php
session_start();
include('../../../../includes/header.php');
include('../../../../includes/db.php');

// Solo accesible para fisioterapeutas
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'fisioterapeuta') {
    header('Location: ../../login.php');
    exit();
}

// Obtener pacientes
$stmt = $pdo->prepare("SELECT id_usuario, nombre, telefono FROM usuarios WHERE rol = 'paciente' AND activo = 1 ORDER BY nombre ASC");
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-grow max-w-4xl mx-auto mt-10 mb-20 px-4">
    <h2 class="text-2xl font-bold text-purple-700 mb-6">Contactar con Paciente</h2>

     <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-center id=mensajeComentario">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <form action="procesar_contacto.php" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        <div>
            <label for="buscadorPaciente" class="block mb-2 font-semibold text-gray-700">Buscar paciente:</label>
            <input type="text" id="buscadorPaciente" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" list="listaPacientes" placeholder="Nombre o teléfono..." autocomplete="off" required>

            <datalist id="listaPacientes">
                <?php foreach ($pacientes as $p): ?>
                    <option data-id="<?= $p['id_usuario'] ?>" value="<?= htmlspecialchars($p['nombre']) . ' - Tlf:' . htmlspecialchars($p['telefono']) ?>"></option>
                <?php endforeach; ?>
            </datalist>

            <!-- Campo oculto con el ID real del paciente -->
            <input type="hidden" name="paciente_id" id="paciente_id" required>
        </div>

        <div>
            <label for="mensaje" class="block mb-2 font-semibold text-gray-700">Mensaje:</label>
            <textarea name="mensaje" id="mensaje" rows="6" class="focus:outline-none focus:ring-2 focus:ring-purple-500 w-full p-3 border rounded-md resize-none" placeholder="Escribe tu mensaje..." required></textarea>
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded">
                Enviar mensaje
            </button>
            <a href="../fisio_dashboard.php" class="text-purple-600 hover:underline font-medium px-4">← Volver al inicio</a>
        </div>
    </form>
</main>

<script>
// Mapear los pacientes (nombre + teléfono → id)
const pacientes = <?= json_encode($pacientes) ?>;

document.getElementById('buscadorPaciente').addEventListener('input', function () {
    const valor = this.value.toLowerCase();
    const idInput = document.getElementById('paciente_id');

    const pacienteEncontrado = pacientes.find(p =>
        (p.nombre + ' - Tlf:' + p.telefono).toLowerCase() === valor
    );

    if (pacienteEncontrado) {
        idInput.value = pacienteEncontrado.id_usuario;
    } else {
        idInput.value = '';
    }
});
</script>

<?php include('../../../../includes/footer.php'); ?>
