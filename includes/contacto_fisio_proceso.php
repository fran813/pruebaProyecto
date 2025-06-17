<?php
/**
 * contacto_fisio_proceso.php
 *
 * Permite al paciente enviar un mensaje a su fisioterapeuta.
 * - Recupera los datos del paciente y del fisio.
 * - Envía un correo al fisio con el mensaje del paciente.
 * - Registra la acción en la tabla de logs.
 * - Muestra un mensaje de confirmación al paciente.
 */
session_start();
include('/includes/db.php');
require_once('/includes/enviar_correo.php');
require_once('/includes/logger.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener ID del paciente desde la sesión
    $paciente_id = $_SESSION['user_id'];
    // Recoger ID del fisioterapeuta y mensaje desde el formulario
    $fisio_id = $_POST['fisio_id'] ?? null;
    $mensaje = trim($_POST['mensaje']);

    // Validar que se ha enviado un fisioterapeuta y un mensaje
    if (!$fisio_id || empty($mensaje)) {
        $_SESSION['mensaje'] = "Debes rellenar todos los campos antes de enviar el mensaje.";
        header("Location: /paciente/contacto_fisio.php");
        exit;
    }

    // Obtener datos del fisioterapeuta (email y nombre)
    $stmt = $pdo->prepare("SELECT email, nombre FROM usuarios WHERE id_usuario = ? AND activo = 1 ");
    $stmt->execute([$fisio_id]);
    $fisio = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener nombre del paciente para personalizar el mensaje
    $stmt2 = $pdo->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ? AND activo = 1");
    $stmt2->execute([$paciente_id]);
    $paciente_nombre = $stmt2->fetchColumn();

    // Si ambos usuarios existen, se construye el correo y se envía
    if ($fisio && $paciente_nombre) {
        $asunto = "Nuevo mensaje de paciente: $paciente_nombre";
        $contenido = "
            <h3>Has recibido un nuevo mensaje de tu paciente <strong>$paciente_nombre</strong>.</h3>
            <p><strong>Mensaje:</strong></p>
            <p>$mensaje</p>
        ";

        // Enviar correo
        enviarCorreoGeneral($fisio['email'], $fisio['nombre'], $asunto, $contenido);

        // Registrar log
        registrarLog($pdo, $paciente_id, 'Contacto_paciente', "Mensaje enviado al fisioterapeuta ID $fisio_id");


        $_SESSION['mensaje'] = "Mensaje enviado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al enviar el mensaje.";
    }
    // Redirigir de vuelta a la página de contacto con un mensaje de estado
    header("Location: /pages/usuario/paciente/contacto/contacto_fisio.php");
    exit;
}
?>
