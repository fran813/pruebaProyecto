<?php
/*
 * Página para que pacientes puedan reservar citas con fisioterapeutas.
 * 
 * - Verifica que el usuario esté autenticado y sea paciente.
 * - Obtiene la lista de fisioterapeutas activos desde la base de datos.
 * - Muestra un calendario interactivo donde el paciente puede seleccionar fechas disponibles
 *   según el fisioterapeuta elegido.
 * - Permite seleccionar tipo y hora de cita mediante un modal con horas disponibles.
 * - Envía la reserva al servidor para crear la cita.
 * 
 * Usa FullCalendar para el calendario y comunicación vía fetch API para interactividad.
 */
session_start();
include('../../../../includes/header.php');
include('../../../../includes/db.php');

// Verificar que haya usuario logueado y que sea paciente, si no, redirigir a login
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: ../login.php');
    exit();
}

// Obtener lista de fisioterapeutas activos ordenados por nombre para mostrar en select
$stmt = $pdo->prepare("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'fisioterapeuta' AND activo = 1 ORDER BY nombre ASC");
$stmt->execute();
$fisioterapeutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<body class="flex flex-col min-h-screen bg-gray-100">

<main class="flex-grow container mx-auto p-8">

    <style>
        .fc-event-title {
            color: white !important;
        }

        .fc .fc-event-title {
            color: white !important;
        }
    </style>

    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Reservar una Cita</h2>

    <div class="mb-6">
        <label for="fisioterapeuta" class="block mb-2 font-semibold text-gray-700">Seleccionar Fisioterapeuta:</label>
        <select id="fisioterapeuta" class="w-full p-2 border rounded-md">
            <option value="">-- Selecciona un fisioterapeuta --</option>
            <?php foreach ($fisioterapeutas as $fisio): ?>
                <option value="<?= $fisio['id_usuario'] ?>"><?= htmlspecialchars($fisio['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="calendar" class="bg-white rounded-lg shadow p-4"></div>

</main>

<div id="modalHoras" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex justify-center items-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 relative">
        <button onclick="cerrarModal()" class="absolute top-2 right-3 text-gray-600 hover:text-red-600 text-2xl font-bold">&times;</button>
        <h3 class="text-lg font-semibold mb-4 text-purple-700">Selecciona una hora</h3>
        <select id="tipoCita" class="w-full p-2 border rounded-md" required>
            <option value=""> Selecciona el tipo de cita --</option>
            <option value="Normal">Normal</option>
            <option value="Bono">Bono</option>
        </select>
        <br><br>
        <div id="horasDisponibles" class="space-y-2"></div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    const calendarEl = document.getElementById('calendar');
    const fisioSelect = document.getElementById('fisioterapeuta');

    // Función para inicializar o reiniciar el calendario
    function initCalendar() {
        if (calendar) calendar.destroy();// destruir calendario anterior si existe

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            selectable: true,
            height: 'auto',
            selectAllow: function(selectInfo) {
                const day = selectInfo.start.getDay();
                return day !== 0 && day !== 6; // Evitar domingos y sábados
            },
            // Acción al hacer click en una fecha
            dateClick: function(info) {
                const fecha = info.dateStr; // Fecha seleccionada en formato ISO
                const fisioterapeutaId = fisioSelect.value; // ID fisioterapeuta seleccionado
                if (!fisioterapeutaId) {
                    alert('Por favor, selecciona un fisioterapeuta.');
                    return;
                }
                mostrarModalHoras(fecha, fisioterapeutaId); // Abrir modal para elegir hora
            },
            //Rango limite para que el paciente pueda reservar una cita 
            validRange: {
                start: new Date(), // hoy
                end: new Date(new Date().setMonth(new Date().getMonth() + 2)) // dentro de 2 meses
            },
            // Fuente de eventos: días ocupados del fisioterapeuta
            eventSources: [
                {
                    url: 'obtener_dias_disponibles_eventos.php',
                    method: 'GET',
                    extraParams: function() {
                        return {
                            id_fisio: fisioSelect.value
                        };
                    },
                    failure: () => alert('Error al cargar los días disponibles.'),
                }
            ]
        });

        calendar.render();
    }

    // Cuando se cambia el fisioterapeuta seleccionado, inicializa o destruye calendario
    fisioSelect.addEventListener('change', () => {
        if (fisioSelect.value) {
            initCalendar();
        } else if (calendar) {
            calendar.destroy();
        }
    });

});

// Mostrar modal para seleccionar hora, cargar bonos y horas disponibles
function mostrarModalHoras(fecha, fisioterapeutaId) {
    document.getElementById('modalHoras').classList.remove('hidden');
    const contenedor = document.getElementById('horasDisponibles');
    contenedor.innerHTML = 'Cargando...';

    // 1. Consultar cuántos bonos tiene el paciente
    fetch('obtener_bonos_usuario.php')
        .then(res => res.json())
        .then(data => {
            const selectTipo = document.getElementById('tipoCita');
            selectTipo.innerHTML = `
                <option value="">-- Selecciona el tipo de cita --</option>
                <option value="Normal">Normal</option>
            `;
            if (data.bonos > 0) {
                selectTipo.innerHTML += `<option value="Bono">Bono (${data.bonos} disponibles)</option>`;
            }
        });

    // 2. Obtener las horas disponibles
    fetch(`obtener_horas_disponibles.php?fecha=${fecha}&id_fisio=${fisioterapeutaId}`)
        .then(res => res.json())
        .then(data => {
            contenedor.innerHTML = '';

            if (!data.opciones) {
                contenedor.innerHTML = '<p class="text-red-600">No hay horas disponibles.</p>';
                return;
            }

            // Crear select para horas disponibles
            const selectHora = document.createElement('select');
            selectHora.className = 'w-full p-2 border rounded-md';
            selectHora.innerHTML = data.opciones;

             // Crear botón para reservar cita
            const botonReservar = document.createElement('button');
            botonReservar.className = 'w-full bg-purple-600 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300';
            botonReservar.textContent = 'Reservar Cita';
            botonReservar.onclick = () => {
                const tipo = document.getElementById('tipoCita').value;
                const horaSeleccionada = selectHora.value;
                if (!tipo || !horaSeleccionada) {
                    alert('Selecciona tipo de cita y hora.');
                    return;
                }
                reservarCita(fecha, horaSeleccionada, fisioterapeutaId, tipo);
            };

            // Añadir select y botón al contenedor
            contenedor.appendChild(selectHora);
            contenedor.appendChild(botonReservar);
        });
}

// Funcion para cerrar el modal 
function cerrarModal() {
    document.getElementById('modalHoras').classList.add('hidden');
}

function reservarCita(fecha, hora, fisioterapeutaId, tipo) {
    const datos = { fecha, hora, id_fisio: fisioterapeutaId, tipo };
    console.log("Datos enviados:", datos);
    fetch('reservar_cita.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include', // <<---- Esta línea es clave para enviar cookies de sesión
        body: JSON.stringify({ fecha, hora, id_fisio: fisioterapeutaId, tipo_cita: tipo })
    })
    .then(res => res.json())
    .then(data => {
        cerrarModal();
        alert(data.mensaje);
        location.reload();
    });
}



</script>

<?php include('../../../../includes/footer.php'); ?>

</body>
