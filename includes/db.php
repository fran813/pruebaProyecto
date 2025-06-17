<?php
/**
 * db.php
 *
 * Establece la conexión con la base de datos mediante PDO.
 * - Define las credenciales de acceso (host, nombre de la BD, usuario y contraseña).
 * - Configura el modo de errores para lanzar excepciones.
 * - Si ocurre un error, muestra un mensaje y detiene la ejecución.
 */

$host = 'benduopgscpyzsyfpi5k-mysql.services.clever-cloud.com'; 
$dbname = 'benduopgscpyzsyfpi5k'; 
$username = 'uqsbgb2n4b8jedqm'; 
$password = 'vQgZQvb30MWIKIMgHZzF'; 

try {
    // Establecer la conexión PDO con la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar para que lance excepciones si ocurre un error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    // Mostrar mensaje de error y detener el script si falla la conexión
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
}
?>
