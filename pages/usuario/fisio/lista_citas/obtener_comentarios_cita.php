<?php
include('../../../../includes/db.php');

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$cita_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT comentario, fecha_comentario FROM comentarios_agenda WHERE agenda_id = ? ORDER BY fecha_comentario ASC");
$stmt->execute([$cita_id]);
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comentarios);
?>
