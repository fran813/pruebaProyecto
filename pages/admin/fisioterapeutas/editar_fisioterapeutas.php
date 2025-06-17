<?php
/*
* Página para editar los datos de un fisioterapeuta existente.
*/
include('../../../includes/header.php');
include('../../../includes/db.php');

// Verificar que se reciba el ID del fisioterapeuta por GET
if (!isset($_GET['id'])) {
    echo "<p class='text-red-500 text-center mt-6'>ID del fisioterapeuta no proporcionado.</p>";
    include('../../includes/footer.php');
    exit;
}

$id_fisioterapeuta = $_GET['id'];

// Obtener los datos actuales del fisioterapeuta de la base de datos
try {
    $stmt = $pdo->prepare("SELECT nombre, email, telefono FROM usuarios WHERE id_usuario = :id AND rol = 'fisioterapeuta'");
    $stmt->bindParam(':id', $id_fisioterapeuta);
    $stmt->execute();
    $fisioterapeuta = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra el fisioterapeuta, mostrar mensaje y salir
    if (!$fisioterapeuta) {
        echo "<p class='text-red-500 text-center mt-6'>Fisioterapeuta no encontrado.</p>";
        include('../../includes/footer.php');
        exit;
    }
} catch (PDOException $e) {
    // Mostrar error si la consulta falla
    echo "<p class='text-red-500 text-center mt-6'>Error: " . $e->getMessage() . "</p>";
    include('../../includes/footer.php');
    exit;
}

// Procesar el formulario si se envió por POST para actualizar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    try {
        // Actualizar datos en la base de datos con consulta preparada
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id_usuario = :id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':id' => $id_fisioterapeuta
        ]);

        // Redirigir a la página de gestión con mensaje de éxito
        header("Location: gestion_fisioterapeutas.php?editado=1");
        exit;
    } catch (PDOException $e) {
        // Mostrar error si falla la actualización
        echo "<p class='text-red-500 text-center mt-6'>Error al actualizar: " . $e->getMessage() . "</p>";
    }
}
?>

<main class="max-w-xl mx-auto mt-10 px-4">
    <h2 class="text-2xl font-semibold text-purple-700 mb-6">Editar Fisioterapeuta</h2>

    <form method="POST" class="bg-white p-6 rounded shadow-md space-y-4">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($fisioterapeuta['nombre']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($fisioterapeuta['email']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($fisioterapeuta['telefono']); ?>" required
                   class="w-full mt-1 p-1 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
        </div>
        <div class="flex justify-end space-x-2">
            <a href="gestion_fisioterapeutas.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 outline-none">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 outline-none">Guardar</button>
        </div>
    </form>
</main>

<?php include('../../../includes/footer.php'); ?>
