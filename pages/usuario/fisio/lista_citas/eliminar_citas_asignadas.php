<?php
session_start();
include('../../../../includes/db.php'); 
require_once('../../../../includes/enviar_correo.php');
require_once('../../../../includes/logger.php'); 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];

    try {
        // Obtener los datos de la cita incluyendo el tipo_cita y paciente_id
        $sql = "SELECT a.fecha, a.hora, a.tipo_cita, a.paciente_id, u.email, u.nombre 
                FROM agenda a
                JOIN usuarios u ON a.paciente_id = u.id_usuario
                WHERE a.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cita_id]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        // Formatear hora
        if ($cita) {
            $cita['hora'] = date('H:i', strtotime($cita['hora']));
            $cita['fecha'] = date('d-m-Y', strtotime( $cita['fecha']));
        }

        if ($cita) {
            // Si la cita es de tipo bono, sumar +1 al bono
            if ($cita['tipo_cita'] === 'Bono') {
                $sql_bono = "SELECT cantidad FROM bonos WHERE id_usuario = ?";
                $stmt_bono = $pdo->prepare($sql_bono);
                $stmt_bono->execute([$cita['paciente_id']]);
                $bono_actual = $stmt_bono->fetchColumn();

                if ($bono_actual !== false) {
                    $sql_update_bono = "UPDATE bonos SET cantidad = cantidad + 1 WHERE id_usuario = ?";
                    $pdo->prepare($sql_update_bono)->execute([$cita['paciente_id']]);
                } else {
                    // Si no tiene bono aún, lo creamos con cantidad 1
                    $sql_insert_bono = "INSERT INTO bonos (id_usuario, cantidad) VALUES (?, 1)";
                    $pdo->prepare($sql_insert_bono)->execute([$cita['paciente_id']]);
                }
            }

            // Eliminar la cita
            $stmt_delete = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
            $stmt_delete->execute([$cita_id]);

            if ($stmt_delete->rowCount() > 0) {
                
                // Enviar correo
                $asunto = 'Cancelación de cita';
                $mensaje = "
                    <h3>Hola {$cita['nombre']},</h3>
                    <p>Te informamos que tu cita programada para el <strong>{$cita['fecha']}</strong> a las <strong>{$cita['hora']}</strong> ha sido <strong>cancelada</strong>.</p>
                    <p>Si deseas, puedes contactarnos para concertar una nueva. ¡Gracias por tu comprensión!</p>
                ";

                //Enviar correo de informacion al paciente
                enviarCorreoGeneral($cita['email'], $cita['nombre'], $asunto, $mensaje);

                // Registrar log
                $descripcion_log = "El fisioterapeuta eliminó la cita del paciente ID = {$cita['paciente_id']} programada para el {$cita['fecha']} a las {$cita['hora']}.";
                registrarLog($pdo, $_SESSION['user_id'], "Eliminar_cita", $descripcion_log);

            } else {
                $_SESSION['mensaje'] = "No se encontró la cita para eliminar.";
            }
        } else {
            $_SESSION['mensaje'] = "No se encontró la cita.";
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error al eliminar la cita: " . $e->getMessage();
    }
}

header("Location: citas_asignadas.php");
exit;
?>
