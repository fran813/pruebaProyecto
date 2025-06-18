<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
 * Reserva una cita para un paciente verificando disponibilidad,
 * gestionando bonos si aplica, enviando correo de confirmación
 * y registrando la acción en el log.
 */
session_start();
include('../../../../includes/db.php');
include('../../../../includes/logger.php');
require '../../../../includes/enviar_correo.php'; 

// Verificar que el usuario esté autenticado y sea paciente
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['mensaje' => 'Acceso no autorizado.']);
    exit();
}

// Obtener datos JSON enviados por cliente
$data = json_decode(file_get_contents('php://input'), true);

$fecha = $data['fecha'] ?? '';
$hora = $data['hora'] ?? '';
$idPaciente = $_SESSION['user_id'];
$idFisio = $data['id_fisio'] ?? '';
$tipo = $data['tipo_cita'] ?? 'Normal';

// Validar datos recibidos
if (!$fecha || !$hora || !$idFisio) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Datos incompletos.']);
    exit();
}

// Comprobar si ya existe una cita reservada en esa fecha, hora y fisioterapeuta
$stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE fecha = ? AND hora = ? AND fisioterapeuta_id = ?");
$stmt->execute([$fecha, $hora, $idFisio]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    echo json_encode(['mensaje' => 'La hora ya está reservada.']);
    exit();
}

// Si la cita es bono, verificar bonos disponibles en tabla bonos
if ($tipo === 'Bono') {
    $stmt = $pdo->prepare("SELECT id_bono, cantidad FROM bonos WHERE id_usuario = ? AND cantidad > 0 ORDER BY id_bono ASC LIMIT 1");
    $stmt->execute([$idPaciente]);
    $bono = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Insertar la nueva cita con estado 'pendiente'
$stmt = $pdo->prepare("INSERT INTO agenda (paciente_id, fisioterapeuta_id, fecha, hora, estado, tipo_cita) VALUES (?, ?, ?, ?, 'pendiente', ?)");
$insertado = $stmt->execute([$idPaciente, $idFisio, $fecha, $hora, $tipo]);

if (!$insertado) {
    echo json_encode(['mensaje' => 'Error al reservar la cita.']);
    exit();
}

// Restar bono si es cita bono
if ($tipo === 'Bono') {
    $stmt = $pdo->prepare("UPDATE bonos SET cantidad = cantidad - 1 WHERE id_bono = ?");
    $stmt->execute([$bono['id_bono']]);
}

// Obtener nombre y email del paciente
$stmt = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$idPaciente]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Registramos la acción en el log
registrarLog($pdo, $idPaciente, "Crear_cita", "Paciente reservó una cita con fisioterapeuta ID $idFisio para el $fecha a las $hora. Tipo: $tipo.");
 
//Enviarmos correo de solicitud de cita
if ($usuario && $usuario['email']) {
        $asunto = "Solicitud de cita";
        $contenido = "
            <h3>Hola {$usuario['nombre']},</h3>
        <p>Hemos recibido tu solicitud de cita para el <strong>$fecha</strong> a las <strong>$hora</strong>.</p>
        <p>Un fisioterapeuta revisará tu solicitud y te confirmaremos la disponibilidad lo antes posible.</p>
        <p>Gracias por confiar en nosotros.</p>";

        // Enviar correo
        enviarCorreoGeneral($usuario['email'], $usuario['nombre'], $asunto, $contenido);
}
echo json_encode(['mensaje' => 'Cita reservada correctamente.']);
?>
