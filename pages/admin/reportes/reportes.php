<?php
/**
 *  Página principal del panel de administrador.
 *  Muestra estadísticas generales del sistema, actividad reciente (logs) y un gráfico de accesos por hora historico de tu bd.
 */
session_start();
include('../../../includes/header.php');
include('../../../includes/db.php');

// Verifica que el usuario esté logueado y tenga rol de 'admin'
// Si no es así, redirige al login público
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../pages/public/login.php");
    exit();
}

// --- Estadísticas ---
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'paciente'");
    $stmt->execute();
    $totalPacientes = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'fisioterapeuta'");
    $stmt->execute();
    $totalFisioterapeutas = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
    $stmt->execute();
    $totalAdmins = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda");
    $stmt->execute();
    $totalCitas = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE estado = 'Realizado'");
    $stmt->execute();
    $totalCitasCompletadas = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM agenda WHERE estado = 'cancelada'");
    $stmt->execute();
    $totalCitasCanceladas = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $totalUsuariosMes = $stmt->fetchColumn();

    
    // --- Datos para el gráfico de logins por hora ---
    try {
        $stmt = $pdo->prepare("
            SELECT HOUR(fecha) AS hora, COUNT(*) AS cantidad
            FROM logs_actividad
            WHERE tipo_accion = 'login'
            GROUP BY hora
            ORDER BY hora ASC
        ");
        $stmt->execute();
        $loginsPorHora = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horas = [];
        $cantidades = [];
        for ($i = 0; $i < 24; $i++) {
            $horas[] = sprintf("%02d:00", $i);
            $cantidades[] = 0;
        }

        foreach ($loginsPorHora as $fila) {
            $hora = (int)$fila['hora'];
            $cantidades[$hora] = (int)$fila['cantidad'];
        }
    } catch (PDOException $e) {
        $horas = [];
        $cantidades = [];
    }

} catch (PDOException $e) {
    die("Error al obtener estadísticas: " . $e->getMessage());
}

// --- Logs con filtro por acción ---
$porPagina = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $porPagina;

// Filtro de acción
$accionFiltro = isset($_GET['accion']) ? trim($_GET['accion']) : '';

// Obtener acciones únicas
$stmt = $pdo->prepare("SELECT DISTINCT tipo_accion FROM logs_actividad ORDER BY tipo_accion ASC");
$stmt->execute();
$accionesDisponibles = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Contar logs según filtro
if ($accionFiltro !== '') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM logs_actividad WHERE accion = :accion");
    $stmt->bindValue(':accion', $accionFiltro);
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM logs_actividad");
}
$stmt->execute();
$totalLogs = $stmt->fetchColumn();
$totalPaginas = ceil($totalLogs / $porPagina);

// Obtener logs
if ($accionFiltro !== '') {
    $stmt = $pdo->prepare("SELECT * FROM logs_actividad WHERE tipo_accion = :accion ORDER BY fecha DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':accion', $accionFiltro);
} else {
    $stmt = $pdo->prepare("SELECT * FROM logs_actividad ORDER BY fecha DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-center text-purple-700 mb-4">Reportes Generales</h1>

    <!-- Estadísticas -->
    <section class="mb-10">
        <h2 class="text-3xl font-bold text-purple-800 border-b-4 border-purple-300 pb-4 mb-8">Estadísticas</h2>

        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 px-4 text-center">
            
            <!-- Tarjeta: Pacientes -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2 ">Pacientes</h3>
            <p class="text-4xl font-extrabold text-purple-600"><?php echo $totalPacientes; ?></p>
            </div>

            <!-- Tarjeta: Fisioterapeutas -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Fisioterapeutas</h3>
            <p class="text-4xl font-extrabold text-purple-600"><?php echo $totalFisioterapeutas; ?></p>
            </div>

            <!-- Tarjeta: Administradores -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Administradores</h3>
            <p class="text-4xl font-extrabold text-purple-600"><?php echo $totalAdmins; ?></p>
            </div>

            <!-- Tarjeta: Citas Totales -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Citas Totales</h3>
            <p class="text-4xl font-extrabold text-purple-600"><?php echo $totalCitas; ?></p>
            </div>

            <!-- Tarjeta: Citas Completadas -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Citas Completadas</h3>
            <p class="text-4xl font-extrabold text-green-600"><?php echo $totalCitasCompletadas; ?></p>
            </div>

            <!-- Tarjeta: Nuevos usuarios -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full hover:shadow-lg transition">
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nuevos usuarios este mes</h3>
            <p class="text-4xl font-extrabold text-blue-600"><?php echo $totalUsuariosMes; ?></p>
            </div>

        </div>
        </section>



    <!-- Logs -->
    <section>
         <section class="mb-10">
            <h2 class="text-2xl font-bold text-purple-800 border-b-2 border-purple-300 pb-2 mb-4">Logins por Hora</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <canvas id="loginChart" height="120"></canvas>
            </div>
        </section>
        <h2 class="text-2xl font-bold text-purple-800 border-b-2 border-purple-300 pb-2 mb-4">Logs de Actividad</h2>
        <!-- Filtro por acción -->
        <form method="get" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center gap-4">
            <label for="accion" class="font-medium">Filtrar por acción:</label>
            <select name="accion" id="accion" class="border rounded px-3 py-1">
                <option value="">Todas</option>
                <?php foreach ($accionesDisponibles as $accion): ?>
                    <option value="<?php echo htmlspecialchars($accion); ?>" <?php echo ($accion === $accionFiltro) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($accion); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700">Filtrar</button>
        </form>

        <?php if (count($logs) === 0): ?>
            <p class="text-gray-600">No hay registros de actividad.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded">
                    <thead>
                        <tr class="bg-purple-100 text-purple-800 font-semibold">
                            <th class="text-left px-4 py-2">ID</th>
                            <th class="text-left px-4 py-2">Usuario</th>
                            <th class="text-left px-4 py-2">Acción</th>
                            <th class="text-left px-4 py-2">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['usuario_id']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['accion']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['fecha']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <nav class="mt-4 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if (
                        $i == 1 || 
                        $i == $totalPaginas || 
                        abs($i - $pagina) <= 1
                    ): ?>
                        <a href="?pagina=<?php echo $i; ?>&accion=<?php echo urlencode($accionFiltro); ?>"
                            class="px-3 py-1 rounded text-sm font-medium <?php echo $i === $pagina ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-purple-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php elseif ($i == 2 && $pagina > 3 || $i == $totalPaginas - 1 && $pagina < $totalPaginas - 2): ?>
                        <span class="px-2 text-gray-500">...</span>
                    <?php endif; ?>
                <?php endfor; ?>
            </nav>

        <?php endif; ?>
    </section>
</main>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('loginChart').getContext('2d');
    const loginChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($horas); ?>,
            datasets: [{
                label: 'Cantidad de logins',
                data: <?php echo json_encode($cantidades); ?>,
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderColor: 'rgba(139, 92, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<?php include('../../../includes/footer.php'); ?>
