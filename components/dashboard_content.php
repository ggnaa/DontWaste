<div class="summary-cards">
    <div class="card">
        <h3>Gastado este mes</h3>
        <p class="amount"></p>
    </div>
    <div class="card">
        <h3>Ingresos del mes</h3>
        <p class="amount"></p>
    </div>
    <div class="card">
        <h3>Ahorro proyectado</h3>
        <p class="amount positive"></p>
    </div>
</div>

<!-- Div de paneles -->
<div class="dashboard-grid">
    
    <!-- Panel izquierdo, rueda de gastos y leyendas -->
    <div class="panel chart-panel">
        <h3>Gastos por categoría</h3>
        <div class="chart-container">
            <canvas id="expenseChart"></canvas>
        </div>
        <!-- Leyendas personalizadas -->
        <div class="custom-legend">
            <div class="legend-item"><span class="color-box" style="background: #FFE6AA;"></span> Gastronomía</div>
            <div class="legend-item"><span class="color-box" style="background: #9AD0F5;"></span> Supermercado</div>
            <div class="legend-item"><span class="color-box" style="background: #E2D1F9;"></span> Vivienda</div>
           	<div class="legend-item"><span class="color-box" style="background: #C9CBFF;"></span> Servicios</div>
            <div class="legend-item"><span class="color-box" style="background: #FCE68A;"></span> Transporte</div>
            <div class="legend-item"><span class="color-box" style="background: #FFB1C1;"></span> Ocio</div>
            <div class="legend-item"><span class="color-box" style="background: #B4E4B4;"></span> Inversiones</div>
        	<div class="legend-item"><span class="color-box" style="background: #D3D3D3;"></span> Varios</div>
    	</div>
    </div>

    <!-- Panel derecho, historial de movimientos -->
    <div class="panel history-panel">
        <h3>Últimos 10 movimientos</h3>
        <ul class="history-list">
            <li>
                <div class="history-info">
                    <strong>Netflix</strong>
                    <span>Suscripciones</span>
                </div>
                <div class="history-amount negative">- $ 6,500</div>
            </li>
            <li>
                <div class="history-info">
                    <strong>Supermercado</strong>
                    <span>Servicios</span>
                </div>
                <div class="history-amount negative">- $ 18,200</div>
            </li>
            <li>
                <div class="history-info">
                    <strong>Sueldo</strong>
                    <span>Ingresos</span>
                </div>
                <div class="history-amount positive">+ $ 120,500</div>
            </li>
        </ul>
    </div>
</div>

<!-- Script para inicializar el gráfico de chart.js -->
<script>
(function() {
    function renderChart() {
        // 1. Verificamos si Chart.js ya terminó de cargar
        if (typeof Chart === 'undefined') {
            setTimeout(renderChart, 50); // Si no cargó, vuelve a intentar en 50 milisegundos
            return;
        }

        // 2. Si ya cargó, buscamos el canvas
        const canvas = document.getElementById('expenseChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        
        // 3. Destruimos el gráfico anterior si existe (clave para HTMX)
        if (window.myPieChart) {
            window.myPieChart.destroy();
        }

        // 4. Dibujamos la rueda
        window.myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Gastronomía', 'Supermercado', 'Vivienda', 'Servicios', 'Transporte', 'Ocio', 'Inversiones', 'Varios'],
                datasets: [{
                    data: [15, 20, 30, 10, 8, 7, 5, 5], 
                    backgroundColor: [
                        '#FFE6AA', '#9AD0F5', '#E2D1F9', '#C9CBFF', 
                        '#FCE68A', '#FFB1C1', '#B4E4B4', '#D3D3D3'  
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Disparamos la función
    renderChart();
})();
</script>
