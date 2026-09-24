<?php
session_start();

$tarjetas = $_SESSION['tarjetas'] ?? [];
$deudaTotal = $_SESSION['deuda'] ?? 0;

$totalActivas = count($tarjetas);
$limiteTotal = array_sum(array_column($tarjetas, 'limite'));
?>

<div class="summary-cards">
    <div class="card">
        <h3>Tarjetas Activas</h3>
        <!-- ID para HTMX -->
        <p id="total-tarjetas" class="amount"><?= $totalActivas ?></p>
    </div>
    <div class="card">
        <h3>Límite de Crédito Total</h3>
        <!-- ID para HTMX -->
        <p id="limite-total" class="amount">$ <?= number_format($limiteTotal, 0, ',', '.') ?></p>
    </div>
    <div class="card">
        <h3>Deuda Pendiente (Tarjetas)</h3>
        <p id="deuda-total" class="amount negative"> $ <?= number_format($deudaTotal, 0, ',','.') ?></p>
    </div>
</div>

<div class="dashboard-grid" style="height: auto; min-height: 400px;">
    <div class="panel" style="flex: 1;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h3>Mis Tarjetas Vinculadas</h3>
            <!-- Botón conectado a Alpine.js para abrir el modal -->
            <button class="btn-primary" @click="isCardModalOpen = true" style="padding: 10px 20px;">+ Agregar Tarjeta</button>
        </div>
        
        <!-- Contenedor con ID donde HTMX inyectará las tarjetas nuevas -->
        <div class="tarjetas-list" id="lista-tarjetas">
            <?php if (empty($tarjetas)): ?>
                <p id="empty-tarjetas-msg" style="color: #888; font-size: 14px; width: 100%;">No tenés tarjetas registradas aún. Hacé clic en "Agregar Tarjeta" para comenzar.</p>
            <?php else: ?>
                <!-- Si el usuario presiona F5, dibujamos las tarjetas guardadas en sesión -->
                <?php foreach ($tarjetas as $t): ?>
                    <div class="credit-card <?= htmlspecialchars($t['marca']) ?>">
                        <div class="card-logo"><?= strtoupper($t['marca']) ?></div>
                        <div class="card-chip">
                            <div class="chip-line"></div>
                            <div class="chip-line"></div>
                            <div class="chip-line"></div>
                            <div class="chip-main"></div>
                        </div>
                        <div class="card-number">**** **** **** <?= htmlspecialchars($t['numeros']) ?></div>
                        <div class="card-details">
                            <div class="card-holder">
                                <span>Titular</span>
                                <p><?= htmlspecialchars($t['titular']) ?></p>
                            </div>
                            <div class="card-expires">
                                <span>Vence</span>
                                <p><?= htmlspecialchars($t['vencimiento']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
