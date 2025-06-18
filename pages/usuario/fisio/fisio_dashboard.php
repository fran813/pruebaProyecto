<?php 
include('../../../includes/header.php'); 
include('../../../includes/db.php'); 
?>
<main class="flex-grow max-w-5xl mx-auto mt-10 mb-10 px-4" >

    <!-- Encabezado con gradiente y saludo -->
    <div class="bg-gradient-to-r from-purple-100 to-purple-300 p-6 rounded-xl shadow mb-8">
        <h2 class="text-3xl font-bold text-purple-800">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?> 👋</h2>
        <p class="text-gray-700 mt-2">Gestiona tus citas y pacientes desde aquí.</p>
    </div>

    <!-- Tarjetas estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-xl shadow border-t-4 border-purple-500 flex flex-col items-center">
            <div class="text-purple-600 text-5xl mb-4">📅</div>
            <h3 class="text-gray-600 text-lg mb-2">Citas Hoy</h3>
            <p class="text-4xl font-bold text-purple-800 text-center">
                <?php
                    $id_fisio = $_SESSION['user_id'];
                    $hoy = date('Y-m-d');
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE fisioterapeuta_id = ? AND fecha = ?");
                    $stmt->execute([$id_fisio, $hoy]);
                    echo $stmt->fetchColumn();
                ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border-t-4 border-green-500 flex flex-col items-center">
            <div class="text-green-600 text-5xl mb-4">🧑‍🤝‍🧑</div>
            <h3 class="text-gray-600 text-lg mb-2">Total Pacientes</h3>
            <p class="text-4xl font-bold text-green-700 text-center">
                <?php
                    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT paciente_id) FROM agenda WHERE fisioterapeuta_id = ?");
                    $stmt->execute([$id_fisio]);
                    echo $stmt->fetchColumn();
                ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border-t-4 border-yellow-500 flex flex-col items-center">
            <div class="text-yellow-500 text-5xl mb-4">⏳</div>
            <h3 class="text-gray-600 text-lg mb-2">Citas Pendientes</h3>
            <p class="text-4xl font-bold text-yellow-600 text-center">
                <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE fisioterapeuta_id = ? AND estado = 'pendiente'");
                    $stmt->execute([$id_fisio]);
                    echo $stmt->fetchColumn();
                ?>
            </p>
        </div>
    </div>

    <!-- Accesos rápidos como tarjetas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="/pages/usuario/fisio/agenda/crear_cita.php" 
           class="flex flex-col items-center p-6 bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer">
            <div class="text-purple-500 text-5xl mb-3">➕</div>
            <h4 class="text-lg font-semibold text-purple-700">Crear Cita</h4>
        </a>

        <a href="/pages/usuario/fisio/gestion_usuarios/gestion_usuarios.php" 
           class="flex flex-col items-center p-6 bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer">
            <div class="text-yellow-500 text-5xl mb-3">⚙️</div>
            <h4 class="text-lg font-semibold text-yellow-600">Gestión Usuarios</h4>
        </a>

    </div>

</main>

<?php include('../../../includes/footer.php'); ?>
