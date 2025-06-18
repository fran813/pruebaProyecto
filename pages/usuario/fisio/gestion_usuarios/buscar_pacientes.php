<form method="GET" class="mb-6">
    <input type="hidden" name="seccion" value="buscar_pacientes">
    <input type="text" name="busqueda" placeholder="Buscar por nombre, email o teléfono"
           class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
</form>
<br>
<?php
$busqueda = $_GET['busqueda'] ?? '';
if ($busqueda !== '') {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol = 'paciente' AND activo = 1 AND
        (nombre LIKE ? OR email LIKE ? OR telefono LIKE ?)");
    $like = "%$busqueda%";
    $stmt->execute([$like, $like, $like]);
    $resultados = $stmt->fetchAll();

    if ($resultados):
        echo '<ul class="space-y-4">';
        foreach ($resultados as $paciente):
            // Consultar los bonos para este paciente
            $stmtBono = $pdo->prepare("SELECT cantidad FROM bonos WHERE id_usuario = ?");
            $stmtBono->execute([$paciente['id_usuario']]);
            $bono = $stmtBono->fetch();
            $cantidadBonos = $bono ? $bono['cantidad'] : 0;

            $paciente['fecha_nacimiento']= date('d-m-Y', strtotime( $paciente['fecha_nacimiento']));

            // Ruta de imagen
            $ruta_imagen = "/uploads/perfil_" . $paciente['id_usuario'] . ".jpg";
?>
            <li class="flex items-center gap-4 p-4 border rounded bg-white shadow">
                <img src="<?= $ruta_imagen ?>"
                     alt="Foto Perfil"
                     onerror="this.onerror=null; this.src='/uploads/default.jpg';"
                     class="w-16 h-16 rounded-full object-cover border text-center" />

                <div>
                    <p class="font-semibold text-lg"><?= htmlspecialchars($paciente['nombre']) ?></p>
                    <p class="text-sm text-gray-600">
                        <b>Email</b>: <?= htmlspecialchars($paciente['email']) ?> |
                        <b>Teléfono</b>: <?= htmlspecialchars($paciente['telefono']) ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        <b>Fecha de Nacimiento</b>: <?= htmlspecialchars($paciente['fecha_nacimiento']) ?> |
                        <b>Sesiones Bono</b>: <?= $cantidadBonos ?>
                    </p>
                </div>
            </li>
<?php
        endforeach;
        echo '</ul>';
    else:
        echo '<p class="text-gray-500 mt-4">No se encontraron pacientes.</p>';
    endif;
}
?>


