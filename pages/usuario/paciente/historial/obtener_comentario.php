<?php
include('../../../../includes/db.php');
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de cita no especificado']);
    exit;
}

$agenda_id = $_GET['id'];

// 1. Obtener el estado de la cita
$sql_estado = "SELECT estado FROM agenda WHERE id = ?";
$stmt_estado = $pdo->prepare($sql_estado);
$stmt_estado->execute([$agenda_id]);
$estado = $stmt_estado->fetchColumn();

if ($estado === false) {
    echo json_encode(['error' => 'Cita no encontrada']);
    exit;
}

// 2. Obtener los comentarios de la cita
$sql_comentarios = "SELECT comentario, fecha_comentario 
                   FROM comentarios_agenda 
                   WHERE agenda_id = ? 
                   ORDER BY fecha_comentario DESC";
$stmt_comentarios = $pdo->prepare($sql_comentarios);
$stmt_comentarios->execute([$agenda_id]);
$comentarios = $stmt_comentarios->fetchAll(PDO::FETCH_ASSOC);

// 3. Devolver JSON con estado y comentarios
echo json_encode([
    'estado' => $estado,
    'comentarios' => $comentarios
]);
