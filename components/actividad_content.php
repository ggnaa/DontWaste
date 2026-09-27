<?php
if (session_status() === PHP_SESSION_NONE){
	session_start();
}

$mesElegido = $_GET['mes'] ?? date('n');
$anioElegido = $_GET['anio'] ?? date('Y');

$movimientos = $_SESSION['movimientos'] ?? [];

$movimientosFiltrados = array_filter($movimientos, function($m) use ($mesElegido, $anioElegido) {
	$timestamp = strtotime($m['fecha']);
	return date('n', $timestamp) == $mesElegido && date('Y', $timestamp) == $anioElegido;
});

usort($movimientosFiltrados, function($a, $b) {
	return strtotime($b['fecha']) - strtotime($a['fecha']);	
});

$meses = [
	1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo',
	6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre',
	11 => 'Noviembre', 12 => 'Diciembre'	
];
?>


<div class="panel" style="margin-top: 20px; min-height: 500px;">
	<div class="actividad-header">
		<div>
			<h3>Historial de Movimientos</h3>
			<p style="color: #666; font-size: 13px; margin-top: 5px;">Registro contable</p>
			</div>

			<form class="actividad-filtros"
				  hx-get="components/actividad_content.php"
				  hx-target="#main-content"
				  hx-trigger="change">

				<select name="mes" class="filtro-select">
					<?php foreach($meses as $num => $nombre): ?>
						<option value="<?= $num ?>" <?= $num == $mesElegido ? 'selected' : '' ?>>
							<?= $nombre ?>
						</option>
					<?php endforeach; ?>
				</select>

				<select name="anio" class="filtro-select">
					<?php for($y = 2025; $y <= date('Y'); $y++): ?>
						<option value="<?= $y ?>" <?= $y == $anioElegido ? 'selected' : '' ?>>
							<?= $y ?>
						</option>
					<?php endfor; ?>
				</select>
			</form>
		</div>

		<div class="actividad-lista">
			<div class="actividad-row header">
				<div class="col-fecha">Fecha</div>
				<div class="col-detalle">Detalle</div>
				<div class="col-cat">Categoría</div>
				<div class="col-metodo">Método</div>
				<div class="col-monto">Monto</div>
			</div>

			<div class="actividad-items">
			<?php if (empty($movimientosFiltrados)) : ?>
				<p style="padding: 30px; text-align: center; color: #888;">No hay movimientos registrados en este período.</p>
			<?php else: ?>
				<?php foreach($movimientosFiltrados as $mov):
					$fechaFmt = date('d/m/Y', strtotime($mov['fecha']));
					$esIngreso = $mov['tipo'] === 'ingreso';
					$signo = $esIngreso ? '+ $' : '- $';
					$claseMonto = $esIngreso ? 'positive' : 'negative';
					$metodoFmt = $mov['metodo_pago'] === 'efectivo' ? 'Efectivo/Débito'	: 'Crédito';
				?>
					<div class="actividad-row">
						<div class="col-fecha"><?= $fechaFmt ?></div>
						<div class="col-detalle"><strong><?= htmlspecialchars($mov['descripcion']) ?></strong></div>
						<div class="col-cat"><span class="badge"><?= htmlspecialchars($mov['categoria']) ?></span></div>
						<div class="col-metodo"><?= $metodoFmt ?></div>
						<div class="col-monto <?= $claseMonto ?>">
							<strong><?= $signo ?> <?= number_format($mov['monto'], 2, ',', '.') ?></strong>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
