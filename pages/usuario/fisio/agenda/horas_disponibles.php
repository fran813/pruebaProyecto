<?php
session_start();
include('../../../../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'fisioterapeuta') {
    http_response_code(403);
    exit;
}

$fecha = $_GET['fecha'] ?? null;
$fisio_id = $_SESSION['user_id'];

if ($fecha) {
    $sql = "SELECT hora FROM agenda WHERE fisioterapeuta_id = ? AND fecha = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fisio_id, $fecha]);
    $horas_ocupadas = array_map(function($hora) {
        return substr($hora, 0, 5);
    }, $stmt->fetchAll(PDO::FETCH_COLUMN));
    

    echo json_encode($horas_ocupadas);
}
?>
