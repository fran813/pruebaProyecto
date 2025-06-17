<?php 
/**
 * Página de contacto de la clínica.
 * Muestra datos de contacto, un formulario para mensajes y un mapa de ubicación.
 * El formulario envía la información a contacto_proceso.php.
 */
include('../../includes/db.php'); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctanos</title>
    <link href="/dist/output.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
    <?php include('../../includes/header.php');?>
    
    <main class="flex-grow container mx-auto p-8">
        <h2 class="text-3xl font-semibold text-center text-purple-700 mb-6">Contáctanos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Información de contacto -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 text-center">Información de Contacto</h3>
                <br><br>
                <p class="mt-2 text-gray-600 text-center">📍 Dirección: Carretera De Nijar 149, Almeria, España</p>
                <p class="mt-2 text-gray-600 text-center">📞 Teléfono: +34 674 344 201</p>
                <p class="mt-2 text-gray-600 text-center">✉️ Correo: pablofisio@gmail.com</p>
                <br><br>
                <p class="mt-2 text-gray-600 text-center">🕒 Horario: Lunes - Viernes</p>
                <p class="mt-2 text-gray-600 text-center">  9:00 AM - 13:30 PM</p>
                <p class="mt-2 text-gray-600 text-center"> 17:00 AM - 20:00 PM</p>

            </div>

            <!-- Formulario de contacto -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800">Envíanos un mensaje</h3>
                <form action="../../proceso/contacto_proceso.php" method="POST" class="mt-4 space-y-4">
                    <div>
                        <label for="nombre" class="block text-gray-700 font-medium">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label for="mensaje" class="block text-gray-700 font-medium">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required class="w-full p-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg shadow-md hover:bg-purple-700">Enviar</button>
                </form>
            </div>
        </div>

        <!-- Google Maps -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 text-center">Ubicación</h3>
            <div class="mt-4">
                <iframe class="w-full h-96 rounded-lg shadow-lg" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d102189.61131996603!2d-2.52255033054271!3d36.83228785384368!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd7a9964dd7139fd%3A0x2498fa751c067d82!2sCentro%20de%20Fisioterapia%20y%20Osteopat%C3%ADa%20Pablo%20Mart%C3%ADnez%20Salinas!5e0!3m2!1ses!2ses!4v1747690429150!5m2!1ses!2ses" 
                    allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </main>

    <?php include('../../includes/footer.php'); ?>

</body>
</html>
