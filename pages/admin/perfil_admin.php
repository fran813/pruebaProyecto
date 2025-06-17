<?php
/*
* perfil_admin.php
* Esta página permite al administrador ver y editar su nombre, correo 
* electrónico y contraseña. Incluye validaciones básicas y registro de 
* actividad en logs. El acceso está restringido a usuarios con rol "admin".
*/
session_start();
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/logger.php');

// Verifica que el usuario tenga el rol de admin, si no lo tiene, lo redirige al login
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../pages/public/login.php");
    exit();
}

// Variables para mostrar mensajes al usuario
$mensaje_exito = '';
$mensaje_error = '';

// Obtiene los datos actuales del administrador desde la base de datos
$stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ? AND rol = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Si se ha enviado el formulario (por método POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log("POST recibido");
error_log(print_r($_POST, true));

    if (isset($_POST['guardar_nombre'])) {
        $nombre = trim($_POST['nombre']);
        if (empty($nombre)) {
            $mensaje_error = "El nombre no puede estar vacío.";
        } else {
            // Actualiza el nombre en la base de datos
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nombre = ? WHERE id_usuario = ? AND rol = 'admin'");
            $stmtUpdate->execute([$nombre, $_SESSION['user_id']]);
            registrarLog($pdo, $_SESSION['user_id'], 'Perfil Admin', "Actualizó su nombre.");
            $mensaje_exito = "Nombre actualizado correctamente.";
            $admin['nombre'] = $nombre;
        }
        // Actualizar correo electrónico
    } elseif (isset($_POST['guardar_email'])) {
        $email = trim($_POST['email']);
        if (empty($email)) {
            $mensaje_error = "El correo no puede estar vacío.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje_error = "El correo no es válido.";
        } else {
            // Verifica que el correo no esté siendo usado por otro usuario
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id_usuario != ?");
            $stmtCheck->execute([$email, $_SESSION['user_id']]);
            if ($stmtCheck->fetchColumn() > 0) {
                $mensaje_error = "El correo ya está en uso por otro usuario.";
            } else {
                // Actualiza el correo en la base de datos
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET email = ? WHERE id_usuario = ? AND rol = 'admin'");
                $stmtUpdate->execute([$email, $_SESSION['user_id']]);
                registrarLog($pdo, $_SESSION['user_id'], 'Perfil Admin', "Actualizó su correo electrónico.");
                $mensaje_exito = "Correo electrónico actualizado correctamente.";
                $admin['email'] = $email;
            }
        }
    } 
    // Cambiar contraseña
    elseif (isset($_POST['guardar_password'])) {
        // Obtiene las contraseñas del formulario
        $password_actual = $_POST['password_actual'] ?? '';
        $nueva_password = $_POST['nueva_password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';

        // Obtener la contraseña actual en la base de datos
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Valida la contraseña actual
        if (!$usuario || !password_verify($password_actual, $usuario['password'])) {
            $mensaje_error_pass = "La contraseña actual es incorrecta.";
        } elseif (strlen($nueva_password) < 6) {
            $mensaje_error_pass = "La nueva contraseña debe tener al menos 6 caracteres.";
        } elseif ($nueva_password !== $confirmar_password) {
            $mensaje_error_pass = "Las nuevas contraseñas no coinciden.";
        } else {
            // Si todo es válido, actualiza la contraseña
            $hash_nuevo = password_hash($nueva_password, PASSWORD_BCRYPT);
            $stmtUpdate = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
            $stmtUpdate->execute([$hash_nuevo, $_SESSION['user_id']]);

            registrarLog($pdo, $_SESSION['user_id'], 'Perfil Admin', "Cambió su contraseña.");
            $mensaje_exito_pass = "Contraseña actualizada correctamente.";
        }
    }
}
?>

<main class="flex-grow max-w-4xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-semibold text-center text-purple-700 mb-6">Mi Perfil</h2>

    <?php if ($mensaje_exito): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center">
            <?= htmlspecialchars($mensaje_exito) ?>
        </div>
    <?php elseif ($mensaje_error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-center">
            <?= htmlspecialchars($mensaje_error) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para cambiar nombre -->
    <form method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto mb-8">
        <label class="block mb-2 font-semibold" for="nombre"><strong>Nombre</strong>: <?= htmlspecialchars($admin['nombre']) ?></label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500"
            required
        >
        <button
            type="submit"
            name="guardar_nombre"
            class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-800 transition-colors"
        >
            Guardar nombre
        </button>
    </form>

    <!-- Formulario para cambiar email -->
    <form method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto mb-8">
        <label class="block mb-2 font-semibold" for="email"><strong>Correo electrónico</strong>: <?= htmlspecialchars($admin['email']) ?></label>
        <input
            type="email"
            name="email"
            id="email"
            value=""
            class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500"
            required
        >
        <button
            type="submit"
            name="guardar_email"
            class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-800 transition-colors"
        >
            Guardar correo
        </button>
    </form>

    <!-- Formulario para cambiar contraseña -->
    <h3 class="text-xl font-semibold mt-10 text-purple-700 text-center mb-4">Cambiar contraseña</h3>

    <?php if (!empty($mensaje_exito_pass)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
            <?= htmlspecialchars($mensaje_exito_pass) ?>
        </div>
    <?php elseif (!empty($mensaje_error_pass)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
            <?= htmlspecialchars($mensaje_error_pass) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
        <label class="block mb-2 font-semibold" for="password_actual">Contraseña actual:</label>
        <input type="password" name="password_actual" id="password_actual" required
            class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">

        <label class="block mb-2 font-semibold" for="nueva_password">Nueva contraseña:</label>
        <input type="password" name="nueva_password" id="nueva_password" required
            class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">

        <label class="block mb-2 font-semibold" for="confirmar_password">Confirmar nueva contraseña:</label>
        <input type="password" name="confirmar_password" id="confirmar_password" required
            class="w-full p-2 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">

        <button type="submit" name="guardar_password"
                class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-800 transition-colors">
            Cambiar contraseña
        </button>
    </form>


    <a href="admin_dashboard.php" class="inline-block mt-6 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
        ← Volver al inicio
    </a>
</main>

<?php include('../../includes/footer.php'); ?>
