<?php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Reservas Fisio</title>
    <link href="/reservas_proyecto/dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

    <!-- Encabezado -->
    <header class="bg-purple-400 p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white">
            <div class="flex items-center space-x-3">
                <img src="img/logo.png" alt="Logo de la clinica" class="h-10 w-auto">
                <h1 class="text-2xl font-bold">Centro de Fisioterapia PM</h1>
            </div>
            <nav>
                <ul class="flex space-x-6 text-lg">
                    <li><a href="index.php" class="hover:text-gray-200 flex items-center">Inicio</a></li>
                    <li><a href="pages/public/login.php" class="hover:text-gray-200 flex items-center">Iniciar sesión</a></li>
                    <li><a href="pages/public/quienes_somos.php" class="hover:text-gray-200 flex items-center">Quienes Somos</a></li>
                    <li><a href="pages/public/contacto.php" class="hover:text-gray-200 flex items-center">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Sección principal -->
    <main class="relative">

        <section class="bg-cover bg-center bg-no-repeat h-screen flex items-center justify-center" style="background-image: url('img/portada1.jpg');">
            <div class="bg-white bg-opacity-70 p-10 rounded shadow-xl">
                <h2 class="text-4xl font-bold text-purple-800 mb-2 text-center">Bienvenido a la Clínica de Fisioterapia</h2>
                <p class="text-lg text-gray-700 text-center">Tu salud y bienestar son nuestra prioridad.</p>
            </div>
        </section>

        <!-- Contenido informativo -->
        <section id="servicios" class="py-16 px-6 max-w-6xl mx-auto text-center">
            <h3 class="text-3xl font-semibold text-purple-700 mb-6">Nuestros servicios</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/fisio_deportiva.jpg" alt="Fisioterapia Deportiva" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Fisioterapia Deportiva</h4>
                        <p class="text-gray-600">Rehabilitación de una amplia variedad de lesiones deportivas, ayudando a que los atletas y personas que hagan ejercicio frecuentemente vuelvan a su mejor estado físico.</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/dolor_espalda.jpg" alt="Dolor de Espalda" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Dolor de Espalda</h4>
                        <p class="text-gray-600">Realizamos todo tipo de tratamientos contra el dolor de columna, dependiendo cual sea su causa. Realizamos un diagnóstico previo y aplicamos la mejor solución en cada caso, teniendo en cuenta siempre las necesidades de los clientes</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/miofascial.jpg" alt="Miofascial" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Miofascial</h4>
                        <p class="text-gray-600">La terapia miofascial es una técnica de fisioterapia que se usa a menudo para tratar el síndrome de dolor miofascial, un trastorno de dolor crónico causado por la sensibilidad y la tensión en los tejidos miofasciales. Estos tejidos rodean y sostienen los músculos de todo el cuerpo.</p>
                    </div>
                </div>
            </div>

            <h3 class="text-3xl font-semibold text-purple-700 mt-16 mb-6">Más tratamientos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/domicilio.jpg" alt="Domicilio" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Servicio a domicilio</h4>
                        <p class="text-gray-600">En nuestro centro de fisioterapia Pablo Martínez sofrecemos un servicio de fisioterapia domiciliaria que nos permite ofrecerte la comodidad de una terapia profesional, en la privacidad de tu hogar. </p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/puncion_seca.jpg" alt="Punción seca" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Punción seca</h4>
                        <p class="text-gray-600">La punción seca consiste en la punción del músculo, con una aguja estéril de acupuntura, estimulando la zona a tratar y disminuyendo el dolor, produciendo la relajación del músculo</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <img src="img/masajes.jpg" alt="Masajes Terapéuticos" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-purple-600 mb-2">Masajes Terapéuticos</h4>
                        <p class="text-gray-600">Un masaje terapéutico alivia los dolores y molestias musculares mediante la manipulación de los tejidos blandos y los músculos. Este tipo de masaje se basa en técnicas del masaje deportivo, el masaje de tejido profundo y el masaje sueco para aliviar la tensión y conseguir la relajación.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>



    <?php include('./includes/footer.php'); ?>
</body>
</html>
