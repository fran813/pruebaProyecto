<?php
session_start();
include('../../../../includes/header.php');
include('../../../../includes/db.php');

//Verificacion de seguridad, existe sesion 
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'paciente') {
    header('Location: /pages/public/login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nombre, email, telefono, fecha_creacion, foto_perfil, fecha_nacimiento FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtBono = $pdo->prepare("SELECT cantidad FROM bonos WHERE id_usuario = ? LIMIT 1");
$stmtBono->execute([$id_usuario]);
$bono = $stmtBono->fetch(PDO::FETCH_ASSOC);

$fechaNacimiento = new DateTime($paciente['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($fechaNacimiento)->y;

?>
<body class="flex flex-col min-h-screen bg-gray-100">

<main class="flex-grow container mx-auto p-8">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Mi Perfil</h2>

    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 max-w-md mx-auto">
        <div class="text-center mb-6">
            <?php if (!empty($paciente['foto_perfil']) && file_exists(__DIR__ . "/../../../../uploads/{$paciente['foto_perfil']}")): ?>
                <img src="/uploads/<?= htmlspecialchars($paciente['foto_perfil']) ?>" alt="Foto de perfil" class="w-24 h-24 mx-auto rounded-full object-cover border border-purple-300">
            <?php else: ?>
                <div class="w-24 h-24 mx-auto rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-4xl font-bold">
                    <?= strtoupper(substr($paciente['nombre'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <h3 class="text-3xl font-semibold mt-4"><?= htmlspecialchars($paciente['nombre']) ?></h3>
        </div>
        
        <div class="space-y-4 text-left">
            <p><strong>Correo electrónico:</strong> <?= htmlspecialchars($paciente['email']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($paciente['telefono']) ?></p>
            <p><strong>Fecha de registro:</strong> <?= date('d/m/Y', strtotime($paciente['fecha_creacion'])) ?></p>
            <p><strong>Edad:</strong> <?php echo $edad; ?> años</p>
        </div>

        <?php if ($bono): ?>
            <div class="mt-8 bg-purple-50 rounded-lg p-6 border border-purple-300 text-center">
                <h3 class="text-xl font-semibold text-purple-700 mb-2">Sesiones disponibles</h3>
                <p class="text-4xl font-bold text-purple-800"><?= (int)$bono['cantidad'] ?></p>
            </div>
        <?php else: ?>
            <div class="mt-8 bg-gray-100 rounded-lg p-6 border border-gray-300 text-center">
                <p class="text-gray-600">No tienes ningún bono activo.</p>
            </div>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="editar_perfil.php" class="text-purple-600 hover:underline"> Editar Perfil</a>
        </div>
    </div>
</main>

<?php include('../../../../includes/footer.php'); ?>

</body>
