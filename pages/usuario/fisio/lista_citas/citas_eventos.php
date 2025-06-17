<?php
session_start();
include('../../../../includes/db.php');
header('Content-Type: application/json');

$id_fisio = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT a.id, a.fecha, a.hora, u.nombre AS nombre_paciente, a.estado
                       FROM agenda a
                       JOIN usuarios u ON a.paciente_id = u.id_usuario
                       WHERE a.fisioterapeuta_id = ?");
$stmt->execute([$id_fisio]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$eventos = [];
foreach ($citas as $cita) {
    $horaFormateada = date('H:i', strtotime($cita['hora']));
    $eventos[] = [
        'id' => $cita['id'],
        'title' => $horaFormateada . ' ' . $cita['nombre_paciente'],
        'start' => $cita['fecha'] . 'T' . $cita['hora'],
        'estado' => $cita['estado'],
    ];
}


echo json_encode($eventos);
