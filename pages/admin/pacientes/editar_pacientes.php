<?php
/**
 * Edita datos de un paciente específico (nombre, email, teléfono)
 * Obtiene datos actuales y procesa el formulario para actualizar en BD
 * Muestra mensajes de error si no se encuentra el paciente o falla la consulta
 */

include('../../../includes/header.php');
include('../../../includes/db.php');

// Verifica que se haya pasado el ID del paciente por GET
if (!isset($_GET['id'])) {
    echo "<p class='text-red-500 text-center mt-6'>ID de paciente no proporcionado.</p>";
    include('../../includes/footer.php');
    exit;
}

$id_paciente = $_GET['id'];

// Obtiene datos actuales del paciente de la BD
try {
    $stmt = $pdo->prepare("SELECT nombre, email, telefono FROM usuarios WHERE id_usuario = :id AND rol = 'paciente'");
    $stmt->bindParam(':id', $id_paciente);
    $stmt->execute();
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        echo "<p class='text-red-500 text-center mt-6'>Paciente no encontrado.</p>";
        include('../../includes/footer.php');
        exit;
    }
} catch (PDOException $e) {
    echo "<p class='text-red-500 text-center mt-6'>Error: " . $e->getMessage() . "</p>";
    include('../../includes/footer.php');
    exit;
}

// Si se envió el formulario, actualiza los datos del paciente en la BD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id_usuario = :id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':id' => $id_paciente
        ]);

        // Redirige tras actualización exitosa
        header("Location: gestion_pacientes.php?editado=1");
        exit;
    } catch (PDOException $e) {
        echo "<p class='text-red-500 text-center mt-6'>Error al actualizar: " . $e->getMessage() . "</p>";
    }
}
?>

<main class="max-w-xl mx-auto mt-10 px-4">
    <h2 class="text-2xl font-semibold text-purple-700 mb-6">Editar Paciente</h2>

    <form method="POST" class="bg-white p-6 rounded shadow-md space-y-4">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($paciente['nombre']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($paciente['email']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($paciente['telefono']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div class="flex justify-end space-x-2">
            <a href="gestion_pacientes.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 outline-none">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 outline-none">Guardar</button>
        </div>
    </form>
</main>

<?php include('../../../includes/footer.php'); ?>
