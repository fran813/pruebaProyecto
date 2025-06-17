<?php
include('../../../../includes/db.php');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit();
}

$agenda_id = $_GET['id'];

try {
    $sql = "SELECT comentario, fecha_comentario FROM comentarios_agenda WHERE agenda_id = ? ORDER BY fecha_comentario DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$agenda_id]);
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['comentarios' => $comentarios]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los comentarios']);
}
