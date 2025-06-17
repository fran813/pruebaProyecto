<?php
session_start();
include('../../../../includes/db.php');
include('../../../../includes/header.php');
include('../../../../includes/enviar_correo.php');


// Seguridad: solo fisioterapeutas
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'fisioterapeuta') {
    header("Location: ../../login.php");
    exit;
}

$seccion = $_GET['seccion'] ?? 'crear_paciente';
?>
<body class="flex flex-col min-h-screen bg-gray-100">

    <main class="max-w-5xl mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold mb-6 text-purple-700">Gestión de Usuarios</h1>

        <nav class="mb-6 flex gap-4">
            <a href="?seccion=crear_paciente" class="<?= $seccion=='crear_paciente' ? 'font-bold border-b-2 border-purple-600' : 'text-gray-600' ?>">Crear Paciente</a>
            <a href="?seccion=buscar_pacientes" class="<?= $seccion=='buscar_pacientes' ? 'font-bold border-b-2 border-purple-600' : 'text-gray-600' ?>">Buscar Pacientes</a>
            <a href="?seccion=gestionar_bonos" class="<?= $seccion=='gestionar_bonos' ? 'font-bold border-b-2 border-purple-600' : 'text-gray-600' ?>">Gestionar Bonos</a>
        </nav>

        <section>
            <?php
            if ($seccion === 'crear_paciente') {
                include('crear_paciente.php');
            } elseif ($seccion === 'buscar_pacientes') {
                include('buscar_pacientes.php');
            } elseif ($seccion === 'gestionar_bonos') {
                include('gestionar_bonos.php');
            }
            ?>
        </section>
        <br>
        <br>
    </main>
</body>
<?php include('../../../../includes/footer.php'); ?>
