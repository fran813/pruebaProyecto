<?php
/*
 * Devuelve en formato JSON los días próximos (60 días) en los que un fisioterapeuta tiene horas disponibles.
 * 
 * - Recibe el ID del fisioterapeuta vía GET.
 * - Consulta las citas futuras del fisioterapeuta.
 * - Compara las horas posibles con las ya ocupadas para cada día hábil.
 * - Genera un arreglo con los días que tienen al menos una hora disponible.
 * - Devuelve estos días como eventos con un título que indica cuántas horas están libres.
 */
include('../../../../includes/db.php');

// Obtener el ID del fisioterapeuta enviado por GET, o cadena vacía si no existe
$id_fisio = $_GET['id_fisio'] ?? '';
// Si no se recibe ID, devolver JSON vacío y salir
if (!$id_fisio) {
    echo json_encode([]);
    exit();
}

// Horas posibles para citas en un día
$horas_posibles = [
    '09:00', '10:00', '11:30', '12:30',
    '17:00', '18:00', '19:00', '20:00'
];

// Consultar citas futuras para el fisioterapeuta a partir de hoy
$stmt = $pdo->prepare("SELECT fecha, hora FROM agenda WHERE fisioterapeuta_id = ? AND fecha >= CURDATE()");
$stmt->execute([$id_fisio]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar citas por fecha
$ocupadas_por_fecha = [];
foreach ($citas as $cita) {
    $fecha = $cita['fecha'];
    $hora = date('H:i', strtotime($cita['hora']));
    $ocupadas_por_fecha[$fecha][] = $hora;
}

// Preparar array para eventos con días disponibles
$eventos = [];
// Fecha de inicio: mañana
$hoy = new DateTime();
$hoy->modify('+1 day');;

// Intervalo de un día
$interval = new DateInterval('P1D');

// Periodo de 60 días desde mañana
$periodo = new DatePeriod($hoy, $interval, 60);

foreach ($periodo as $fecha) {
    $fechaStr = $fecha->format('Y-m-d');
    $diaSemana = $fecha->format('w');

    // Saltar sábados (6) y domingos (0)
    if ($diaSemana == 0 || $diaSemana == 6) continue; // Omitir fines de semana

    // Horas ocupadas para ese día
    $horas_ocupadas = $ocupadas_por_fecha[$fechaStr] ?? [];

    // Horas disponibles restando las ocupadas de las posibles
    $disponibles = array_diff($horas_posibles, $horas_ocupadas);
    $count = count($disponibles);

    // Si hay horas disponibles, crear evento con título y color
    if ($count > 0) {
        $eventos[] = [
            'title' => "$count hora(s) disponible(s)",
            'start' => $fechaStr,
            'display' => 'background',
            'color' => 'rgba(69, 6, 118, 1)'
        ];
    }
}
// Indicar que la respuesta es JSON y devolver los eventos
header('Content-Type: application/json');
echo json_encode($eventos);
?>
