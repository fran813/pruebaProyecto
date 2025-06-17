<?php
/**
 * enviar_correo.php
 *
 * Función para enviar correos electrónicos mediante PHPMailer y SMTP de Gmail.
 * 
 * - Usa Composer para cargar PHPMailer.
 * - Configura el servidor SMTP con usuario y contraseña de aplicación.
 * - Permite enviar mensajes HTML personalizados a un destinatario dado.
 * - Maneja errores con excepciones y registra logs si falla el envío.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga automática de dependencias instaladas por Composer (PHPMailer, etc.)
require __DIR__ . '../vendor/autoload.php'; 


function enviarCorreoGeneral($emailDestino, $nombrePaciente, $asunto, $mensajeHtml) {
    $mail = new PHPMailer(true); //Creamos objeto PHPMailer

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'franciscojavier.martinezsalinas@gmail.com'; 
        $mail->Password = 'ynmlrermntzsqfsj'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';

        // Datos del remitente
        $mail->setFrom('franciscojavier.martinezsalinas@gmail.com', 'Clínica de Fisioterapia');
        // Añadimos el destinatario
        $mail->addAddress($emailDestino, $nombrePaciente);

        // Configuramos el correo como HTML
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHtml;

        $mail->send();
        return true;// Si todo fue bien, devolvemos true
    } catch (Exception $e) {
        // En caso de error, guardamos el mensaje en el log
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        return false;
    }
}
?>
