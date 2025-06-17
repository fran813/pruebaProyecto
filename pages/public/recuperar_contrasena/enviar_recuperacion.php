<?php
// Archivo para procesar la solicitud de recuperación de contraseña.
// Recibe el email del usuario, verifica si existe en la base de datos,
// genera un token seguro y lo guarda con fecha de expiración.
// Luego envía un correo con un enlace para restablecer la contraseña.
include('../../../includes/db.php');
require_once('../../../includes/enviar_correo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Buscar si el correo existe en la base de datos
    $stmt = $pdo->prepare("SELECT id_usuario, nombre FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Genera un token seguro para la recuperación
        $token = bin2hex(random_bytes(32));
        // Establece expiración del token a 1 hora desde ahora
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guarda el token junto con usuario, email y expiración en la tabla recuperaciones
        $stmtInsert = $pdo->prepare("INSERT INTO recuperaciones (usuario_id, token,email, expiracion) VALUES (:usuario_id, :token, :email, :expiracion)");
        $stmtInsert->execute([
            'usuario_id' => $usuario['id_usuario'],
            'token' => $token,
            'email' => $email,
            'expiracion' => $expiracion
        ]);

        // Construye el enlace para restablecer la contraseña con el token como parámetro
        $enlace = "http://localhost/reservas_proyecto/pages/public/recuperar_contrasena/nueva_contrasena.php?token=$token";

        // Asunto y cuerpo del correo en HTML
        $asunto = "Recuperación de contraseña";
        $mensajeHtml = "
            <p>Hola {$usuario['nombre']},</p>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
            <p><a href='$enlace'>$enlace</a></p>
            <p>Este enlace es válido por 1 hora.</p>
        ";

        // Envía el correo y redirige según resultado
        if (enviarCorreoGeneral($email, $usuario['nombre'], $asunto, $mensajeHtml)) {
            header("Location: recuperar_contrasena.php?estado=enviado");
            exit;
        } else {
            header("Location: recuperar_contrasena.php?estado=error1");
            exit;
        }
        } else {
            header("Location: recuperar_contrasena.php?estado=error2");
            exit;
        }
}
?>
