<?php
include('../../../../includes/db.php');
include('../../../../includes/logger.php');
require_once('../../../../includes/enviar_correo.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario']);
    $cita_id = intval($_POST['cita_id']);
    $usuario_id = $_SESSION['user_id'] ?? null;

    if (!$usuario_id) {
        die("Usuario no autenticado.");
    }

    if (!empty($comentario)) {
        // Insertar comentario
        $sql = "INSERT INTO comentarios_agenda (agenda_id, usuario_id, comentario, fecha_comentario) VALUES (:cita_id, :usuario_id, :comentario, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cita_id', $cita_id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);

        if ($stmt->execute()) {

            // Registrar log
            registrarLog($pdo, $usuario_id, "Comentario_cita", "Añadió un comentario a la cita con ID $cita_id");

            // Obtener email y nombre del paciente asociado a la cita
            $sql_cita = "SELECT u.email, u.nombre, a.fecha, a.hora
                         FROM agenda a
                         JOIN usuarios u ON a.paciente_id = u.id_usuario
                         WHERE a.id = :cita_id";
            $stmt_cita = $pdo->prepare($sql_cita);
            $stmt_cita->bindParam(':cita_id', $cita_id, PDO::PARAM_INT);
            $stmt_cita->execute();
            $cita = $stmt_cita->fetch(PDO::FETCH_ASSOC);

            if ($cita) {

                $cita['hora'] = date('H:i', strtotime($cita['hora']));
                $cita['fecha'] = date('d-m-Y', strtotime( $cita['fecha']));

                // Preparar asunto y mensaje del correo
                $asunto = "Nuevo comentario en tu cita";
                $mensaje = "
                    <h3>Hola {$cita['nombre']},</h3>
                    <p>Se ha añadido un nuevo comentario a tu cita del <strong>{$cita['fecha']}</strong> a las <strong>{$cita['hora']}</strong>.</p>
                    <p><em>" . htmlspecialchars($comentario) . "</em></p>
                ";

                // Enviar correo
                enviarCorreoGeneral($cita['email'], $cita['nombre'], $asunto, $mensaje);
            }

            echo "Comentario guardado correctamente.";
        } else {
            echo "Error al guardar comentario.";
        }
    } else {
        echo "El comentario está vacío.";
    }
}
?>
