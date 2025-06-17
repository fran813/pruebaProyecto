<?php
/*
* Desactiva un fisioterapeuta y envía un correo de notificación al usuario.
* También registra la acción en el log y redirige según el resultado.
*/
session_start();
include('../../../includes/db.php');
include('../../../includes/logger.php');
include('../../../includes/enviar_correo.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id = $_POST['id_usuario'];

    // Obtener el nombre del fisio antes de desactivarlo
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $fisio = $stmt->fetch(PDO::FETCH_ASSOC);

    //Si existe fisio se enviar un correo informando sobre la cuenta desactivada
    if ($fisio) {
        $asunto = 'Cuenta Desactivada - Clínica de Fisioterapia';
        $mensaje = "
            <h2>Hola {$fisio['nombre']},</h2>
            <p>Hemos procesado la baja de tu cuenta.</p>
            <p>Si deseas volver a activarla o tienes cualquier consulta, no dudes en contactarnos.</p>
            <p>Gracias por haber confiado en nosotros.</p>
            <br>
            <p>Atentamente,</p>
            <p>El equipo de la Clínica de Fisioterapia PM</p>
        ";

        enviarCorreoGeneral($fisio['email'], $fisio['nombre'], $asunto, $mensaje);
    }

    //Actualizamos la BD para desactivar el fisio
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = ?");
    if ($stmt->execute([$id])) {

        //Registro log de desactivar fisio
        $admin_id = $_SESSION['user_id'];
        $tipo_accion = "Desactivación de fisioterapeuta";
        $accion = "El administrador desactivó al fisioterapeuta con (ID: $id)";
        registrarLog($pdo, $admin_id, $tipo_accion, $accion);

        header("Location: gestion_fisioterapeutas.php?mensaje=desactivado");
        exit;
    } else {
        header("Location: gestion_fisioterapeutas.php?error=bd");
        exit;
    }
} else {
    header("Location: gestion_fisioterapeutas.php?error=parametros");
    exit;
}
