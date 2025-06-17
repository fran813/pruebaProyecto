<?php
/*
 * Devuelve las horas disponibles.
 * 
 * - Recibe la fecha y el ID del fisioterapeuta por parámetros GET.
 * - Valida que la fecha no sea pasada ni fin de semana.
 * - Consulta las horas ya ocupadas en esa fecha para ese fisioterapeuta.
 * - Calcula las horas libres según un horario fijo.
 * - Devuelve un JSON con las opciones HTML para un <select> con las horas disponibles.
 */
include('../../../../includes/db.php');

// Obtener fecha e ID fisioterapeuta desde GET
$fecha = $_GET['fecha'] ?? '';
$id_fisio = $_GET['id_fisio'] ?? '';

// Validar que existan ambos parámetros
if (!$fecha || !$id_fisio) {
    http_response_code(400);
    echo json_encode([]);
    exit();
}

// Definir mañana para evitar fechas pasadas
$mañana = date('Y-m-d', strtotime('+1 day'));

// No permitir fechas anteriores a mañana
if ($fecha < $mañana) {
    echo json_encode(['opciones' => null]);
    exit();
}

// No permitir fines de semana (sábado = 6, domingo = 0)
$dow = date('w', strtotime($fecha));
if ($dow == 0 || $dow == 6) {
    echo json_encode(['opciones' => null]);
    exit();
}

// Horas posibles
$horas_posibles = [
    '09:00', '10:00', '11:30', '12:30',
    '17:00', '18:00', '19:00', '20:00'
];

// Consultar horas ocupadas para el fisioterapeuta en esa fecha
$stmt = $pdo->prepare("SELECT hora FROM agenda WHERE fecha = ? AND fisioterapeuta_id = ?");
$stmt->execute([$fecha, $id_fisio]);
$horas_ocupadas_raw = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Normalizar formato de horas ocupadas a 'HH:mm'
$horas_ocupadas = array_map(function($hora) {
    return date('H:i', strtotime($hora));
}, $horas_ocupadas_raw);

// Calcular horas disponibles (posibles menos ocupadas)
$horas_disponibles = array_values(array_diff($horas_posibles, $horas_ocupadas));

// Construir opciones HTML para un select con las horas libres
$opciones_html = '';
foreach ($horas_disponibles as $hora) {
    $opciones_html .= "<option value='$hora'>$hora</option>";
}

// Indicar que la respuesta es JSON y enviar las opciones
header('Content-Type: application/json');
echo json_encode(['opciones' => $opciones_html]);
?>
