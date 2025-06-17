<?php 
include('../../../includes/db.php'); 
include('../../../includes/header.php'); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

    <main class="flex-grow flex items-center justify-center p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center text-purple-700 mb-4">Recuperar Contraseña</h2>

            <p class="text-gray-600 text-sm text-center mb-4">
                Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.
            </p>

            <form action="enviar_recuperacion.php" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required
                        class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full bg-purple-500 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
                    Enviar Instrucciones
                </button>
            </form>
            <br>
            <?php if (isset($_GET['estado'])): ?>
                <?php if ($_GET['estado'] === 'enviado'): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
                        Te hemos enviado un enlace a tu correo para restablecer tu contraseña.
                    </div>
                <?php elseif ($_GET['estado'] === 'error1'): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
                        No se pudo enviar el correo. Intenta más tarde.
                    </div>
                <?php elseif ($_GET['estado'] === 'error2'): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
                        No se encontró ninguna cuenta con ese correo.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <p class="text-center text-gray-600 text-sm mt-4">
                <a href="../login.php" class="text-purple-600 hover:underline">Volver al inicio de sesión</a>
            </p>
        </div>
    </main>

    <?php include('../../../includes/footer.php'); ?>

</body>
</html>
