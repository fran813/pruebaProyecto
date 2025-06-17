<?php
session_start();
include('../../../../includes/db.php'); 
require_once('../../../../includes/enviar_correo.php');
require_once('../../../../includes/logger.php'); 

//Cogemos el id del fisio que cambia el estado
$id_fisio = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cita_id']) && isset($_POST['estado'])) {
        $cita_id = $_POST['cita_id'];
        $nuevo_estado = $_POST['estado'];

        $estados_validos = ['Pendiente', 'Confirmado', 'Realizado'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            echo "Estado no válido";
            exit;
        }

        try {
            // Obtener el estado actual de la cita
            $stmt_estado = $pdo->prepare("SELECT estado FROM agenda WHERE id = ?");
            $stmt_estado->execute([$cita_id]);
            $estado_actual = $stmt_estado->fetchColumn();

            if (!$estado_actual) {
                echo "Cita no encontrada.";
                exit;
            }

            // Validar transición
            $transiciones_validas = [
                'Pendiente' => ['Confirmado', 'Realizado'],
                'Confirmado' => ['Realizado'],
                'Realizado' => [] // No se puede cambiar más
            ];

            if (!in_array($nuevo_estado, $transiciones_validas[$estado_actual])) {
                    echo "No se puede cambiar a este estado.";
                exit;
            }

            // Actualizar estado
            $sql = "UPDATE agenda SET estado = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nuevo_estado, $cita_id]);

            if ($stmt->rowCount() > 0) {
                // Recuperar datos de la cita para el correo
                $sql_cita = "SELECT a.fecha, a.hora, u.email, u.nombre 
                             FROM agenda a
                             JOIN usuarios u ON a.paciente_id = u.id_usuario
                             WHERE a.id = ?";
                $stmt_cita = $pdo->prepare($sql_cita);
                $stmt_cita->execute([$cita_id]);
                $cita = $stmt_cita->fetch(PDO::FETCH_ASSOC);

                if ($cita) {
                    $cita['hora'] = date('H:i', strtotime($cita['hora']));
                    $cita['fecha'] = date('d-m-Y', strtotime( $cita['fecha']));
                }

                // Enviar correo según el nuevo estado
                $asunto = '';
                $mensaje = '';

                switch ($nuevo_estado) {
                    case 'Pendiente':
                        $asunto = 'Tu cita está pendiente';
                        $mensaje = "
                            <h3>Hola {$cita['nombre']},</h3>
                            <p>Te informamos que tu cita para el <strong>{$cita['fecha']}</strong> a las <strong>{$cita['hora']}</strong> está actualmente en estado <strong>Pendiente</strong>.</p>
                            <p>Nos pondremos en contacto contigo para confirmarla pronto.</p>
                        ";
                        break;
                    case 'Confirmado':
                        $asunto = 'Tu cita ha sido confirmada';
                        $mensaje = "
                            <h3>Hola {$cita['nombre']},</h3>
                            <p>¡Buenas noticias! Tu cita para el <strong>{$cita['fecha']}</strong> a las <strong>{$cita['hora']}</strong> ha sido <strong>confirmada</strong>.</p>
                            <p>Te esperamos en nuestra clínica.</p>
                        ";
                        break;
                    case 'Realizado':
                        $asunto = 'Cita realizada';
                        $mensaje = "
                            <h3>Hola {$cita['nombre']},</h3>
                            <p>Esperamos que tu cita del <strong>{$cita['fecha']}</strong> a las <strong>{$cita['hora']}</strong> haya sido de tu agrado.</p>
                            <p>Gracias por confiar en nosotros. ¡Hasta la próxima!</p>
                        ";
                        break;
                }

                // Enviar correo
                enviarCorreoGeneral($cita['email'], $cita['nombre'], $asunto, $mensaje);

                //Registrar Log
                $descripcion = "El fisioterapeuta cambió el estado de la cita de $estado_actual a $nuevo_estado.";
                registrarLog($pdo, $_SESSION['user_id'], "cambio_estado_cita", $descripcion);
            }

            echo "ok";
            exit;

        } catch (Exception $e) {
            echo "Error al actualizar el estado de la cita: " . $e->getMessage();
        }
    } else {
        echo "Faltan datos para actualizar el estado de la cita.";
    }
}
?>
