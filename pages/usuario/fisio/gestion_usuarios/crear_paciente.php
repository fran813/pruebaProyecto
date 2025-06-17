<?php
include('../../../../includes/logger.php');

$errores = [];
$mensaje_exito = "";

//Comprueba si el formulario se envia por metodo POst
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $password = $_POST['password'];

    // Validaciones
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Email no válido.";
    if (empty($telefono)) $errores[] = "El teléfono es obligatorio.";
    if (empty($fecha_nacimiento)) $errores[] = "La fecha de nacimiento es obligatoria.";
    if (strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";

    // Verificar si email ya existe
    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errores[] = "Ya existe un usuario con ese correo.";
        }
    }

    // Insertar en la base de datos
    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $rol = "paciente";

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, fecha_nacimiento, password, rol) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $telefono, $fecha_nacimiento, $hash, $rol]);


        // ENVIAR CORREO DE BIENVENIDA
        $asunto = "¡Bienvenido a la clínica!";
        $mensajeHtml = "
            <h2>Hola, $nombre</h2>
            <p>Tu cuenta ha sido creada exitosamente por nuestro equipo de fisioterapia.</p>
            <p>Puedes iniciar sesión en nuestra plataforma con el correo <strong>$email</strong>.</p>
            <p>Tu contraseña temporal es: <strong>$password</strong></p>
            <p>Te recomendamos cambiarla en tu perfil después de iniciar sesión.</p>
            <br>
            <p>Gracias por confiar en nosotros.</p>
        ";

        enviarCorreoGeneral($email, $nombre, $asunto, $mensajeHtml);
        $mensaje_exito = "Paciente registrado correctamente.";

        // Registrar en logs 
        $id_creador = $_SESSION['user_id'];
        $id_nuevo_paciente = $pdo->lastInsertId();
        $descripcion_log = "Creó un nuevo paciente con ID $id_nuevo_paciente, nombre $nombre y correo $email.";
        registrarLog($pdo, $id_creador, 'Creación de Paciente', $descripcion_log);
    

        // Limpiar campos
        $nombre = $email = $telefono = $fecha_nacimiento = "";
    }
}
?>

<form method="POST" class="bg-white p-6 rounded shadow max-w-lg mx-auto">
    <h2 class="text-2xl font-semibold text-center text-purple-700 mb-4">Crear Nuevo Paciente</h2>

    <label class="block mb-2 font-medium" for="nombre">Nombre Completo:</label>
    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" required
        class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" />

    <label class="block mb-2 font-medium" for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required
        class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" />

    <label class="block mb-2 font-medium" for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($telefono ?? '') ?>" required
        class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" />

    <label class="block mb-2 font-medium" for="fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>" required
        class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" />

    <label class="block mb-2 font-medium" for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required
        class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" />

    <button type="submit" class="w-full mb-4 bg-purple-500 text-white py-2 rounded-lg shadow-md hover:bg-purple-700 transition duration-300">
        Crear Paciente
    </button>

     <!-- Mensajes de error -->
    <?php if (!empty($errores)): ?>
        <div class="bg-red-100 border border-red-400 text-center text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errores as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje de éxito -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="bg-green-100 border border-green-400 text-center text-green-700 px-4 py-3 rounded mb-4">
            <p><?= htmlspecialchars($mensaje_exito) ?></p>
        </div>
    <?php endif; ?>
</form>
