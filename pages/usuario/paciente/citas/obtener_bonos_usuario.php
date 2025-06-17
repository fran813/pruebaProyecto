<?php
/*
 * Devuelve en formato JSON la cantidad de bonos disponibles para el usuario logueado.
 * 
 * - Recupera el ID del usuario de la sesión.
 * - Consulta en la base de datos la cantidad de bonos del usuario.
 * - Retorna un JSON con el número de bonos (0 si no tiene ninguno).
 */
session_start();
include('../../../../includes/db.php');

// Obtiene el id del usuario desde la sesión, o 0 si no existe
$id_usuario = $_SESSION['user_id'] ?? 0;

// Prepara y ejecuta la consulta para obtener la cantidad de bonos del usuario
$stmt = $pdo->prepare("SELECT cantidad FROM bonos WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);

// Recupera la cantidad de bonos (si no hay resultado, será null)
$bonos = $stmt->fetchColumn();

// Devuelve un JSON con el número de bonos, 0 si no tiene ninguno
echo json_encode(['bonos' => $bonos ?: 0]);
?>
