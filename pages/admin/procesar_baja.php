<?php
/*
* Desactiva la cuenta de un usuario, envía correo de notificación,
* marca la solicitud de baja como procesada y registra la acción en el log.
*/
session_start();
include('../../includes/db.php');
include('../../includes/logger.php');
include('../../includes/enviar_correo.php');


// Verificar que se haya enviado el formulario con el ID del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];

    // Actualizar la tabla usuarios para desactivar la cuenta
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);

    // Obtener datos del usuario para enviar correo de notificación
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el usuario existe, enviar correo informativo
    if ($usuario) {
        $asunto = 'Cuenta Desactivada - Clínica de Fisioterapia';
        $mensaje = "
            <h2>Hola {$usuario['nombre']},</h2>
            <p>Hemos procesado tu solicitud de baja y tu cuenta ha sido <strong>desactivada</strong>.</p>
            <p>Si deseas volver a activarla o tienes cualquier consulta, no dudes en contactarnos.</p>
            <p>Gracias por haber confiado en nosotros.</p>
            <br>
            <p>Atentamente,</p>
            <p>El equipo de la Clínica de Fisioterapia</p>
        ";

        enviarCorreoGeneral($usuario['email'], $usuario['nombre'], $asunto, $mensaje);
    }


    // Actualizar la solicitud de baja como procesada
    $stmt = $pdo->prepare("UPDATE solicitudes_baja SET estado = 'procesada' WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);

    // Registrar la acción en el log de actividad
    registrarLog($pdo, $_SESSION['user_id'], 'Desactiva_usuario', "Se desactivó la cuenta del usuario $usuario_id");

    // Redirigir a la página de solicitudes de baja
    header("Location: ver_solicitudes_baja.php");
    exit();
}
?>
