<?php
session_start();
include('../../../../includes/db.php');

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit();
}

$paciente_id = $_GET['id'];

try {
    // Consultamos las citas del paciente
    $sql = "SELECT id AS agenda_id, fecha, hora, estado 
            FROM agenda 
            WHERE paciente_id = ? 
            ORDER BY fecha DESC, hora DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paciente_id]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver en formato JSON
    header('Content-Type: application/json');
    echo json_encode($citas);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al obtener las citas.']);
}
