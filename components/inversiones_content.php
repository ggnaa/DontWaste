<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. SELECTOR DINÁMICO DE GRÁFICOS
$tickerActivo = $_GET['chart'] ?? 'SPY';
$apiUrl = "https://query1.finance.yahoo.com/v8/finance/chart/{$tickerActivo}?range=1mo&interval=1d";

$context = stream_context_create(['http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]]);
$apiDatos = @file_get_contents($apiUrl, false, $context);

$fechasApi = [];
$preciosApi = [];

if ($apiDatos) {
    $data = json_decode($apiDatos, true);
    $result = $data['chart']['result'][0] ?? null;
    if ($result) {
        $timestamps = $result['timestamp'];
        $closes = $result['indicators']['quote'][0]['close'];
        foreach ($timestamps as $i => $time) {
            if (isset($closes[$i])) {
                $fechasApi[] = "'" . date('d/m', $time) . "'";
                $preciosApi[] = round($closes[$i], 2);
            }
        }
    }
}

if (empty($preciosApi)) { // Fallback por si Yahoo Finance bloquea la IP
    $fechasApi = ["'01/09'", "'05/09'", "'10/09'", "'15/09'", "'20/09'", "'25/09'", "'30/09'"];
    $preciosApi = [150.2, 152.5, 148.1, 155.8, 160.3, 158.9, 162.1]; 
}

$fechasStr = implode(',', $fechasApi);
$preciosStr = implode(',', $preciosApi);
$precioActualGrafico = end($preciosApi);

// 2. PORTAFOLIO REAL (Inicia vacío)
$portfolio = $_SESSION['portfolio'] ?? [];

$inversionTotal = 0;
$valorActualTotal = 0;

foreach ($portfolio as $activo) {
    $inversionTotal += ($activo['cantidad'] * $activo['precio_compra']);
    $valorActualTotal += ($activo['cantidad'] * $activo['precio_actual']);
}

$rendimientoTotal = $valorActualTotal - $inversionTotal;
$rendimientoPct = ($inversionTotal > 0) ? ($rendimientoTotal / $inversionTotal) * 100 : 0;
$claseRendimiento = $rendimientoTotal >= 0 ? 'positive' : 'negative';
$signoRendimiento = $rendimientoTotal >= 0 ? '+ $' : '- $';
?>

<div class="summary-cards inv-summary">
    <div class="card">
        <h3>Capital Invertido</h3>
        <p class="amount">u$s <?= number_format($inversionTotal, 2, '.', ',') ?></p>
    </div>
    <div class="card">
        <h3>Valor Actual</h3>
        <p class="amount">u$s <?= number_format($valorActualTotal, 2, '.', ',') ?></p>
    </div>
    <div class="card">
        <h3>Rendimiento Histórico</h3>
        <p class="amount <?= $claseRendimiento ?>">
            <?= $signoRendimiento ?> <?= number_format(abs($rendimientoTotal), 2, '.', ',') ?> 
            <span class="pct-span">(<?= number_format($rendimientoPct, 2, '.', '') ?>%)</span>
        </p>
    </div>
</div>

<div class="dashboard-grid inv-grid">
    <!-- Panel del Gráfico -->
    <div class="panel chart-panel inv-chart-panel">
        <div class="inv-chart-header">       
            <!-- Botones selectores de HTMX -->
            <div class="ticker-selectors">
                <?php 
                $opciones = ['SPY' => 'S&P 500', 'AAPL' => 'Apple', 'MSFT' => 'Microsoft'];
                foreach ($opciones as $ticker => $nombre): 
                    $isActiveClass = $tickerActivo === $ticker ? 'active-ticker' : '';
                ?>
                    <button hx-get="components/inversiones_content.php?chart=<?= $ticker ?>" 
                            hx-target="#main-content" 
                            class="ticker-btn <?= $isActiveClass ?>">
                        <?= $ticker ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="chart-container inv-chart-container">
            <canvas id="marketChart"></canvas>
        </div>
    </div>

    <!-- Panel de Mis Posiciones -->
    <div class="panel history-panel inv-history-panel">
        <div class="inv-history-header">
            <h3>Mis Posiciones</h3>
            <button class="btn-primary btn-sm" @click="isInvModalOpen = true">+ Comprar</button>
        </div>
        
        <div class="inv-history-scroll">
            <ul class="history-list inv-list">
                <?php if (empty($portfolio)): ?>
                    <p class="empty-msg">No tenés activos. Registrá tu primera compra.</p>
                <?php else: ?>
                    <?php foreach ($portfolio as $activo): 
                        $ganancia = ($activo['precio_actual'] - $activo['precio_compra']) * $activo['cantidad'];
                        $gananciaPct = (($activo['precio_actual'] - $activo['precio_compra']) / $activo['precio_compra']) * 100;
                        $claseActivo = $ganancia >= 0 ? 'positive' : 'negative';
                    ?>
                        <li class="inv-item">
                            <div class="history-info">
                                <strong><?= htmlspecialchars($activo['ticker']) ?></strong>
                                <span><?= htmlspecialchars($activo['nombre']) ?> (<?= $activo['cantidad'] ?> cuotas)</span>
                            </div>
                            <div class="inv-amount-wrapper">
                                <div class="inv-current-price">u$s <?= number_format($activo['precio_actual'], 2, '.', ',') ?></div>
                                <div class="inv-pct <?= $claseActivo ?>">
                                    <?= $ganancia >= 0 ? '+' : '' ?><?= number_format($gananciaPct, 2, '.', '') ?>%
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<script>
(function() {
    function renderMarketChart() {
        if (typeof Chart === 'undefined') {
            setTimeout(renderMarketChart, 50); return;
        }
        const canvas = document.getElementById('marketChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (window.myMarketChart) window.myMarketChart.destroy();

        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(23, 138, 64, 0.2)');
        gradient.addColorStop(1, 'rgba(23, 138, 64, 0)');

        window.myMarketChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?= $fechasStr ?>],
                datasets: [{
                    data: [<?= $preciosStr ?>],
                    borderColor: '#178A40',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false, callbacks: { label: (ctx) => ' u$s ' + ctx.parsed.y.toFixed(2) } }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f0f0f0' }, suggestedMin: Math.min(...[<?= $preciosStr ?>]) * 0.98, suggestedMax: Math.max(...[<?= $preciosStr ?>]) * 1.02 }
                }
            }
        });
    }
    renderMarketChart();
})();
</script>
