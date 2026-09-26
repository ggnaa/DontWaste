<?php
session_start();

// Recibimos los datos
$marca = $_POST['marca'] ?? 'visa';
$numeros = $_POST['numeros'] ?? '0000';
$titular = strtoupper($_POST['titular'] ?? 'TITULAR');
$vencimiento = $_POST['vencimiento'] ?? '00/00';
$limite = max(0, (float)($_POST['limite'] ?? 0));

if (!isset($_SESSION['tarjetas'])) {
    $_SESSION['tarjetas'] = [];
}

// Creamos el array de la nueva tarjeta
$nuevaTarjeta = [
    'marca' => $marca,
    'numeros' => $numeros,
    'titular' => $titular,
    'vencimiento' => $vencimiento,
    'limite' => $limite
];

// La agregamos al principio del historial de tarjetas
array_unshift($_SESSION['tarjetas'], $nuevaTarjeta);

// Recalculamos los totales
$totalActivas = count($_SESSION['tarjetas']);
$limiteTotal = array_sum(array_column($_SESSION['tarjetas'], 'limite'));

usleep(300000); 
?>

<!-- HTML del plástico que se inyectará en la lista -->
<div class="credit-card <?= htmlspecialchars($marca) ?>">
    <div class="card-logo"><?= strtoupper($marca) ?></div>
    <div class="card-chip">
        <div class="chip-line"></div>
        <div class="chip-line"></div>
        <div class="chip-line"></div>
        <div class="chip-main"></div>
    </div>
    <div class="card-number">**** **** **** <?= htmlspecialchars($numeros) ?></div>
    <div class="card-details">
        <div class="card-holder">
            <span>Titular</span>
            <p><?= htmlspecialchars($titular) ?></p>
        </div>
        <div class="card-expires">
            <span>Vence</span>
            <p><?= htmlspecialchars($vencimiento) ?></p>
        </div>
    </div>
</div>

<!-- OOB Swaps para actualizar las tarjetas de resumen al instante -->
<p id="total-tarjetas" class="amount" hx-swap-oob="true"><?= $totalActivas ?></p>
<p id="limite-total" class="amount" hx-swap-oob="true">$ <?= number_format($limiteTotal, 0, ',', '.') ?></p>
<p id="empty-tarjetas-msg" hx-swap-oob="true" style="display: none;"></p>

<!-- OOB Swap que actualiza el selector del modal de movimiento al instante -->
<select name="metodo_pago" id="selector-metodo" :disabled="tipo !== 'gasto'" hx-swap-oob="true">
	<option value="efectivo">Débito / Efectivo</option>
	<?php
	foreach($_SESSION['tarjetas'] as $index => $t){
		$nombreTarjeta = ucfirst($t['marca']) . ' **** ' . $t['numeros'];
		echo "<option value='tarjeta_{$index}'>Crédito: " . htmlspecialchars($nombreTarjeta) . "</option>";
	}
	?>
</select>
