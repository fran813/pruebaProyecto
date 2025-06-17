<?php 
/**
 *  registro.php
 * Página para que nuevos usuarios se registren en el sistema.
 * Incluye la conexión a la base de datos y el header común.
 * Contiene un formulario que envía los datos vía fetch a registro_proceso.php,
 * mostrando mensajes de éxito o error sin recargar la página.
 * Usa Tailwind CSS para el diseño y tiene enlaces para login y volver al inicio.
 */
include('../../includes/db.php'); 
include('../../includes/header.php'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="../src/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">


    <main class="flex-grow flex items-center justify-center p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center text-purple-700 mb-4">Registro de Usuario</h2>

            <form id="registroForm" action="../../includes/registro_proceso.php" method="POST" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-gray-700 font-medium">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label for="telefono" class="block text-gray-700 font-medium">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label for="fecha_nacimiento" class="block text-gray-700 font-medium">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-medium">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full bg-purple-500 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
                    Registrarse
                </button>
            </form>
            <br>
            <div id="mensajeRespuesta" class="hidden"></div>
            <p class="text-center text-gray-600 text-sm mt-4">
                ¿Ya tienes una cuenta? <a href="login.php" class="text-purple-600 hover:underline">Inicia sesión aquí</a>
            </p>
            <p class="text-center text-gray-600 text-sm mt-2">
                <a href="/index.php" class="text-purple-600 hover:underline">Volver al inicio</a>
            </p>
        </div>
    </main>

    <script>
        // Captura el evento de envío del formulario para evitar la recarga de página
        document.getElementById('registroForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Recoge todos los datos del formulario
            const formData = new FormData(this);

            fetch('../../includes/registro_proceso.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json()) // Convierte la respuesta en JSON
            .then(data => {
                const mensajeDiv = document.getElementById('mensajeRespuesta');
                mensajeDiv.innerHTML = '';
                mensajeDiv.classList.remove('hidden');

                if (data.status === 'ok') {
                    // Mensaje de exito en color verde
                    mensajeDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center';
                    mensajeDiv.innerText = data.mensaje;
                    // Redirige después de 3 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    // Mensaje de error
                    mensajeDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center';
                    mensajeDiv.innerText = data.mensaje;
                }
            })
            .catch(err => {
                const mensajeDiv = document.getElementById('mensajeRespuesta');
                mensajeDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center';
                mensajeDiv.innerText = 'Ocurrió un error al enviar el formulario.';
                mensajeDiv.classList.remove('hidden');
                console.error(err);
            });
        });
    </script>


    <?php include('../../includes/footer.php'); ?>
</body>
</html>
