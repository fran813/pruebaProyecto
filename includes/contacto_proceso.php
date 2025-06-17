<?php
/**
 * contacto_proceso.php
 *
 * Procesa el formulario de contacto público.
 * - Recoge el nombre, email y mensaje desde el formulario.
 * - Valida que los campos no estén vacíos.
 * - Envía un correo a la clínica con el contenido del mensaje.
 * - Redirige con un parámetro GET indicando éxito o error.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('enviar_correo.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger y limpiar datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    // Validación básica de campos
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        header('Location: ../public/contacto.php?error=1');
        exit;
    }

    // Construcción del contenido del correo
    $asunto = 'Nuevo mensaje desde el formulario de contacto';
    $contenido = "
        <h3>Has recibido un nuevo mensaje de contacto</h3>
        <p><strong>Nombre:</strong> {$nombre}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Mensaje:</strong><br>{$mensaje}</p>
    ";

    // Datos de destino del correo (clínica)
    $correoDestino = 'fran813@hotmail.com';
    $nombreDestino = 'Clínica de Fisioterapia';

    // Envío del correo y redirección según resultado
    if (enviarCorreoGeneral($correoDestino, $nombreDestino, $asunto, $contenido)) {
        header('Location: /reservas_proyecto/pages/public/contacto.php?enviado=1');
    } else {
        header('Location: /reservas_proyecto/pages/public/contacto.php?error=2');
    }
    exit;
}
