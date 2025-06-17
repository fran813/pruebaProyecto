<?php
session_start();
include('../../../../includes/db.php');

if (!isset($_GET['paciente_id'])) {
    echo json_encode(['bono' => 0]);
    exit();
}

$paciente_id = intval($_GET['paciente_id']);

$sql = "SELECT cantidad FROM bonos WHERE id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$paciente_id]);
$bono = $stmt->fetchColumn();

echo json_encode(['bono' => (int)$bono ?: 0]);
?>
