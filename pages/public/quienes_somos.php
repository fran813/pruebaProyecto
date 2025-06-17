<?php 
/**
 * Página "Quiénes Somos" que describe la clínica de fisioterapia,
 * sus servicios y el equipo profesional.
 * Incluye sección informativa y galería de imágenes.
 */
include '/includes/db.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiénes Somos | Reservas Fisio</title>
    <link href="/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">

    <?php include '/includes/header.php'; ?>

    <main class="flex-grow">
        <section class="bg-white py-16 px-6 max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-purple-700 mb-8 text-center">¿Quiénes Somos?</h1>
            <p class="text-lg text-gray-700 mb-6">
                Somos una clínica de fisioterapia y centro de osteopatía en Almería dedicado a la fisioterapia deportiva y terapias manuales. Tenemos una amplia variedad de tratamientos entre ellos: osteopatía, terapia miofascial, fisioterapia deportiva, masaje deportivo, masaje terapéutico, tratamiento del dolor de columna, punción seca...
            </p>
            <p class="text-lg text-gray-700 mb-6">
                Somos un equipo de profesionales con mucha experiencia que pueden ayudarte con muchos tipos diferentes de afecciones y problemas musculares y articulares, así como con rehabilitaciones posquirúrgicas, y achaques de la edad.
            </p>
            <p class="text-lg text-gray-700 mb-6">
                En Centro de Fisioterapia Pablo Martínez, nuestro objetivo es brindar la mejor atención y asesoramiento posibles para facilitar a los clientes una mayor calidad de vida, de forma que recuperen su confianza y puedan llevar a cabo todo tipo de acciones que deseen.
            </p>
            <p class="text-lg text-gray-700 mb-6">
                Además, si no puedes desplazarte, contamos con un servicio a domicilio en el cual uno de nuestros profesionales irá a tu hogar para realizar un diagnóstico de tu problema y tratarlo de la mejor forma.
            </p>
            <p class="text-lg text-purple-800 font-semibold">
                ¡No lo dudes más y solicita tu cita ya!
            </p>
        </section>

        <section class="py-16 px-6 max-w-6xl mx-auto text-center">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-3xl font-semibold text-purple-700 mb-8 text-center">Conócenos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="overflow-hidden rounded-lg shadow-lg">
                        <img src="/img/Foto-1-fisio.jpeg" alt="Fisioterapeuta" class="w-full h-64 object-cover transform transition duration-500 hover:scale-105 hover:brightness-90">
                    </div>
                    <div class="overflow-hidden rounded-lg shadow-lg">
                        <img src="/img/Foto-2-fisio.jpeg" alt="Clínica 1" class="w-full h-64 object-cover transform transition duration-500 hover:scale-105 hover:brightness-90">
                    </div>
                    <div class="overflow-hidden rounded-lg shadow-lg">
                        <img src="/img/Foto-3-fisio.jpeg" alt="Clínica 2" class="w-full h-64 object-cover transform transition duration-500 hover:scale-105 hover:brightness-90">
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include '/includes/footer.php'; ?>

</body>
</html>
