<?php 
include('../../../../includes/header.php'); 
include('../../../../includes/db.php'); 
?>
<body class="flex flex-col min-h-screen bg-gray-100">

<style>
    .fc .fc-daygrid-event-dot {
        display: none !important;
    }
    .fc .fc-list-event-dot {
        display: none !important;
    }
</style>

<main class="max-w-6xl mx-auto px-4 mb-6 mt-10">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Citas Asignadas</h2>
    
    <div id="calendar" class="bg-white rounded-lg shadow p-4"></div>
</main>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-[rgba(0,0,0,0.3)] flex justify-center items-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 relative">
        <h2 class="text-2xl text-center font-semibold text-purple-700 mb-4">Detalles de la Cita</h2>
        <div id="modal-content"></div>
        <br>
        <!-- Botón para mostrar/ocultar comentarios -->
        <button 
            id="btnToggleComentarios" 
            type="button"
            class="bg-purple-200 text-purple-800 py-1 px-4 rounded hover:bg-purple-300 mb-1 w-full">
            Mostrar Comentarios
        </button>
        <!-- Contenedor de comentarios oculto por defecto -->
        <div id="comentarios-container" class="hidden max-h-48 overflow-y-auto p-2 rounded mb-4 bg-gray-50"></div>
        <!-- Formulario para cambiar estado -->
        <div id="modal-form" class="mt-1">
            <form id="estadoForm" action="cambiar_estado_citas.php" method="POST">
                <div id="estadoContainer" class="mt-2">
                    <input type="hidden" name="cita_id" id="cita_id">
                    <label for="estado" class="block text-gray-700">Estado de la cita:</label>
                    <select name="estado" id="estado" class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmado">Confirmada</option>
                        <option value="Realizado">Realizada</option>
                    </select>
                </div>
                <div class="flex justify-between mt-4">
                    <button type="submit" id="btnCambiarEstado" class="bg-purple-400 text-white py-2 px-6 rounded-lg hover:bg-purple-500 transition duration-300">Cambiar Estado</button>
                    <button type="button" onclick="closeModal()" id="btnCerrarModal" class="bg-gray-400 text-white py-2 px-6 rounded-lg hover:bg-gray-500 transition duration-300">Cerrar</button>
                </div>
            </form>

            <!-- Eliminar cita -->
            <form action="eliminar_citas_asignadas.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta cita?');">
                <input type="hidden" name="cita_id" id="cita_id_eliminar">
                <button type="submit" class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-600 transition duration-300 mt-2 w-full">Eliminar Cita</button>
            </form>
            <!-- Botón para mostrar el formulario de comentario -->
            <button 
                id="btnMostrarFormularioComentario" 
                class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition duration-300 mt-2 w-full">
                Añadir Comentario
            </button>

            <!-- Formulario oculto para escribir el comentario -->
            <form id="formComentario" class="hidden mt-2">
                <textarea 
                    id="nuevoComentario" 
                    class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" 
                    rows="3" 
                    placeholder="Escribe tu comentario..."></textarea>
                
                <!-- Botón para enviar el comentario -->
                <button 
                    type="button" 
                    onclick="enviarComentario()" 
                    class="bg-purple-400 text-white py-1 px-4 rounded mt-2 hover:bg-purple-600">
                    Guardar Comentario
                </button>
            </form>
            <br><br>
            <!-- Mensaje de confirmación -->
            <div id="mensajeComentario" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">Comentario guardado correctamente</div>
        </div>
    </div>
</div>

