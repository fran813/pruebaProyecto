<?php
session_start();
include('../../../../includes/header.php'); 
include('../../../../includes/db.php'); 

// Verificar si el usuario está autenticado como fisioterapeuta
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'fisioterapeuta') {
    header('Location: ../../../login.php');
    exit();
}

// Obtener todos los pacientes ordenados alfabéticamente
$sql = "SELECT id_usuario, nombre, telefono FROM usuarios WHERE rol = 'paciente' AND activo = 1 ORDER BY nombre ASC"; // Orden alfabético ascendente
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-grow max-w-5xl mx-auto mt-10 mb-10">
    <h2 class="text-2xl font-semibold text-purple-700 mb-4">Historial de Pacientes</h2>

    <!-- Buscador de Pacientes -->
    <div class="w-full max-w-4xl mx-auto mb-6 relative">
        <label for="buscadorPaciente" class="block text-gray-700 font-semibold mb-2">Paciente</label>
        <input type="text" id="buscadorPaciente" placeholder="Escribe el nombre del paciente" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
        <ul id="listaResultados" class="absolute z-10 bg-white border border-gray-300 w-full rounded-md mt-1 hidden max-h-60 overflow-y-auto"></ul>
    </div>


    <!-- Historial de Citas en cuadrícula -->
    <div id="historialCitas" class="w-full max-w-4xl mx-auto hidden">
        <h3 class="text-xl font-semibold text-purple-700 mb-4">Citas del Paciente</h3>
        <div id="listaCitas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Las citas se cargarán aquí -->
        </div>
    </div>
</main>

<script>
    const pacientes = <?php echo json_encode($pacientes); ?>;

    const buscador = document.getElementById('buscadorPaciente');
    const listaResultados = document.getElementById('listaResultados');

    buscador.addEventListener('input', () => {
        const texto = buscador.value.toLowerCase();
        listaResultados.innerHTML = '';

        if (texto.length === 0) {
            listaResultados.classList.add('hidden');
            return;
        }

        const resultados = pacientes.filter(p =>
            p.nombre.toLowerCase().includes(texto) || p.telefono.includes(texto)
        );

        if (resultados.length === 0) {
            listaResultados.classList.add('hidden');
            return;
        }

        resultados.forEach(paciente => {
            const item = document.createElement('li');
            item.className = 'px-4 py-2 hover:bg-purple-100 cursor-pointer';
            item.textContent = `${paciente.nombre} – Tlf: ${paciente.telefono}`;
            item.addEventListener('click', () => {
                buscador.value = paciente.nombre;
                listaResultados.classList.add('hidden');
                mostrarHistorial(paciente.id_usuario); // Cargar citas
            });
            listaResultados.appendChild(item);
        });

        listaResultados.classList.remove('hidden');
    });

    // Ocultar la lista si haces clic fuera
    document.addEventListener('click', function(e) {
        if (!buscador.contains(e.target) && !listaResultados.contains(e.target)) {
            listaResultados.classList.add('hidden');
        }
    });



// Función para mostrar el historial de un paciente
function mostrarHistorial(id) {
    document.getElementById('historialCitas').classList.remove('hidden');
    document.getElementById('listaCitas').innerHTML = '<p class="text-gray-500">Cargando citas...</p>';

    fetch(`obtener_citas_paciente.php?id=${id}`)
        .then(res => {
            if (!res.ok) throw new Error("Respuesta no válida del servidor");
            return res.json();
        })
        .then(data => {
            console.log('Citas recibidas:', data);

            const container = document.getElementById('listaCitas');
            container.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<p class="text-gray-600">Este paciente no tiene citas pasadas.</p>';
                return;
            }

            // Mostrar las citas en cuadrícula
            data.forEach(cita => {
                console.log('Procesando cita:', cita); 

                const citaDiv = document.createElement('div');
                citaDiv.className = 'bg-white border border-gray-300 rounded-lg p-4 shadow-lg hover:shadow-xl transition duration-300';

                citaDiv.innerHTML = `
                    <p><strong>Fecha:</strong> ${cita.fecha}</p>
                    <p><strong>Hora:</strong> ${cita.hora}</p>
                    <p><strong>Estado:</strong> ${cita.estado}</p>
                    <button onclick="verComentario(${cita.agenda_id})"
                            class="mt-2 text-sm text-purple-600 hover:underline">
                        Ver comentario
                    </button>
                    <div id="comentario-${cita.agenda_id}" class="mt-2 text-gray-700 hidden"></div>
                `;
                container.appendChild(citaDiv);
            });
        })
        .catch(error => {
            console.error('Error al cargar citas:', error);
            document.getElementById('listaCitas').innerHTML = '<p class="text-red-600">Error al cargar las citas.</p>';
        });
}

// Función para ver el comentario de una cita
function verComentario(id) {
    const comentarioDiv = document.getElementById(`comentario-${id}`);
    const boton = document.querySelector(`button[onclick="verComentario(${id})"]`);

    if (!comentarioDiv.classList.contains('hidden')) {
        comentarioDiv.classList.add('hidden');
        if (boton) boton.textContent = 'Ver comentario';
        return;
    }

    fetch(`obtener_comentario_cita.php?id=${id}`)
    .then(res => res.json())
    .then(data => {
        const div = document.getElementById(`comentario-${id}`);
        div.innerHTML = ''; // Limpiar contenido

        if (!Array.isArray(data.comentarios) || data.comentarios.length === 0) {
            div.innerHTML = '<p>Sin comentarios.</p>';
        } else {
            data.comentarios.forEach(c => {
                const p = document.createElement('p');
                p.innerHTML = `<span class="text-sm text-gray-500">${c.fecha_comentario}:</span> ${c.comentario}`;
                div.appendChild(p);
            });
        }

        div.classList.remove('hidden');
        const boton = document.querySelector(`button[onclick="verComentario(${id})"]`);
        if (boton) boton.textContent = 'Ocultar comentario';
    });

}
</script>

<?php include('../../../../includes/footer.php'); ?>
