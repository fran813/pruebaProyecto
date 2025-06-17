<?php
/**
 * Reactiva un fisioterapeuta desactivado.
 * - Recibe por POST el id_usuario del fisioterapeuta.
 * - Envía un correo notificando la reactivación.
 * - Actualiza el campo 'activo' en la base de datos.
 * - Registra la acción en el log de actividad.
 * - Redirige con mensaje de éxito o error.
 */
session_start();
include('../../../includes/db.php');
include('../../../includes/logger.php');
include('../../../includes/enviar_correo.php');

// Verificar que se envió el ID del fisioterapeuta por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id = $_POST['id_usuario'];

    // Obtener el nombre del fisio antes de reactivarlo
    $stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $fisio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fisio) {
        // Preparar correo de notificación de reactivación
        $asunto = 'Cuenta Activada - Clínica de Fisioterapia';
        $mensaje = "
            <h2>Hola {$fisio['nombre']},</h2>
            <p>Hemos procedido a la activación tu cuenta.</p>
            <p>Si tienes cualquier consulta, no dudes en contactarnos.</p>
            <p>Gracias por haber confiado en nosotros.</p>
            <br>
            <p>Atentamente,</p>
            <p>El equipo de la Clínica de Fisioterapia PM</p>
        ";

        enviarCorreoGeneral($fisio['email'], $fisio['nombre'], $asunto, $mensaje);
    }

    // Actualizar campo activo a 1 para reactivar al fisioterapeuta
    $stmt = $pdo->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = ?");
    if ($stmt->execute([$id])) {

        // Registrar acción en log de administración
        $admin_id = $_SESSION['user_id'];
        $tipo_accion = "Reactivación de fisioterapeuta";
        $accion = "El administrador reactivó al fisio con (ID: $id)";
        registrarLog($pdo, $admin_id, $tipo_accion, $accion);


        // Redirigir con mensaje de éxito
        header("Location: fisioterapeutas_desactivados.php?mensaje=reactivado");
        exit;
    } else {
        // Redirigir con error de BD
        header("Location: fisioterapeutas_desactivados.php?error=bd");
        exit;
    }
} else {
    // Redirigir si no se envió el parámetro correcto
    header("Location: fisioterapeutas_desactivados.php?error=parametros");
    exit;
}