<!-- FullCalendar + Scripts -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: 'citas_eventos.php',
        eventClick: function(info) {
            const citaId = info.event.id;
            const estadoActual = info.event.extendedProps.estado;
            openModal(citaId,estadoActual);
        },
        displayEventTime: false,
        eventClassNames: function(arg) {
            switch (arg.event.extendedProps.estado) {
                case 'Pendiente':
                    return ['bg-yellow-100', 'text-yellow-800', 'border', 'border-yellow-300'];
                case 'Confirmado':
                    return ['bg-green-100', 'text-green-800', 'border', 'border-green-300'];
                case 'Realizado':
                    return ['bg-purple-100', 'text-purple-800', 'border', 'border-purple-300'];
                default:
                    return ['bg-gray-100', 'text-gray-800', 'border', 'border-gray-300'];
            }
        },
        eventDidMount: function(info) {
            info.el.classList.add('rounded', 'px-2', 'py-1', 'text-sm', 'font-medium');
        },
        height: 'auto'
    });
    calendar.render();

    //Enviar mensaje con fetch
    document.getElementById('estadoForm').addEventListener('submit', function(e) {
        e.preventDefault(); // evitar submit tradicional y recarga

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            if (text.trim() === "ok") {
                alert("Estado actualizado correctamente.");
                closeModal();
                location.reload();
            } else {
                alert("Error: " + text);
            }
        })
        .catch(error => {
            alert("Error en la comunicación con el servidor.");
            console.error(error);
        });
    });
});


function openModal(citaId) {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('cita_id_eliminar').value = citaId;
    document.getElementById('cita_id').value = citaId;


    fetch('obtener_detalles_cita.php?id=' + citaId)
        .then(response => response.json())
        .then(data => {
            
            // Convertir fecha a d-m-Y
            const fechaParts = data.fecha.split('-'); // [YYYY, MM, DD]
            const fechaFormateada = `${fechaParts[2]}-${fechaParts[1]}-${fechaParts[0]}`;
            //Quitamos los segundos en la hora 
            const horaSinSegundos = data.hora.substring(0, 5);


            // Solo detalles básicos en modal-content
            const modalContent = ` 
                <p><strong>Paciente:</strong> ${data.nombre_paciente}</p>
                <p><strong>Teléfono:</strong> ${data.telefono}</p>
                <p><strong>Fecha y Hora:</strong> ${fechaFormateada} / ${horaSinSegundos}</p>
                <p><strong>Estado:</strong> ${data.estado} <strong>Tipo de cita:</strong> ${data.tipo_cita}</p>
                `;
            document.getElementById('modal-content').innerHTML = modalContent;
            document.getElementById('cita_id').value = citaId;

            // Ocultar botón de eliminar si la cita ya ha pasado
            const citaFechaHora = new Date(`${data.fecha}T${data.hora}`);
            const ahora = new Date();
            const botonEliminar = document.querySelector('form[action="eliminar_citas_asignadas.php"]');
            if (citaFechaHora < ahora) {
                botonEliminar.classList.add('hidden');
            } else {
                botonEliminar.classList.remove('hidden');
            }

            // Ocultar botón "Cambiar Estado" y la select si ya está en "Realizado"
            const botonCambiarEstado = document.getElementById('btnCambiarEstado');
            const estadoContainer = document.getElementById('estadoContainer');
            const botonCerrar = document.getElementById('btnCerrarModal');

            if (data.estado === 'Realizado') {
                botonCambiarEstado.classList.add('hidden');
                estadoContainer.classList.add('hidden'); // Oculta select y label
                // Cambiar estilo del botón "Cerrar"
                botonCerrar.classList.add('w-full');
                botonCerrar.classList.remove('px-6');
            } else {
                botonCambiarEstado.classList.remove('hidden');
                estadoContainer.classList.remove('hidden');
                // Restaurar estilo original del botón "Cerrar"
                botonCerrar.classList.remove('w-full');
                botonCerrar.classList.add('px-6');
            }

            


            // Comentarios en el contenedor con scroll
            fetch('obtener_comentarios_cita.php?id=' + citaId)
                .then(res => res.json())
                .then(comentarios => {
                    const contenedorComentarios = document.getElementById('comentarios-container');
                    if (comentarios.length > 0) {
                        let htmlComentarios = '<ul class="list-disc list-inside">';
                        comentarios.forEach(c => {
                            htmlComentarios += `
                                <li class="mb-1">
                                    <small class="text-gray-500">${new Date(c.fecha_comentario).toLocaleString()}</small>: ${c.comentario}
                                </li>`;
                        });
                        htmlComentarios += '</ul>';
                        contenedorComentarios.innerHTML = htmlComentarios;
                    } else {
                        contenedorComentarios.innerHTML = '<p class="text-gray-500">No hay comentarios aún.</p>';
                    }
                })
                .catch(err => {
                    console.error('Error al cargar comentarios:', err);
                    document.getElementById('comentarios-container').innerHTML = '<p class="text-red-600">Error al cargar comentarios.</p>';
                });
        })
        .catch(error => console.error('Error al cargar los detalles de la cita:', error));
}

    // Alternar visibilidad de comentarios
    document.getElementById('btnToggleComentarios').addEventListener('click', function () {
        const comentarios = document.getElementById('comentarios-container');
        const estaVisible = !comentarios.classList.contains('hidden');

        comentarios.classList.toggle('hidden');
        this.textContent = estaVisible ? 'Mostrar Comentarios' : 'Ocultar Comentarios';
    });





    function closeModal() {
    // Ocultar el modal
    document.getElementById('modal').classList.add('hidden');

    // Limpiar contenido dinámico dentro del modal
    const modalContent = document.getElementById('modal-content');
    if (modalContent) {
        modalContent.innerHTML = '';
    }

    // Ocultar comentarios y reiniciar texto del botón
    const comentarios = document.getElementById('comentarios-container');
    const btnToggle = document.getElementById('btnToggleComentarios');
    if (comentarios && btnToggle) {
        comentarios.classList.add('hidden');
        btnToggle.textContent = 'Mostrar Comentarios';
    }

    // Ocultar formulario de nuevo comentario
    const formComentario = document.getElementById('formComentario');
    if (formComentario) {
        formComentario.classList.add('hidden');
    }

    // Limpiar el campo de nuevo comentario
    const inputComentario = document.getElementById('nuevoComentario');
    if (inputComentario) {
        inputComentario.value = '';
    }

    // Ocultar mensaje de confirmación si existe
    const mensaje = document.getElementById('mensajeComentario');
    if (mensaje) {
        mensaje.classList.add('hidden');
    }

    // Restaurar botones y estilos si existen
    const btnCambiarEstado = document.getElementById('btnCambiarEstado');
    const estadoContainer = document.getElementById('estadoContainer');
    const btnCerrar = document.getElementById('btnCerrarModal');

    if (btnCambiarEstado) btnCambiarEstado.classList.remove('hidden');
    if (estadoContainer) estadoContainer.classList.remove('hidden');
    if (btnCerrar) {
        btnCerrar.classList.remove('w-full');
        btnCerrar.classList.add('px-6');
    }
}



