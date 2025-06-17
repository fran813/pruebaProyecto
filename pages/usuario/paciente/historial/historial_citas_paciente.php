<?php
/*
 * Obtiene todas las citas futuras del paciente logueado,
 * incluyendo el nombre del fisioterapeuta, y las devuelve en formato JSON.
 */
session_start();
include('../../../../includes/db.php');

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

// Obtener ID del paciente desde sesión
$usuario_id = $_SESSION['user_id'];

// Consulta para obtener citas del paciente con el nombre del fisioterapeuta
$sql = "SELECT a.id, a.fecha, a.hora, a.estado, u.nombre AS nombre_fisio
        FROM agenda a
        JOIN usuarios u ON a.fisioterapeuta_id = u.id_usuario
        WHERE a.paciente_id = ? AND a.fecha
        ORDER BY a.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);


$eventos = [];

foreach ($citas as $cita) {
    $eventos[] = [
        'id' => $cita['id'],
        'title' => date('H:i', strtotime($cita['hora'])) . " - Fisio: " . $cita['nombre_fisio'],
        'start' => $cita['fecha'],
        'color' => 'rgba(169, 108, 255, 0.8)' 
    ];
}

echo json_encode($eventos);
?>
