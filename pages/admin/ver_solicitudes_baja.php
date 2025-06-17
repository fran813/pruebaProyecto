<?php
// Muestra las solicitudes de baja pendientes y permite desactivar cuentas.
include('../../includes/db.php');
include('../../includes/header.php');

//Consulta para la solicitud de bajas pendientes
$stmt = $pdo->query("
    SELECT sb.id_usuario, u.nombre, u.email, sb.fecha_solicitud 
    FROM solicitudes_baja sb 
    JOIN usuarios u ON sb.id_usuario = u.id_usuario 
    WHERE sb.estado = 'pendiente'
");

$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="flex-grow max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-4 text-purple-700">Solicitudes de Desactivación</h2>

    <?php foreach ($solicitudes as $solicitud): ?>
        <div class="bg-white shadow-md p-4 rounded mb-4 border">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($solicitud['nombre']) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($solicitud['email']) ?></p>
            <p><strong>Fecha:</strong> <?= $solicitud['fecha_solicitud'] ?></p>
            <form action="procesar_baja.php" method="POST" class="mt-2 text-center">
                <input type="hidden" name="usuario_id" value="<?= $solicitud['id_usuario'] ?>">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Desactivar Cuenta
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</main>

<?php include('../../includes/footer.php'); ?>
