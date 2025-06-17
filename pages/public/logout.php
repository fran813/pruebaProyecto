<?php
/**
 * Cierra la sesión del usuario actual,
 * registra el evento de logout en el log de actividad,
 * y redirige al formulario de login.
 */
session_start();
require_once('../../includes/db.php');
require_once('../../includes/logger.php');

// Si hay un usuario logueado, registramos el cierre de sesión
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    registrarLog($pdo, $userId, "Logout", "Cierre de sesión");
}

// Elimina todas las variables de sesión y destruye la sesión
session_unset();
session_destroy();

// Redirigir al login o página de inicio
header("Location: ../../pages/public/login.php");
exit();
?>
