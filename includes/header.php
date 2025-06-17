<?php
// Iniciar sesión si no está iniciada aún
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Fisioterapia</title>
    <link href="/dist/output.css" rel="stylesheet">
    <!-- FullCalendar CSS y JS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
    <header class="bg-purple-400 p-4 shadow-lg" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white">
            <div class="flex items-center space-x-3">
                <?php
                if (isset($_SESSION['rol'])) {
                    switch ($_SESSION['rol']) {
                        case 'admin':
                            echo '<img src="/img/logo.png" alt="Logo de la clinica" class="h-10 w-auto"><h1 class="text-xl md:text-2xl font-bold">Portal del Administrador</h1>';
                            break;
                        case 'fisioterapeuta':
                            echo '<img src="/img/logo.png" alt="Logo de la clinica" class="h-10 w-auto"><h1 class="text-xl md:text-2xl font-bold">Portal del Fisioterapeuta</h1>';
                            break;
                        case 'paciente':
                            echo '<img src="/img/logo.png" alt="Logo de la clinica" class="h-10 w-auto"><h1 class="text-xl md:text-2xl font-bold">Portal del Paciente</h1>';
                            break;
                        default:
                            echo '<h1 class="text-xl md:text-2xl font-bold">Portal del Usuario</h1>';
                    }
                } else {
                    echo '<img src="/img/logo.png" alt="Logo de la clinica" class="h-10 w-auto">
                          <h1 class="text-xl md:text-2xl font-bold">Centro de Fisioterapia PM</h1>';
                }
                ?>
            </div>

            <!-- Botón hamburguesa -->
            <button @click="open = !open" class="md:hidden text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Menú navegación grande -->
            <nav class="hidden md:flex">
                <ul class="flex space-x-6 text-lg">
                    <?php
                    if (isset($_SESSION['rol'])) {
                        switch ($_SESSION['rol']) {
                            case 'admin':
                                echo '<li><a href="/pages/admin/admin_dashboard.php" class="hover:text-gray-200">Inicio</a></li>';
                                echo '<li><a href="/pages/admin/perfil_admin.php" class="hover:text-gray-200">Perfil</a></li>';
                                echo '<li><a href="/pages/admin/reportes/reportes.php" class="hover:text-gray-200">Reportes</a></li>';
                                break;
                            case 'fisioterapeuta':
                                echo '<li><a href="/pages/usuario/fisio/fisio_dashboard.php" class="hover:text-gray-200">Inicio</a></li>';
                                echo '<li><a href="/pages/usuario/fisio/lista_citas/citas_asignadas.php" class="hover:text-gray-200">Agenda</a></li>';
                                echo '<li><a href="/pages/usuario/fisio/historial_pacientes/historial_pacientes.php" class="hover:text-gray-200">Historiales</a></li>';
                                echo '<li><a href="/pages/usuario/fisio/contacto/contactar.php" class="hover:text-gray-200">Contactar</a></li>';
                                break;
                            case 'paciente':
                                echo '<li><a href="/pages/usuario/paciente/paciente_dashboard.php" class="hover:text-gray-200">Inicio</a></li>';
                                echo '<li><a href="/pages/usuario/paciente/perfil/perfil_paciente.php" class="hover:text-gray-200">Perfil</a></li>';
                                echo '<li><a href="/pages/usuario/paciente/citas/crear_cita.php" class="hover:text-gray-200">Citas</a></li>';
                                echo '<li><a href="/pages/usuario/paciente/historial/historial.php" class="hover:text-gray-200">Historial</a></li>';
                                echo '<li><a href="/pages/usuario/paciente/contacto/contacto_fisio.php" class="hover:text-gray-200">Contacto</a></li>';
                                break;
                        }
                        echo '<li><a href="/pages/public/logout.php" class="hover:text-gray-200">Cerrar sesión</a></li>';
                    } else {
                        echo '<li><a href="/index.php" class="hover:text-gray-200">Inicio</a></li>';
                        echo '<li><a href="/pages/public/login.php" class="hover:text-gray-200">Iniciar sesión</a></li>';
                        echo '<li><a href="/pages/public/quienes_somos.php" class="hover:text-gray-200">Quienes Somos</a></li>';
                        echo '<li><a href="/pages/public/contacto.php" class="hover:text-gray-200">Contacto</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>

        <!-- Menú móvil -->
        <div class="md:hidden mt-4" x-show="open" @click.away="open = false">
            <ul class="space-y-2 text-lg text-white">
                <?php
                if (isset($_SESSION['rol'])) {
                    switch ($_SESSION['rol']) {
                        case 'admin':
                            echo '<li><a href="/pages/admin/admin_dashboard.php" class="block px-4 py-2 hover:bg-purple-500">Inicio</a></li>';
                            echo '<li><a href="/pages/admin/perfil_admin.php" class="block px-4 py-2 hover:bg-purple-500">Perfil</a></li>';
                            echo '<li><a href="/pages/admin/reportes/reportes.php" class="block px-4 py-2 hover:bg-purple-500">Reportes</a></li>';
                            break;
                        case 'fisioterapeuta':
                            echo '<li><a href="/pages/usuario/fisio/fisio_dashboard.php" class="block px-4 py-2 hover:bg-purple-500">Inicio</a></li>';
                            echo '<li><a href="/pages/usuario/fisio/lista_citas/citas_asignadas.php" class="block px-4 py-2 hover:bg-purple-500">Agenda</a></li>';
                            echo '<li><a href="/pages/usuario/fisio/historial_pacientes/historial_pacientes.php" class="block px-4 py-2 hover:bg-purple-500">Historiales</a></li>';
                            echo '<li><a href="/pages/usuario/fisio/contacto/contactar.php" class="block px-4 py-2 hover:bg-purple-500">Contactar</a></li>';
                            break;
                        case 'paciente':
                            echo '<li><a href="/pages/usuario/paciente/paciente_dashboard.php" class="block px-4 py-2 hover:bg-purple-500">Inicio</a></li>';
                            echo '<li><a href="/pages/usuario/paciente/perfil/perfil_paciente.php" class="block px-4 py-2 hover:bg-purple-500">Perfil</a></li>';
                            echo '<li><a href="/pages/usuario/paciente/citas/crear_cita.php" class="block px-4 py-2 hover:bg-purple-500">Citas</a></li>';
                            echo '<li><a href="/pages/usuario/paciente/historial/historial.php" class="block px-4 py-2 hover:bg-purple-500">Historial</a></li>';
                            echo '<li><a href="/pages/usuario/paciente/contacto/contacto_fisio.php" class="block px-4 py-2 hover:bg-purple-500">Contacto</a></li>';
                            break;
                    }
                    echo '<li><a href="/pages/public/logout.php" class="block px-4 py-2 hover:bg-purple-500">Cerrar sesión</a></li>';
                } else {
                    // Usuario no logueado: menú público
                    echo '<li><a href="/index.php" class="block px-4 py-2 hover:bg-purple-500">Inicio</a></li>';
                    echo '<li><a href="/pages/public/login.php" class="block px-4 py-2 hover:bg-purple-500">Iniciar sesión</a></li>';
                    echo '<li><a href="/pages/public/quienes_somos.php" class="block px-4 py-2 hover:bg-purple-500">Quienes Somos</a></li>';
                    echo '<li><a href="/pages/public/contacto.php" class="block px-4 py-2 hover:bg-purple-500">Contacto</a></li>';
                }
                ?>
            </ul>
        </div>
    </header>
