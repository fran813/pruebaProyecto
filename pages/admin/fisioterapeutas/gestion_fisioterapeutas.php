<?php 
/**
 * Lista fisioterapeutas activos con opciones para editar o desactivar.
 * Permite acceder a fisioterapeutas desactivados y muestra mensajes de confirmación.
 */
include('../../../includes/header.php'); 
include('../../../includes/db.php'); 

// Obtener todos los fisioterapeutas activos (activo = 1)
try {
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, email, telefono, fecha_creacion FROM usuarios WHERE rol = 'fisioterapeuta' AND activo = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $fisioterapeutas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En caso de error se muestra mensaje y se detiene la ejecución
    die("Error al obtener fisioterapeutas: " . $e->getMessage());
}
?>

<main class="flex-grow max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'desactivado'): ?>
        <div id= mensajeComentario class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 text-center">
            Fisioterapeuta desactivado correctamente.
        </div>
    <?php endif; ?>
    
    <h2 class="text-2xl font-semibold text-purple-700 mb-6">Gestión de Fisioterapeutas</h2>

    <div class="text-right mb-4">
        <a href="fisioterapeutas_desactivados.php" class="inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            Ver Fisioterapeutas Desactivados
        </a>
    </div>
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-purple-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Teléfono</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Fecha Registro</th>
                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($fisioterapeutas as $fisioerapeuta): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($fisioerapeuta['nombre']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($fisioerapeuta['email']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($fisioerapeuta['telefono']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo date("d/m/Y", strtotime($fisioerapeuta['fecha_creacion'])); ?></td>
                        <td class="px-6 py-4 text-center text-sm">
                            <a href="editar_fisioterapeutas.php?id=<?php echo $fisioerapeuta['id_usuario']; ?>" class="inline-block bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600 mr-2">Editar</a>
                            <form method="POST" action="desactivar_fisioterapeutas.php" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres desactivar este fisioterapeuta?');">
                                <input type="hidden" name="id_usuario" value="<?php echo $fisioerapeuta['id_usuario']; ?>">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                    Desactivar
                                </button>
                            </form>                       
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (count($fisioterapeutas) === 0): ?>
            <p class="p-6 text-gray-500 text-center">No hay fisioterapeutas registrados.</p>
        <?php endif; ?>
    </div>
    <a href="../admin_dashboard.php" class="inline-block mt-6 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
        ← Volver a al inicio
    </a>
</main>
<script>
    // Ocultar mensaje despues de 3 segundos
    setTimeout(() => {
        document.getElementById('mensajeComentario').classList.add('hidden');
    }, 3000);
</script>
<?php include('../../../includes/footer.php'); ?>
