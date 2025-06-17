<?php
session_start();
include('../../../../includes/db.php');

//Verificacion de seguridad, existe sesion 
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: /reservas_proyecto/pages/public/login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];

    if ($foto['error'] === UPLOAD_ERR_OK && strpos($foto['type'], 'image/') === 0) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nuevoNombre = 'perfil_' . $id_usuario . '.' . $ext;
        $ruta = '../../../../uploads/' . $nuevoNombre;

        // Mover archivo
        if (move_uploaded_file($foto['tmp_name'], $ruta)) {
            // Actualizar en BD
            $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?");
            $stmt->execute([$nuevoNombre, $id_usuario]);

            $_SESSION['mensaje_exito'] = "Foto de perfil actualizada correctamente.";
        } else {
            $_SESSION['mensaje_error'] = "Error al subir la foto.";
        }
    } else {
        $_SESSION['mensaje_error'] = "Archivo no válido. Solo imágenes permitidas.";
    }

    header('Location: editar_perfil.php');
    exit();
}

header('Location: editar_perfil.php');
exit();
?>
