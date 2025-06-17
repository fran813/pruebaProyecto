<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/logger.php');

// Verifica si existe la sesion y si no reedirige
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: /pages/public/login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

// Verifica si ya existe una solicitud pendiente
$stmt = $pdo->prepare("SELECT id FROM solicitudes_baja WHERE id_usuario = ? AND estado = 'pendiente'");
$stmt->execute([$id_usuario]);

if ($stmt->rowCount() === 0) {
    // Insertar nueva solicitud
    $stmtInsert = $pdo->prepare("INSERT INTO solicitudes_baja (id_usuario) VALUES (?)");
    if ($stmtInsert->execute([$id_usuario])) {
        // Registrar log
        registrarLog($pdo, $id_usuario, 'Solicita_baja', 'El paciente ha solicitado la desactivación de su cuenta.');

        $_SESSION['mensaje_exito'] = "Tu solicitud de baja ha sido enviada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Hubo un error al registrar tu solicitud. Inténtalo más tarde.";
    }
} else {
    $_SESSION['mensaje_error'] = "Ya tienes una solicitud de baja pendiente.";
}

header('Location: editar_perfil.php');
exit();
