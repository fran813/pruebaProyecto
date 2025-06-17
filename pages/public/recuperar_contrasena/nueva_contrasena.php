<?php
// Restablece la contraseña tras verificar token válido y no usado.
// Actualiza la contraseña y marca el token como usado, mostrando mensajes según resultado.
include('../../../includes/db.php');
include('../../../includes/header.php');


$token = $_GET['token'] ?? '';    // Obtener token de la URL (si no existe, cadena vacía)
$mensaje = '';                    // Variable para mensajes a mostrar al usuario
$tipoMensaje = '';                // Tipo de mensaje: 'success' o 'error'

$formularioActivo = true;

// Si no hay token, mostrar error y ocultar formulario
if (empty($token)) {
    $mensaje = 'Token no válido.';
    $tipoMensaje = 'error';
    $formularioActivo = false;
} else {
    // Buscar token válido y no usado en la tabla 'recuperaciones'
    $stmt = $pdo->prepare("SELECT * FROM recuperaciones WHERE token = ? AND usado = 0");
    $stmt->execute([$token]);
    $recuperacion = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si token no válido o ya usado, mostrar error y ocultar formulario
    if (!$recuperacion) {
        $mensaje = 'El enlace ya ha sido usado o no es válido.';
        $tipoMensaje = 'error';
        $formularioActivo = false;
    // Si se envió el formulario con POST y están los campos de contraseña
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirmar'])) {
        $password = $_POST['password'];
        $confirmar = $_POST['confirmar'];

        // Validar que las contraseñas coincidan
        if ($password !== $confirmar) {
            $mensaje = 'Las contraseñas no coinciden.';
            $tipoMensaje = 'error';
        } else {
            // Hashear la nueva contraseña
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Actualizar la contraseña del usuario en la BD
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            $stmt->execute([$hash, $recuperacion['email']]);

            // Marcar el token como usado para evitar reutilización
            $stmt = $pdo->prepare("UPDATE recuperaciones SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);

            // Obtener ID de usuario para registrar el log
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
            $stmt->execute([$recuperacion['email']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                include_once('../../../includes/logger.php');
                // Registrar en el log que el usuario cambió su contraseña
                registrarLog($pdo, $usuario['id_usuario'], 'Cambio_password', 'El usuario cambió su contraseña desde el enlace de recuperación.');
            }

             // Mensaje éxito y desactivar formulario
            $mensaje = 'Tu contraseña ha sido actualizada correctamente.';
            $tipoMensaje = 'success';
            $formularioActivo = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
    <main class="flex-grow flex items-center justify-center p-8">
        <div class="bg-white p-8 rounded-xl shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center text-purple-700 mb-6">Restablecer Contraseña</h2>

            <?php if ($mensaje): ?>
                <div class="mb-4 px-4 py-3 rounded text-center <?= $tipoMensaje === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' ?>">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if ($formularioActivo): ?>
                <form action="" method="POST" class="space-y-4">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div>
                        <label class="block text-gray-700">Nueva Contraseña</label>
                        <input type="password" name="password" required class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700">Confirmar Contraseña</label>
                        <input type="password" name="confirmar" required class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
                        Cambiar Contraseña
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center mt-6">
                    <a href="/pages/public/login.php" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-800 transition">
                        Volver al inicio de sesión
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
