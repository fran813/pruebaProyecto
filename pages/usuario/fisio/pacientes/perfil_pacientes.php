<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/header.php');

// Redirigir si no está logueado o no es fisioterapeuta
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'fisioterapeuta') {
    header("Location: ../../login.php");
    exit;
}

// Obtener lista de pacientes
$sql = "
    SELECT u.id_usuario, u.nombre, u.email, u.telefono, u.foto_perfil, u.fecha_nacimiento,
           b.cantidad AS num_bonos
    FROM usuarios u
    LEFT JOIN bonos b ON u.id_usuario = b.id_usuario
    WHERE u.rol = 'paciente'
    AND activo = 1
    GROUP BY u.id_usuario
    ORDER BY u.nombre ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Pacientes</title>
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
<main class="max-w-6xl mx-auto px-4 mb-6 mt-10">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Ficha de Pacientes</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
       <?php
        foreach ($pacientes as $paciente) {
            $ruta_imagen = "/reservas_proyecto/uploads/" .'perfil_'. $paciente['id_usuario'] . ".jpg";
            $fechaNacimiento = new DateTime($paciente['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
            ?>
            <div class="flex items-center bg-white p-4 rounded-lg shadow hover:scale-105 transition-transform">
                <img src="<?= htmlspecialchars($ruta_imagen) ?>" 
                    alt="Foto de <?= htmlspecialchars($paciente['nombre']) ?>" 
                    class="w-20 h-20 rounded-full object-cover mr-4 border-2 border-purple-500">
                <div>
                    <p class="text-lg font-semibold text-purple-700"><?= htmlspecialchars($paciente['nombre']) ?></p>
                    <p class="text-gray-700"><strong>Email</strong>: <?= htmlspecialchars($paciente['email']) ?></p>
                    <p class="text-gray-700"><strong>Teléfono</strong>: <?= htmlspecialchars($paciente['telefono']) ?></p>
                    <p class="text-gray-700"><strong>Edad</strong>: <?= $edad ?> años</p>
                    <p class="text-gray-700"><strong>Sesiones(bono)</strong>: <?= $paciente['num_bonos'] ?></p>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</main>

<?php include('../../../../includes/footer.php'); ?>
</body>
</html>
