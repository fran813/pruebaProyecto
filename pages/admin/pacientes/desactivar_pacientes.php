<?php
/**
 * Desactiva un paciente, envía correo de notificación y registra la acción en el log.
 * Recibe el ID por POST y redirige con mensaje según resultado.
 */
session_start();
include('../../../includes/db.php');
include('../../../includes/logger.php');
include('../../../includes/enviar_correo.php');

// Comprobar que la petición es POST y que se recibió el id_usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id = $_POST['id_usuario'];

    // Obtener el nombre del paciente antes de desactivarlo
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        // Preparar correo notificando la desactivación de la cuenta
        $asunto = 'Cuenta Desactivada - Clínica de Fisioterapia';
        $mensaje = "
            <h2>Hola {$paciente['nombre']},</h2>
            <p>Hemos procesado la baja de tu cuenta.</p>
            <p>Si deseas volver a activarla o tienes cualquier consulta, no dudes en contactarnos.</p>
            <p>Gracias por haber confiado en nosotros.</p>
            <br>
            <p>Atentamente,</p>
            <p>El equipo de la Clínica de Fisioterapia PM</p>
        ";

        enviarCorreoGeneral($paciente['email'], $paciente['nombre'], $asunto, $mensaje);
    }


    // Desactivar el paciente en la base de datos (activo = 0)
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = ?");
    if ($stmt->execute([$id])) {

        //Registro la accion en el log
        $admin_id = $_SESSION['user_id'];
        $tipo_accion = "Desactivación de paciente";
        $accion = "El administrador desactivó al paciente con (ID: $id)";
        registrarLog($pdo, $admin_id, $tipo_accion, $accion);
    
        // Redirigir con mensaje de éxito
        header("Location: gestion_pacientes.php?mensaje=desactivado");
        exit;
    } else {
        header("Location: gestion_pacientes.php?error=bd");
        exit;
    }
} else {
    // Redirigir si no se enviaron parámetros correctos
    header("Location: gestion_pacientes.php?error=parametros");
    exit;
}
