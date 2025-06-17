<?php 
/**
 * Página de inicio de sesión para los usuarios.
 * Incluye formulario de login, enlaces de recuperación/registro y muestra errores si los hay.
 */
include('../../includes/db.php'); 
include('../../includes/header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

    <main class="flex-grow flex items-center justify-center p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center text-purple-700 mb-4">Iniciar Sesión</h2>

            <form action="/includes/login_proceso.php" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-medium">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <div class="text-right">
                    <a href="recuperar_contrasena/recuperar_contrasena.php" class="text-sm text-purple-600 hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit"
                    class="w-full bg-purple-500 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
                    Iniciar Sesión
                </button>
            </form>
            <p class="text-center text-gray-600 text-sm mt-4">
                ¿No tienes cuenta? <a href="registro.php" class="text-purple-600 hover:underline">Regístrate aquí</a>
            </p>
            <p class="text-center text-gray-600 text-sm mt-2">
                <a href="/index.php" class="text-purple-600 hover:underline">Volver al inicio</a>
            </p>
            <br>
           <?php if (isset($_GET['error']) && $_GET['error'] === 'inactivo'): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
                    Este usuario está desactivado. Contacte con la clínica.
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'credenciales'): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
                    Usuario o contraseña incorrectos.
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include('../../includes/footer.php'); ?>

</body>
</html>
