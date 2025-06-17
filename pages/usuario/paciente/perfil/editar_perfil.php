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

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT nombre, email, telefono, foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<main class="max-w-3xl mx-auto mt-10 mb-10 px-4">
    <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Editar Perfil</h2>

    <!-- Mostrar mensajes de sesión -->
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <?= htmlspecialchars($_SESSION['mensaje_exito']) ?>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

    <!-- Formulario actualizar datos -->
    <section class="mb-10 bg-white shadow rounded-xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold mb-4 text-purple-700">Actualizar información personal</h3>
        <form action="actualizar_datos.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Correo electrónico</label>
                <input type="email" name="email" value="<?= htmlspecialchars($paciente['email']) ?>" class="ocus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($paciente['telefono']) ?>" class="ocus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 outline-none">Guardar cambios</button>
        </form>
    </section>

    <!-- Formulario cambiar contraseña -->
    <section class="mb-10 bg-white shadow rounded-xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold mb-4 text-purple-700">Cambiar contraseña</h3>
        <form action="cambiar_contrasena.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Contraseña actual</label>
                <input type="password" name="contrasena_actual" class="ocus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Nueva contraseña</label>
                <input type="password" name="nueva_contrasena" class="ocus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Confirmar nueva contraseña</label>
                <input type="password" name="confirmar_contrasena" class="ocus:ring-purple-500 focus:border-purple-500 outline-none w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 outline-none">Cambiar contraseña</button>
        </form>
    </section>

    <!-- Formulario subir foto -->
    <section class="bg-white shadow rounded-xl p-6 border border-gray-200 text-center">
        <h3 class="text-lg font-semibold mb-4 text-purple-700">Cambiar foto de perfil</h3>
        <?php if (!empty($paciente['foto_perfil']) && file_exists(__DIR__ . "/../../../../uploads/{$paciente['foto_perfil']}")): ?>
            <img src="/reservas_proyecto/uploads/<?= htmlspecialchars($paciente['foto_perfil']) ?>" alt="Foto de perfil" class="w-24 h-24 mx-auto rounded-full object-cover border border-purple-300 mb-4">
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" action="subir_foto.php">
            <input type="file" name="foto" accept="image/*" required
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0 file:text-sm file:font-semibold
                          file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 mb-4">
            <button type="submit" class="outline-none bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Subir Foto
            </button>
        </form>
    </section>
    
    <br>
    <!-- Formulario para Solicitar desactivar cuenta --> 
    <form class="text-center" action="solicitar_baja.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres solicitar la desactivación de tu cuenta?');">
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
            Solicitar desactivación de cuenta
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="perfil_paciente.php" class="text-purple-600 hover:underline">Volver a Mi Perfil</a>
    </div>
</main>

<?php include('../../../../includes/footer.php'); ?>
