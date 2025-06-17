<?php
session_start();
include('../../../../includes/db.php');
require_once('../../../../includes/enviar_correo.php');
require_once('../../../../includes/logger.php');

// Solo accesible si es fisioterapeuta
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'fisioterapeuta') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? null;
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!$paciente_id || empty($mensaje)) {
        $_SESSION['mensaje'] = "Debe seleccionar un paciente y escribir un mensaje.";
        header('Location: contactar.php');
        exit();
    }

    // Obtener el correo y nombre del paciente
    $stmt = $pdo->prepare("SELECT email, nombre FROM usuarios WHERE id_usuario = ? AND activo = 1");
    $stmt->execute([$paciente_id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        $_SESSION['mensaje'] = "Paciente no encontrado o inactivo.";
        header('Location: contactar.php');
        exit();
    }

    $email = $paciente['email'];
    $nombre = $paciente['nombre'];
    
    // Obtener nombre del fisioterapeuta que envía el mensaje
    $stmtFisio = $pdo->prepare("SELECT nombre FROM usuarios WHERE id_usuario = ? AND activo = 1");
    $stmtFisio->execute([$_SESSION['user_id']]);
    $nombreFisio = $stmtFisio->fetchColumn() ?: 'Fisioterapeuta';

    $asunto = "Mensaje desde la Clínica - $nombreFisio";
    $mensajeHtml = "
        <h3>Hola $nombre,</h3>
        <p>Has recibido un mensaje de tu fisioterapeuta <strong>$nombreFisio</strong> desde la clínica:</p>
        <blockquote style='font-style:italic; padding-left:10px; border-left:3px solid #ccc;'>"
            . nl2br(htmlspecialchars($mensaje)) . "
        </blockquote>
        <p>Si necesitas responder, por favor, contacta con tu fisioterapeuta.</p>
        <hr>
        <p style='font-size:small;color:#666;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
    ";

    $enviado = enviarCorreoGeneral($email, $nombre, $asunto, $mensajeHtml);

    if ($enviado) {
        registrarLog($pdo, $_SESSION['user_id'], 'Contacto_fisio', "Mensaje enviado a paciente ID $paciente_id");
        $_SESSION['mensaje'] = "Mensaje enviado correctamente.";
        header('Location: contactar.php');
    } else {
        registrarLog($pdo, $_SESSION['user_id'], 'Contacto_fisio_error', "Error al enviar mensaje a paciente ID $paciente_id");
        $_SESSION['mensaje'] = "Error al enviar el mensaje. Inténtalo de nuevo más tarde.";
        header('Location: contactar.php');
    }
    exit();
}
