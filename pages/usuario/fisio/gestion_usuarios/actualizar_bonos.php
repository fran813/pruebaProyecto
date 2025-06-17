<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/logger.php');
include('../../../../includes/enviar_correo.php');



// Nos aseguramos que solo lo puedan hacer fisios
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'fisioterapeuta') {
    header("Location: ../../login.php");
    exit;
}

$id_usuario = $_POST['id_usuario'] ?? null;
$cantidad = (int) ($_POST['cantidad'] ?? 0);
$accion = $_POST['accion'] ?? '';

if ($id_usuario && $cantidad > 0 && in_array($accion, ['sumar', 'restar'])) {
    // Verificar si ya tiene bonos
    $stmt = $pdo->prepare("SELECT cantidad FROM bonos WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $bono = $stmt->fetch();

    if ($bono) {
        $nuevaCantidad = $accion === 'sumar' ? $bono['cantidad'] + $cantidad : max(0, $bono['cantidad'] - $cantidad);
        $stmtUpdate = $pdo->prepare("UPDATE bonos SET cantidad = ? WHERE id_usuario = ?");
        $stmtUpdate->execute([$nuevaCantidad, $id_usuario]);

        // Registrar en logs
        $descripcion = $accion === 'sumar' 
            ? "Añadió $cantidad bonos al paciente con ID $id_usuario. Total: $nuevaCantidad."
            : "Restó $cantidad bonos al paciente con ID $id_usuario. Total: $nuevaCantidad.";
        registrarLog($pdo, $_SESSION['user_id'], 'Gestión de Bonos', $descripcion);

    } else if ($accion === 'sumar') {
        // Solo se puede crear si se suma
        $stmtInsert = $pdo->prepare("INSERT INTO bonos (id_usuario, cantidad) VALUES (?, ?)");
        $stmtInsert->execute([$id_usuario, $cantidad]);

        // Registrar en logs
        $descripcion = "Creó un bono con $cantidad sesiones para el paciente con ID $id_usuario.";
        registrarLog($pdo, $_SESSION['user_id'], 'Gestión de Bonos', $descripcion);
    }
}

header("Location: gestion_usuarios.php?seccion=gestionar_bonos&mensaje=actualizado");
exit;
