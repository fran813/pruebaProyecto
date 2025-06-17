<form method="GET" class="mb-6">
    <input type="hidden" name="seccion" value="gestionar_bonos">
    <input type="text" name="busqueda" placeholder="Buscar paciente"
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
    $pacientes = $stmt->fetchAll();

    if ($pacientes):
        echo '<ul class="space-y-4">';
        foreach ($pacientes as $p):
            // Obtener cantidad de bonos
            $stmtBonos = $pdo->prepare("SELECT cantidad FROM bonos WHERE id_usuario = ?");
            $stmtBonos->execute([$p['id_usuario']]);
            $bono = $stmtBonos->fetch();
            $cantidad = $bono ? $bono['cantidad'] : 0;
?>
            <li class="p-4 border rounded bg-white shadow">
                <p class="font-semibold text-lg"><?= htmlspecialchars($p['nombre']) ?> - <?= htmlspecialchars($p['telefono']) ?> (<?= $cantidad ?> bonos)</p>
                <form method="POST" action="actualizar_bonos.php" class="flex gap-2 items-center mt-2">
                    <input type="hidden" name="id_usuario" value="<?= $p['id_usuario'] ?>">
                    <input class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none" type="number" name="cantidad" placeholder="Cantidad"
                           class="p-1 border rounded w-24" required>
                    <select name="accion" class="w-full mb-2 mt-1 p-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 outline-none">
                        <option value="sumar">Sumar</option>
                        <option value="restar">Restar</option>
                    </select>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1 rounded">Actualizar</button>
                </form>
            </li>
<?php
        endforeach;
        echo '</ul>';
    else:
        echo '<p class="text-gray-500">No se encontraron pacientes.</p>';
    endif;
}
?>
