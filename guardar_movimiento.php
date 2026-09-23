<?php
session_start(); // Esencial para acceder a la memoria

$tipo = $_POST['tipo'] ?? 'gasto';
$monto = (float)($_POST['monto'] ?? 0);
$categoria = $_POST['categoria'] ?? 'Varios';
$descripcion = $_POST['descripcion'] ?? '';

$titulo = ($descripcion !== '') ? $descripcion : $categoria;
$montoFormateado = number_format($monto, 2, ',', '.'); 
$claseMonto = ($tipo === 'ingreso') ? 'positive' : 'negative';
$signo = ($tipo === 'ingreso') ? '+ $' : '- $';

// Verificación de fondos para gastos
$gastosActuales = $_SESSION['gastos'] ?? 0;
$ingresosActuales = $_SESSION['ingresos'] ?? 0;
$ahorroActual = $ingresosActuales - $gastosActuales;

if ($tipo === 'gasto' && $monto > $ahorroActual) {
    // Si el gasto supera el ahorro, devolvemos una alerta y detenemos la ejecución
    echo "<script>alert('Fondos insuficientes: No puedes registrar un gasto de $ " . number_format($monto, 2, ',', '.') . " porque tu saldo actual es de $ " . number_format($ahorroActual, 2, ',', '.') . "');</script>";
    exit;
}

usleep(500000); 

// ACUMULAMOS los totales
if (!isset($_SESSION['gastos'])) $_SESSION['gastos'] = 0;
if (!isset($_SESSION['ingresos'])) $_SESSION['ingresos'] = 0;
if (!isset($_SESSION['categorias'])) {
    $_SESSION['categorias'] = [
        'Gastronomía' => 0, 'Supermercado' => 0, 'Vivienda' => 0, 
        'Servicios' => 0, 'Transporte' => 0, 'Ocio' => 0, 
        'Inversiones' => 0, 'Varios' => 0
    ];
}

if ($tipo === 'gasto') {
    $_SESSION['gastos'] += $monto;
    // Sumamos a la categoría específica si existe, sino a Varios
    if (array_key_exists($categoria, $_SESSION['categorias'])) {
        $_SESSION['categorias'][$categoria] += $monto;
    } else {
        $_SESSION['categorias']['Varios'] += $monto;
    }
} else {
    $_SESSION['ingresos'] += $monto;
}

$datosGraficoNuevo = implode(',', array_values($_SESSION['categorias']));


// Calculamos el ahorro actual en base a los nuevos totales guardados
$baseAhorro = $_SESSION['ingresos'] - $_SESSION['gastos'];

// Damos formato a los nuevos totales
$totalGastadoStr = '$ ' . number_format($_SESSION['gastos'], 0, ',', '.');
$totalIngresosStr = '$ ' . number_format($_SESSION['ingresos'], 0, ',', '.');

$claseAhorro = ($baseAhorro >= 0) ? 'positive' : 'negative';
$signoAhorro = ($baseAhorro >= 0) ? '+ $' : '- $';
$totalAhorroStr = $signoAhorro . ' ' . number_format(abs($baseAhorro), 0, ',', '.');
?>

<!-- Movimiento nuevo para el historial -->
<li>
    <div class="history-info">
        <strong><?= htmlspecialchars($titulo) ?></strong>
        <span><?= htmlspecialchars($categoria) ?></span>
    </div>
    <div class="history-amount <?= $claseMonto ?>">
        <?= $signo ?> <?= $montoFormateado ?>
    </div>
</li>

<!-- OOB: Reemplazos en vivo para las tarjetas -->
<p id="total-gastado" class="amount" hx-swap-oob="true"><?= $totalGastadoStr ?></p>
<p id="total-ingresos" class="amount" hx-swap-oob="true"><?= $totalIngresosStr ?></p>
<p id="total-ahorro" class="amount <?= $claseAhorro ?>" hx-swap-oob="true"><?= $totalAhorroStr ?></p>

<!-- OOB: Actualización dinámica del gráfico -->
<div id="chart-updater" hx-swap-oob="true">
    <script>
        if (window.myPieChart) {
            // Actualizamos los datos internos de Chart.js
            window.myPieChart.data.datasets[0].data = [<?= $datosGraficoNuevo ?>];
            // Disparamos la animación de actualización
            window.myPieChart.update();
        }
    </script>
</div>
