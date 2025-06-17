<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/header.php');

// Redireccionar si no está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

<main class="flex-grow container mx-auto p-8">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Historial de Citas</h2>
    <div id="calendar" class="bg-white p-4 rounded shadow-lg"></div>

    <!-- Modal de comentarios -->
    <div id="comentarioModal" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex justify-center items-center hidden z-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 relative">
            <button onclick="cerrarModal()" class="absolute top-2 right-3 text-gray-600 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-semibold mb-4 text-purple-700">Comentarios de la Cita</h3>
            <p class="mb-4"><strong class="text-gray-700">Estado de la cita:</strong> <span id="estadoCita" class="text-blue-600 font-medium"></span></p>
            <div id="contenidoComentario" class="text-gray-700"></div>
        </div>
    </div>
</main>

<script>
function cerrarModal() {
    document.getElementById('comentarioModal').classList.add('hidden');
    document.getElementById('contenidoComentario').innerHTML = '';
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        events: 'historial_citas_paciente.php',
        eventColor: '#60A5FA',
        eventTextColor: '#fff',
        eventClick: function(info) {
            const citaId = info.event.id;

            fetch('obtener_comentario.php?id=' + citaId)
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('contenidoComentario');
                    const estadoSpan = document.getElementById('estadoCita');

                    estadoSpan.textContent = data.estado || 'No disponible';
                    contenedor.innerHTML = '';

                    if (!data.comentarios || data.comentarios.length === 0) {
                        contenedor.innerHTML = '<p class="text-gray-500">No hay comentarios para esta cita.</p>';
                    } else {
                        data.comentarios.forEach(c => {
                            const p = document.createElement('p');
                            p.className = 'mb-2';
                            p.innerHTML = `<span class="font-medium text-purple-700">📅 ${c.fecha_comentario}:</span> ${c.comentario}`;
                            contenedor.appendChild(p);
                        });
                    }

                    document.getElementById('comentarioModal').classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                    alert("Error al cargar los comentarios");
                });
        }
    });

    calendar.render();
});
</script>


<?php include('../../../../includes/footer.php'); ?>
</body>
</html>
