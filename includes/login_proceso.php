<?php
/**
 * Procesa el formulario de inicio de sesión.
 * Verifica el email y la contraseña, comprueba si el usuario está activo,
 * y redirige al dashboard correspondiente según el rol.
 * También registra un log de inicio de sesión exitoso.
 */
session_start();
include('db.php');
include('logger.php'); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar usuario por email, sin filtrar activo todavía
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['activo'] == 0) {
            // Usuario desactivado manda mensaje de usuario inactivo
            header("Location: /pages/public/login.php?error=inactivo");
            exit();
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['nombre'] = $user['nombre'];

            //REGISTRO DEL LOG
            registrarLog($pdo, $user['id_usuario'], "Login", "Inicio de sesión exitoso");

            // Redirigir según rol
            if ($_SESSION['rol'] == 'admin') {
                header("Location: /pages/admin/admin_dashboard.php");
                exit();
            } elseif ($_SESSION['rol'] == 'paciente') {
                header("Location: /pages/usuario/paciente/paciente_dashboard.php");
                exit();
            } elseif ($_SESSION['rol'] == 'fisioterapeuta') {
                header("Location: /pages/usuario/fisio/fisio_dashboard.php");
                exit();
            } else {
                session_destroy();
                header("Location: /pages/public/login.php");
                exit();
            }
        } else {
            // Contraseña incorrecta
            header("Location: /pages/public/login.php?error=credenciales");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: /pages/public/login.php?error=credenciales");
        exit();
    }
}
?>
