<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/logger.php');

//Verificacion de seguridad, existe sesion 
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: /pages/public/login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($telefono)) {
        
        // Actualizar en la base de datos
        $stmt = $pdo->prepare("UPDATE usuarios SET email = ?, telefono = ? WHERE id_usuario = ?");
        $resultado = $stmt->execute([$email, $telefono, $id_usuario]);

        $accion = "El paciente actualizó su perfil (email o teléfono)";
        registrarLog($pdo, $id_usuario, 'Edita_perfil', $accion);

        if ($resultado) {
            $_SESSION['mensaje_exito'] = "Datos actualizados correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al actualizar los datos. Inténtalo de nuevo.";
        }
    } else {
        $_SESSION['mensaje_error'] = "Por favor, ingresa un email válido y teléfono.";
    }
} else {
    $_SESSION['mensaje_error'] = "Acceso no permitido.";
}

header('Location: editar_perfil.php');
exit();
