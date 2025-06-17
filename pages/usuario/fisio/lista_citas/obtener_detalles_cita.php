<?php
session_start();
include('../../../../includes/db.php'); 

if (isset($_GET['id'])) {
    $cita_id = $_GET['id'];

    // Obtener los detalles de la cita
    $sql = "SELECT agenda.*, usuarios.nombre AS nombre_paciente, usuarios.id_usuario, usuarios.telefono AS telefono
            FROM agenda
            JOIN usuarios ON usuarios.id_usuario = agenda.paciente_id
            WHERE agenda.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cita_id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cita) {
        // Obtener el comentario más reciente para la cita
        $sqlComentario = "SELECT comentario FROM comentarios_agenda WHERE agenda_id = ? ORDER BY fecha_comentario DESC LIMIT 1";
        $stmtComentario = $pdo->prepare($sqlComentario);
        $stmtComentario->execute([$cita_id]);
        $comentario = $stmtComentario->fetchColumn();

        // Verificar si el paciente tiene bonos disponibles
        $sqlBono = "SELECT cantidad FROM bonos WHERE id_usuario = ? LIMIT 1";
        $stmtBono = $pdo->prepare($sqlBono);
        $stmtBono->execute([$cita['id_usuario']]);
        $bono = $stmtBono->fetch(PDO::FETCH_ASSOC);

        $tiene_bono = $bono && $bono['cantidad'] > 0;

        // Retornar los datos en formato JSON
        echo json_encode([
            'nombre_paciente' => $cita['nombre_paciente'],
            'fecha' => $cita['fecha'],
            'hora' => $cita['hora'],
            'descripcion' => $comentario ?: 'Sin comentarios',
            'estado' => $cita['estado'],
            'tipo_cita' => $cita['tipo_cita'],
            'telefono' => $cita['telefono'],
            'tiene_bono' => $tiene_bono
        ]);
    } else {
        echo json_encode(['error' => 'Cita no encontrada']);
    }
} else {
    echo json_encode(['error' => 'ID de cita no válido']);
}

?>
