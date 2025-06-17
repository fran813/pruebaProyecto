<?php 
/**
 * gestión_admin.php
 * Página para que los administradores gestionen otros administradores.
 * - Crear nuevos admins
 * - Eliminar admins existentes (con restricciones)
 * - Ver lista de admins activos
 */
include('../../../includes/header.php'); 
include('../../../includes/db.php'); 
include('../../../includes/logger.php');
include('../../../includes/enviar_correo.php');

// Seguridad para verificar que puedes acceder si eres administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

// Variables para mensajes
$mensaje_exito = '';
$mensaje_error = '';

// Crear nuevo admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['crear_admin'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validaciones básicas
    if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() == 0) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo, fecha_creacion) VALUES (?, ?, ?, 'admin', 1, NOW())");
            $stmtInsert->execute([$nombre, $email, $hash]);

            // Obtener el ID del nuevo admin
            $nuevoAdminId = $pdo->lastInsertId();

            // Enviar correo al nuevo admin
            $asunto = "Bienvenido a la plataforma de administración";
            $mensajeHtml = "
                <h2>Hola $nombre,</h2>
                <p>Tu cuenta como administrador ha sido creada exitosamente.</p>
                <p>Podrás acceder al panel con este correo: <strong>$email</strong> y contraseña <strong>$password</strong>.</p>
                <p>Por motivos de seguridad, te recomendamos cambiar tu contraseña tras el primer inicio de sesión.</p>
                <br>
                <p>Un saludo,</p>
                <p><em>Equipo de la clínica</em></p>
            ";

            enviarCorreoGeneral($email, $nombre, $asunto, $mensajeHtml);

            //REgistrar en el log
            registrarLog($pdo, $_SESSION['user_id'], 'Gestión de Administradores', "Se creó un nuevo administrador con ID $nuevoAdminId");
            $mensaje_exito = "Administrador creado correctamente.";
        } else {
            $mensaje_error = "El correo ya está registrado.";
        }
    } else {
        $mensaje_error = "Datos inválidos o incompletos. La contraseña debe tener al menos 6 caracteres.";
    }
}

// Eliminar admin
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $idEliminar = (int)$_GET['eliminar'];

    if ($idEliminar !== $_SESSION['user_id']) {

        // Verificar si el admin a eliminar es super_admin
        $stmtCheck = $pdo->prepare("SELECT super_admin FROM usuarios WHERE id_usuario = ? AND rol = 'admin'");
        $stmtCheck->execute([$idEliminar]);
        $admin = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($admin && $admin['super_admin'] == 0) {

            // Borrar logs relacionados antes de borrar el admin
            $stmtDeleteLogs = $pdo->prepare("DELETE FROM logs_actividad WHERE usuario_id = ?");
            $stmtDeleteLogs->execute([$idEliminar]);

            // Ahora borrar al admin
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND rol = 'admin'");
            $stmt->execute([$idEliminar]);

            //Registramos eliminar admin en el log
            registrarLog($pdo, $_SESSION['user_id'], 'Gestión de Administradores', "Se eliminó un administrador con ID $idEliminar");
            header("Location: gestion_admin.php?mensaje=eliminado");
            exit;
        } else {
            $mensaje_error = "No puedes eliminar un super administrador.";
        }
    } else {
        $mensaje_error = "No puedes eliminar tu propio usuario.";
    }
}




// Obtener lista de administradores activos
$stmt = $pdo->prepare("SELECT id_usuario, nombre, email, fecha_creacion FROM usuarios WHERE rol = 'admin' AND activo = 1 ORDER BY nombre ASC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="flex-grow max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">

    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'eliminado'): ?>
        <div id="mensajeComentario" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 text-center">
            Administrador eliminado correctamente.
        </div>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <div id="mensajeComentario" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
            <?= htmlspecialchars($mensaje_exito) ?>
        </div>
    <?php elseif ($mensaje_error): ?>
        <div id="mensajeComentario" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-center">
            <?= htmlspecialchars($mensaje_error) ?>
        </div>
    <?php endif; ?>

    <h2 class="text-2xl font-semibold text-purple-700 mb-6">Gestión de Administradores</h2>

    <form method="POST" class="mb-6 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-4">Crear nuevo administrador</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="nombre" placeholder="Nombre" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" required>
            <input type="email" name="email" placeholder="Email" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" required>
            <input type="password" name="password" placeholder="Contraseña (mín 6 caracteres)" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" required>
        </div>
        <button name="crear_admin" class="mt-4 bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-800">Crear</button>
    </form>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-purple-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Fecha Registro</th>
                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($admin['nombre']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($admin['email']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?= date("d/m/Y", strtotime($admin['fecha_creacion'])) ?></td>
                        <td class="px-6 py-4 text-center text-sm">
                            <?php if ($admin['id_usuario'] !== $_SESSION['user_id']): ?>
                                <a href="?eliminar=<?= $admin['id_usuario'] ?>" class="inline-block bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                                   onclick="return confirm('¿Seguro que quieres eliminar este administrador?')">Eliminar</a>
                            <?php else: ?>
                                <span class="text-gray-500 italic">Tú mismo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (count($admins) === 0): ?>
            <p class="p-6 text-gray-500 text-center">No hay administradores registrados.</p>
        <?php endif; ?>
    </div>

    <a href="../admin_dashboard.php" class="inline-block mt-6 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
        ← Volver al inicio
    </a>
</main>

<script>
    // Ocultar mensaje luego de 3 segundos
    setTimeout(() => {
        const mensaje = document.getElementById('mensajeComentario');
        if (mensaje) mensaje.classList.add('hidden');
    }, 3000);
</script>

<?php include('../../../includes/footer.php'); ?>
