<?php 
/**
 * Página que muestra la lista de pacientes desactivados (activo = 0)
 * Permite reactivar pacientes mediante un formulario que envía su ID a reactivar_paciente.php
 * Muestra un mensaje de confirmación tras la reactivación.
 * Incluye navegación para volver a la gestión de pacientes activos.
 */
include('../../../includes/header.php'); 
include('../../../includes/db.php'); 

// Obtener pacientes desactivados (activo = 0)
try {
    $stmt = $pdo->prepare("SELECT id_usuario, nombre, email, telefono, fecha_creacion FROM usuarios WHERE rol = 'paciente' AND activo = 0 ORDER BY nombre ASC");
    $stmt->execute();
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener pacientes: " . $e->getMessage());
}
?>

<main class="flex-grow max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'reactivado'): ?>
        <div id= mensajeComentario class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 text-center">
            Paciente reactivado correctamente.
        </div>
    <?php endif; ?>
    <h2 class="text-2xl font-semibold text-red-700 mb-6">Pacientes Desactivados</h2>
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Nombre</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Teléfono</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-600">Fecha Registro</th>
                    <th class="px-6 py-3 text-center text-sm font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($paciente['nombre']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($paciente['email']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo htmlspecialchars($paciente['telefono']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800"><?php echo date("d/m/Y", strtotime($paciente['fecha_creacion'])); ?></td>
                        <td class="px-6 py-4 text-center text-sm">
                            <form method="POST" action="reactivar_paciente.php" class="inline" onsubmit="return confirm('¿Deseas reactivar este paciente?');">
                                <input type="hidden" name="id_usuario" value="<?php echo $paciente['id_usuario']; ?>">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                                    Reactivar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (count($pacientes) === 0): ?>
            <p class="p-6 text-gray-500 text-center">No hay pacientes desactivados.</p>
        <?php endif; ?>
    </div>
    <a href="gestion_pacientes.php" class="inline-block mt-6 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
        ← Volver a pacientes activos
    </a>
</main>
<script>
    // Ocultar mensaje despues de 3 segundos
    setTimeout(() => {
        document.getElementById('mensajeComentario').classList.add('hidden');
    }, 3000);
</script>
<?php include('../../../includes/footer.php'); ?>
