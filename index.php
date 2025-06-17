<?php
include 'includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicio | Reservas Fisio</title>
    <link href="/reservas_proyecto/dist/output.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-100" x-data="{ open: false }">

    <!-- Encabezado -->
    <header class="bg-purple-400 p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white">
            <div class="flex items-center space-x-3">
                <img src="img/logo.png" alt="Logo de la clinica" class="h-10 w-auto" />
                <h1 class="text-2xl font-bold">Centro de Fisioterapia PM</h1>
            </div>

            <!-- Botón hamburguesa para móvil -->
            <button
                @click="open = !open"
                class="block md:hidden focus:outline-none"
                aria-label="Abrir menú"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <!-- Menú navegación grande -->
            <nav class="hidden md:flex space-x-6 text-lg">
                <a href="index.php" class="hover:text-gray-200 flex items-center">Inicio</a>
                <a href="pages/public/login.php" class="hover:text-gray-200 flex items-center">Iniciar sesión</a>
                <a href="pages/public/quienes_somos.php" class="hover:text-gray-200 flex items-center">Quienes Somos</a>
                <a href="pages/public/contacto.php" class="hover:text-gray-200 flex items-center">Contacto</a>
            </nav>
        </div>

        <!-- Menú móvil (vertical) -->
        <nav
            x-show="open"
            @click.outside="open = false"
            class="md:hidden bg-purple-400 text-white"
            x-transition
            style="display: none;"
        >
            <ul class="flex flex-col space-y-2 p-4 text-lg">
                <li><a href="index.php" class="hover:text-gray-200 block">Inicio</a></li>
                <li><a href="pages/public/login.php" class="hover:text-gray-200 block">Iniciar sesión</a></li>
                <li><a href="pages/public/quienes_somos.php" class="hover:text-gray-200 block">Quienes Somos</a></li>
                <li><a href="pages/public/contacto.php" class="hover:text-gray-200 block">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <!-- Sección principal -->
    <main class="relative">
        <section
            class="bg-cover bg-center bg-no-repeat h-screen flex items-center justify-center"
            style="background-image: url('img/portada1.jpg');"
        >
            <div class="bg-white bg-opacity-70 p-10 rounded shadow-xl">
                <h2
                    class="text-4xl font-bold text-purple-800 mb-2 text-center"
                >
                    Bienvenido a la Clínica de Fisioterapia
                </h2>
                <p class="text-lg text-gray-700 text-center">
                    Tu salud y bienestar son nuestra prioridad.
                </p>
            </div>
        </section>

        <!-- Contenido informativo -->
        <section
            id="servicios"
            class="py-16 px-6 max-w-6xl mx-auto text-center"
        >
            <h3 class="text-3xl font-semibold text-purple-700 mb-6">
                Nuestros servicios
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/fisio_deportiva.jpg"
                        alt="Fisioterapia Deportiva"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Fisioterapia Deportiva
                        </h4>
                        <p class="text-gray-600">
                            Rehabilitación de una amplia variedad de lesiones deportivas, ayudando a que los atletas y personas que hagan ejercicio frecuentemente vuelvan a su mejor estado físico.
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/dolor_espalda.jpg"
                        alt="Dolor de Espalda"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Dolor de Espalda
                        </h4>
                        <p class="text-gray-600">
                            Realizamos todo tipo de tratamientos contra el dolor de columna, dependiendo cual sea su causa. Realizamos un diagnóstico previo y aplicamos la mejor solución en cada caso, teniendo en cuenta siempre las necesidades de los clientes
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/miofascial.jpg"
                        alt="Miofascial"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Miofascial
                        </h4>
                        <p class="text-gray-600">
                            La terapia miofascial es una técnica de fisioterapia que se usa a menudo para tratar el síndrome de dolor miofascial, un trastorno de dolor crónico causado por la sensibilidad y la tensión en los tejidos miofasciales. Estos tejidos rodean y sostienen los músculos de todo el cuerpo.
                        </p>
                    </div>
                </div>
            </div>

            <h3 class="text-3xl font-semibold text-purple-700 mt-16 mb-6">
                Más tratamientos
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/domicilio.jpg"
                        alt="Domicilio"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Servicio a domicilio
                        </h4>
                        <p class="text-gray-600">
                            En nuestro centro de fisioterapia Pablo Martínez sofrecemos un servicio de fisioterapia domiciliaria que nos permite ofrecerte la comodidad de una terapia profesional, en la privacidad de tu hogar. 
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/puncion_seca.jpg"
                        alt="Punción seca"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Punción seca
                        </h4>
                        <p class="text-gray-600">
                            La punción seca consiste en la punción del músculo, con una aguja estéril de acupuntura, estimulando la zona a tratar y disminuyendo el dolor, produciendo la relajación del músculo
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
                >
                    <img
                        src="img/masajes.jpg"
                        alt="Masajes Terapéuticos"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-6">
                        <h4
                            class="text-xl font-semibold text-purple-600 mb-2"
                        >
                            Masajes Terapéuticos
                        </h4>
                        <p class="text-gray-600">
                            El masaje terapéutico ayuda a aliviar el dolor, reducir la tensión muscular y promover la relajación. Con técnicas adaptadas a cada necesidad.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include('./includes/footer.php'); ?>
</body>
</html>
