<?php
/**
 * Reactiva un paciente desactivado:
 * recibe el ID por POST, actualiza su estado a activo,
 * envía correo de notificación y registra la acción en el log.
 */
session_start();
include('../../../includes/db.php');
include('../../../includes/logger.php');
include('../../../includes/enviar_correo.php');

// Verifica que la solicitud es POST y que se recibió el ID del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id = $_POST['id_usuario'];

    // Obtener el nombre y correo del paciente antes de reactivarlo
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        // Preparar contenido del correo de reactivación
        $asunto = 'Cuenta Activada - Clínica de Fisioterapia';
        $mensaje = "
            <h2>Hola {$paciente['nombre']},</h2>
            <p>Hemos procedido a la activación tu cuenta.</p>
            <p>Si tienes cualquier consulta, no dudes en contactarnos.</p>
            <p>Gracias por haber confiado en nosotros.</p>
            <br>
            <p>Atentamente,</p>
            <p>El equipo de la Clínica de Fisioterapia PM</p>
        ";

        // Enviar correo al paciente
        enviarCorreoGeneral($paciente['email'], $paciente['nombre'], $asunto, $mensaje);
    }

    // Actualizar estado del paciente en la base de datos (activar cuenta)
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = ?");
    if ($stmt->execute([$id])) {

        // Registrar en el log que un admin reactivó a un paciente
        $admin_id = $_SESSION['user_id'];
        $tipo_accion = "Reactivación de paciente";
        $accion = "El administrador reactivó al paciente con (ID: $id)";
        registrarLog($pdo, $admin_id, $tipo_accion, $accion);
    

        // Redirigir con mensaje de éxito
        header("Location: pacientes_desactivados.php?mensaje=reactivado");
        exit;
    } else {
        // Redirigir si falla la actualización
        header("Location: pacientes_desactivados.php?error=bd");
        exit;
    }
} else {
    // Redirigir si faltan parámetros o método incorrecto
    header("Location: pacientes_desactivados.php?error=parametros");
    exit;
}
