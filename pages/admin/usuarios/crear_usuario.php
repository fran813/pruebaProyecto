<?php
/**
 * crear_usuario.php
 * 
 * Página del panel de administrador que permite registrar nuevos usuarios en el sistema,
 * ya sea pacientes o fisioterapeutas. Valida los datos enviados desde un formulario,
 * guarda al nuevo usuario en la base de datos con la contraseña encriptada,
 * envía un correo de bienvenida y registra la acción en el log de actividad.
 
 */
session_start();
include('../../../includes/header.php');
include('../../../includes/db.php');
include('../../../includes/enviar_correo.php');
include('../../../includes/logger.php');

// Variables para mostrar mensajes en la interfaz
$mensaje = "";
$error = "";

// Verifica si el formulario fue enviado por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /// Obtiene y limpia los datos enviados desde el formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $rol = isset($_POST['rol']) ? $_POST['rol'] : '';

    // Verifica que todos los campos estén completos
    if (empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($rol)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Encripta la contraseña antes de guardarla
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Inserta el nuevo usuario en la base de datos
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol, fecha_creacion) 
                                   VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nombre, $email, $telefono, $hashedPassword, $rol]);

            // Prepara y envía un correo de bienvenida al nuevo usuario
            $asunto = "¡Bienvenido a la clínica!";
            $mensajeHtml = "
                <h3>Hola $nombre,</h3>
                <p>Tu cuenta ha sido creada correctamente en nuestro sistema como <strong>$rol</strong>.</p>
                <p>Ahora puedes iniciar sesión con tu correo electrónico: <strong>$email</strong>.</p>
                <p>Tu contraseña es: <strong>$password</strong>, le recomendamos cambiarla para más seguridad.
                <p>¡Gracias por confiar en nosotros!</p>
                <br>
                <p>Saludos,<br>Clínica de Fisioterapia</p>
            ";
            enviarCorreoGeneral($email, $nombre, $asunto, $mensajeHtml);

            // Registra la acción en el log de actividad
            $admin_id = $_SESSION['user_id'];
            $tipo_accion = "Creación de usuario";
            $nuevo_usuario_id = $pdo->lastInsertId();
            $accion = "El administrador creó un nuevo usuario con ID ($nuevo_usuario_id)";
            registrarLog($pdo, $admin_id, $tipo_accion, $accion);
        
            // Mensaje de éxito para mostrar en la interfaz
            $mensaje = "Usuario creado correctamente.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "El correo electrónico ya está registrado. Por favor, usa otro.";
            } else {
                $error = "Error al crear usuario: " . $e->getMessage();
            }
        }

    }
}
?>

<main class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold text-purple-700 mb-6">Crear Nuevo Usuario</h2>

    <?php if ($mensaje): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded"><?= $mensaje ?></div>
    <?php elseif ($error): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?= $error ?></div>
    <?php endif; ?>

    <form action="crear_usuario.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
            <input type="text" name="nombre" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
            <input type="email" name="email" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="text" name="telefono" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" name="password" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Rol</label>
            <select name="rol" class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                <option value="">Selecciona un rol</option>
                <option value="paciente">Paciente</option>
                <option value="fisioterapeuta">Fisioterapeuta</option>
            </select>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:ring-purple-500 focus:border-purple-500 outline-none">
                Crear Usuario
            </button>
        </div>
    </form>
</main>
<?php include('../../../includes/footer.php'); ?>
