<?php 
session_start();
include('../../../../includes/db.php'); 
include('../../../../includes/header.php'); 

// Verificamos si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Obtener todos los fisioterapeutas
$stmt = $pdo->prepare("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'fisioterapeuta' AND activo = 1");
$stmt->execute();
$fisios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto con Fisioterapeuta</title>
    <link href="/reservas_proyecto/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

<main class="flex-grow container mx-auto p-8">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Contacta a tu Fisioterapeuta</h2>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="max-w-2xl mx-auto mb-4">
                <div class="p-4 rounded-lg shadow <?= strpos($_SESSION['mensaje'], 'correctamente') !== false ? 'bg-green-100  border-green-400' : 'bg-red-100  border-red-400' ?>">
                    <?= $_SESSION['mensaje'] ?>
                </div>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        <form action="/reservas_proyecto/includes/contacto_fisio_proceso.php" method="POST" class="space-y-4">
            <!-- Fisioterapeuta -->
            <div>
                <label for="fisio_id" class="block text-gray-700 font-medium">Selecciona al fisioterapeuta</label>
                <select id="fisio_id" name="fisio_id" required class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($fisios as $fisio): ?>
                        <option value="<?= $fisio['id_usuario'] ?>"><?= htmlspecialchars($fisio['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Mensaje -->
            <div>
                <label for="mensaje" class="block text-gray-700 font-medium">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="5" required class="focus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg"></textarea>
            </div>

            <!-- Botón -->
            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">Enviar Mensaje</button>
        </form>
    </div>
</main>

<?php include('../../../../includes/footer.php'); ?>

</body>
</html>
