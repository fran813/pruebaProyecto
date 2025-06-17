<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/logger.php');

//Verificacion de seguridad, existe sesion 
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: /reservas_proyecto/pages/public/login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    if (empty($contrasena_actual) || empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $_SESSION['mensaje_error'] = "Todos los campos son obligatorios.";
        header('Location: editar_perfil.php');
        exit();
    }

    if ($nueva_contrasena !== $confirmar_contrasena) {
        $_SESSION['mensaje_error'] = "La nueva contraseña y su confirmación no coinciden.";
        header('Location: editar_perfil.php');
        exit();
    }

    // Obtener la contraseña actual almacenada (hashed)
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($contrasena_actual, $usuario['password'])) {
        $_SESSION['mensaje_error'] = "La contraseña actual es incorrecta.";
        header('Location: editar_perfil.php');
        exit();
    }

    // Hashear la nueva contraseña
    $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

    // Actualizar la contraseña
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
    $resultado = $stmt->execute([$hash, $id_usuario]);

    //Registro log
    $accion = "El paciente actualizó su contraseña";
    registrarLog($pdo, $id_usuario, "Edita_contraseña", $accion);


    if ($resultado) {
        $_SESSION['mensaje_exito'] = "Contraseña cambiada correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al cambiar la contraseña. Inténtalo de nuevo.";
    }
} else {
    $_SESSION['mensaje_error'] = "Acceso no permitido.";
}

header('Location: editar_perfil.php');
exit();
