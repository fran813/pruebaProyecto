<?php 
/**
 * admin_dashboard.php
 * Panel principal del administrador:
 * - Muestra alerta si hay solicitudes de baja pendientes
 * - Enlaces rápidos para gestionar usuarios
 */
include('../../includes/header.php'); 
include('../../includes/db.php'); 
?>

<main class="flex-grow max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    
    <?php
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM solicitudes_baja WHERE estado = 'pendiente'");
    $total = $stmt->fetchColumn();

    if ($total > 0) {
        echo "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4'>
            Hay <strong>$total</strong> solicitud(es) de desactivación pendientes. 
            <a href='ver_solicitudes_baja.php' class='underline'>Ver detalles</a>
        </div>";
    }
    ?>


    <h2 class="text-2xl font-semibold text-purple-700 mb-4">Bienvenido, <?php echo $_SESSION['nombre']; ?></h2>
    <p class="text-gray-700">Desde aquí puedes gestionar los usuarios, roles y ver estadísticas generales.</p>

    <!-- Sección de gestión de usuarios -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Gestión de Usuarios</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card para gestionar pacientes -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Pacientes</h4>
                <p class="text-gray-600 mb-4">Visualiza, edita o desactiva a los pacientes registrados.</p>
                <a href="pacientes/gestion_pacientes.php" class="block text-center p-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700">
                    Gestionar Pacientes
                </a>
            </div>

            <!-- Card para gestionar fisioterapeutas -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Fisioterapeutas</h4>
                <p class="text-gray-600 mb-4">Visualiza, edita o desactiva a los fisioterapeutas registrados.</p>
                <a href="fisioterapeutas/gestion_fisioterapeutas.php" class="block text-center p-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700">
                    Gestionar Fisioterapeutas
                </a>
            </div>

            <!-- Card para gestionar administradores -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Administradores</h4>
                <p class="text-gray-600 mb-4">Gestiona los administradores y sus permisos.</p>
                <a href="administradores/gestion_admin.php" class="block text-center p-2 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600">
                    Gestionar Administradores
                </a>
            </div>

            <!-- Card para crear nuevo usuario -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Crear Nuevo Usuario</h4>
                <p class="text-gray-600 mb-4">Accede al formulario para registrar un nuevo usuario.</p>
                <a href="usuarios/crear_usuario.php" class="block text-center p-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700">
                    Crear Usuario
                </a>
            </div>
        </div>
    </div>
</main>

<?php include('../../includes/footer.php'); ?>
