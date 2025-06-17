<?php
/**
 * Procesa el registro de nuevos pacientes.
 * Verifica edad mínima, evita correos duplicados, registra al usuario,
 * envía un correo de bienvenida y guarda el log del registro.
 */
include('db.php');
include('enviar_correo.php');
include('logger.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = "paciente";

    $fecha_actual = new DateTime();
    $fecha_nacimiento_dt = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);

    if (!$fecha_nacimiento_dt) {
        echo json_encode(['status' => 'error', 'mensaje' => 'El formato de fecha no es válido.']);
        exit;
    }

    $edad = $fecha_actual->diff($fecha_nacimiento_dt)->y;
    if ($edad < 12) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Debes tener al menos 12 años para registrarte.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Este correo ya está registrado.']);
        exit;
    }

    $sql = "INSERT INTO usuarios (nombre, email, telefono, fecha_nacimiento, password, rol) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nombre, $email, $telefono, $fecha_nacimiento, $password, $rol])) {

        $nuevoUsuarioId = $pdo->lastInsertId();

        //REGISTRO DEL LOG
        registrarLog($pdo, $nuevoUsuarioId, "Registro", "El usuario $nombre se registró correctamente.");

        // Enviar correo de bienvenida
        $asunto = "¡Bienvenido a la Clínica de Fisioterapia!";
        $mensajeHtml = "
            <h3>Hola $nombre,</h3>
            <p>¡Gracias por registrarte en nuestra Clínica de Fisioterapia!</p>
            <p>Ya puedes iniciar sesión y reservar tus citas cómodamente desde nuestra plataforma.</p>
            <p>Si tienes cualquier duda, no dudes en contactarnos.</p>
            <br>
            <p>Atentamente,<br>El equipo de la Clínica de Fisioterapia PM</p>
        ";

        enviarCorreoGeneral($email, $nombre, $asunto, $mensajeHtml);

        echo json_encode(['status' => 'ok', 'mensaje' => '¡Registro completado con éxito! Ya puedes iniciar sesión.']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error al registrar el usuario. Intenta de nuevo.']);
    }
    exit;
}
?>
