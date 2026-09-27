<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ticker = $_POST['ticker'] ?? 'SPY';
$montoUsd = (float)($_POST['monto'] ?? 0);
$precioCompra = (float)($_POST['precio_compra'] ?? 1);
$fecha = $_POST['fecha'] ?? date('Y-m-d');

// Calculamos cuántas cuotas (acciones) compró con ese dinero
$cantidadCuotas = round($montoUsd / $precioCompra, 4);

$cotizacionDolar = 1350.50;
$montoArs = $montoUsd * $cotizacionDolar;

$gastosActuales = $_SESSION['gastos'] ?? 0;
$ingresosActuales = $_SESSION['ingresos'] ?? 0;
$ahorroActual = $ingresosActuales - $gastosActuales;

if($montoArs > $ahorroActual){
	echo "<script>alert('Fondos insuficientes. Necesitás $ )" . number_format($montoArs, 2, ',', '.') . " ARS pero tu saldo disponible es de $ " . number_format($ahorroActual, 2, ',', '.') . " ARS');</script>";
	include '../components/inversiones_content.php';
	exit;
}

$nombres = [
    'SPY' => 'S&P 500 ETF',
    'AAPL' => 'Apple Inc.',
    'MSFT' => 'Microsoft'
];

if (!isset($_SESSION['portfolio'])) {
    // Si el portafolio está vacío, iniciamos sin datos de ejemplo
    $_SESSION['portfolio'] = [];
}

// Bandera para promediar el precio si ya tiene acciones de esa misma empresa
$existe = false;
foreach ($_SESSION['portfolio'] as $index => $activo) {
    if ($activo['ticker'] === $ticker) {
        // Promedio ponderado del precio de compra
        $inversionAnterior = $activo['cantidad'] * $activo['precio_compra'];
        $inversionNueva = $cantidadCuotas * $precioCompra;
        $nuevaCantidadTotal = $activo['cantidad'] + $cantidadCuotas;
        
        $_SESSION['portfolio'][$index]['cantidad'] = $nuevaCantidadTotal;
        $_SESSION['portfolio'][$index]['precio_compra'] = ($inversionAnterior + $inversionNueva) / $nuevaCantidadTotal;
        $existe = true;
        break;
    }
}

if (!$existe) {
    $_SESSION['portfolio'][] = [
        'ticker' => $ticker,
        'nombre' => $nombres[$ticker],
        'cantidad' => $cantidadCuotas,
        'precio_compra' => $precioCompra,
        'precio_actual' => $precioCompra // Temporal, se actualiza al cargar la vista
    ];
}

if (!isset($_SESSION['gastos'])) $_SESSION['gastos'] = 0;
$_SESSION['gastos'] += $montoArs;

if (!isset($_SESSION['categorias'])) $_SESSION['categorias'] = [];
$_SESSION['categorias']['Inversiones'] = ($_SESSION['categorias']['Inversiones'] ?? 0) + $montoArs;

if (!isset($_SESSION['movimientos'])) $_SESSION['movimientos'] = [];
$_SESSION['movimientos'][] = [
    'tipo' => 'gasto',
    'monto' => $montoArs,
    'categoria' => 'Inversiones',
    'descripcion' => "Compra de $cantidadCuotas $ticker",
    'metodo_pago' => 'efectivo',
    'fecha' => $fecha . ' 10:00:00'
];

// Devolvemos la vista completa de inversiones para que recalcule los rendimientos y la gráfica
include '../components/inversiones_content.php';
exit;
?>