</script>
<script>
    // Mostrar o ocultar el formulario al hacer clic en el botón
    document.getElementById('btnMostrarFormularioComentario').addEventListener('click', function () {
        document.getElementById('formComentario').classList.toggle('hidden');
    });

    function enviarComentario() {
        const comentario = document.getElementById('nuevoComentario').value.trim();
        const citaId = document.getElementById('cita_id').value;

        if (comentario === '') {
            alert('Por favor, escribe un comentario.');
            return;
        }

        fetch('guardar_comentario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cita_id=${encodeURIComponent(citaId)}&comentario=${encodeURIComponent(comentario)}`
        })
        .then(response => response.text())
        .then(data => {
            // Mostrar mensaje
            document.getElementById('mensajeComentario').classList.remove('hidden');

            // Ocultar formulario y limpiar textarea
            document.getElementById('formComentario').classList.add('hidden');
            document.getElementById('nuevoComentario').value = '';

            // Crear y añadir el nuevo comentario al contenedor
            const contenedor = document.getElementById('comentarios-container');
            const fechaActual = new Date().toLocaleString();
            const nuevoComentarioHTML = `
                <li class="mb-1">
                    <small class="text-gray-500">${fechaActual}</small>: ${comentario}
                </li>`;

            const lista = contenedor.querySelector('ul');
            if (lista) {
                lista.innerHTML += nuevoComentarioHTML;
            } else {
                contenedor.innerHTML = `
                    <h3 class="font-semibold mb-2">Comentarios:</h3>
                    <ul class="list-disc list-inside">${nuevoComentarioHTML}</ul>`;
            }

            // Ocultar mensaje luego de 2 segundos
            setTimeout(() => {
                document.getElementById('mensajeComentario').classList.add('hidden');
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }


</script>

</body>

<?php include('../../../../includes/footer.php'); ?>
