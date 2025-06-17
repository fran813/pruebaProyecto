<?php
/**
 * Registra una acción en la tabla de logs.
 * Recibe el objeto PDO, el ID del usuario, el tipo de acción y la descripción de la acción.
 */
function registrarLog($pdo, $usuario_id, $tipo_accion, $accion) {
    try {
        $stmt = $pdo->prepare("INSERT INTO logs_actividad (usuario_id, tipo_accion, accion) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $tipo_accion, $accion]);
    } catch (PDOException $e) {
        error_log("Error al registrar log: " . $e->getMessage());
    }
}
?>
