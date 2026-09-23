<?php
// Leemos los totales acumulados en la memoria del servidor (o 0 si está vacío)
$gastos = $_SESSION['gastos'] ?? 0;
$ingresos = $_SESSION['ingresos'] ?? 0;
$ahorro = $ingresos - $gastos;

// Formateamos los números
$gastosFmt = '$ ' . number_format($gastos, 0, ',', '.');
$ingresosFmt = '$ ' . number_format($ingresos, 0, ',', '.');
$claseAhorro = ($ahorro >= 0) ? 'positive' : 'negative';
$signoAhorro = ($ahorro >= 0) ? '+ $' : '- $';
$ahorroFmt = $signoAhorro . ' ' . number_format(abs($ahorro), 0, ',', '.');


// Inicializamos las categorías manteniendo el orden exacto de los colores
if (!isset($_SESSION['categorias'])) {
    $_SESSION['categorias'] = [
        'Gastronomía' => 0, 'Supermercado' => 0, 'Vivienda' => 0, 
        'Servicios' => 0, 'Transporte' => 0, 'Ocio' => 0, 
        'Inversiones' => 0, 'Varios' => 0
    ];
}

// Convertimos el array en una lista separada por comas (ej: "0,0,1500,0...")
$datosGrafico = implode(',', array_values($_SESSION['categorias']));
?>

<div class="summary-cards">
    <div class="card">
        <h3>Gastado este mes</h3>
        <p id="total-gastado" class="amount"><?= $gastosFmt?></p>
    </div>
    <div class="card">
        <h3>Ingresos del mes</h3>
        <p id="total-ingresos" class="amount"><?= $ingresosFmt ?></p>
    </div>
    <div class="card">
        <h3>Ahorro proyectado</h3>
        <p id="total-ahorro" class="amount <?= $claseAhorro?>"><?= $ahorroFmt ?></p>
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
            <div class="legend-item"><span class="color-box" style="background: #FFB067;"></span> Gastronomía</div>
            <div class="legend-item"><span class="color-box" style="background: #9AD0F5;"></span> Supermercado</div>
            <div class="legend-item"><span class="color-box" style="background: #E2D1F9;"></span> Vivienda</div>
           	<div class="legend-item"><span class="color-box" style="background: #82CCDD;"></span> Servicios</div>
            <div class="legend-item"><span class="color-box" style="background: #FCE68A;"></span> Transporte</div>
            <div class="legend-item"><span class="color-box" style="background: #FFB1C1;"></span> Ocio</div>
            <div class="legend-item"><span class="color-box" style="background: #B4E4B4;"></span> Inversiones</div>
        	<div class="legend-item"><span class="color-box" style="background: #D3D3D3;"></span> Varios</div>
    	</div>
    </div>

    <!-- Panel derecho, historial de movimientos -->
    <div class="panel history-panel">
        <h3>Últimos movimientos</h3>
        <ul class="history-list">
         
        </ul>
    </div>
</div>

<!-- Contenedor invisible para que HTMX inyecte los scripts de actualización -->
<div id="chart-updater"></div>

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

		// Limpieza forzada: Si el mouse sale del recuadro del canvas, apagamos todo.
		canvas.addEventListener('mouseout', () => {
		    document.querySelectorAll('.legend-item').forEach(el => el.classList.remove('highlight'));
		});
        
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
                    data: [<?= $datosGrafico ?>], 
                    backgroundColor: [
                        '#FFB067', '#9AD0F5', '#E2D1F9', '#82CCDD', 
                        '#FCE68A', '#FFB1C1', '#B4E4B4', '#D3D3D3'  
                    ],
                    borderWidth: 1,
                    borderColor: '#ffffff',
					hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

				layout:{
					padding: 10
				},
				// Evento al pasar el mouse sobre la rueda
				onHover: (event, chartElement) => {
				    // Limpiamos cualquier resaltado previo
				    document.querySelectorAll('.legend-item').forEach(el => el.classList.remove('highlight'));
				    
				    // Si estamos parados sobre una porción de color
				    if (chartElement.length > 0) {
				        const index = chartElement[0].index; // Obtenemos qué porción es (0 a 7)
				        const legendItems = document.querySelectorAll('.legend-item');
				        
				        // Resaltamos la leyenda correspondiente
				        if (legendItems[index]) {
				            legendItems[index].classList.add('highlight');
				        }
				    }
				},
				
				// Evento al sacar el mouse de la rueda por completo
				onLeave: () => {
				    document.querySelectorAll('.legend-item').forEach(el => el.classList.remove('highlight'));
				},
                
                plugins: { legend: { display: false },
					tooltip: {
						callbacks: {
					    label: function(context) {
					    	// Sumamos todos los valores del gráfico para obtener el 100%
					    	const total = context.dataset.data.reduce((a, b) => a + b, 0);
					    	const valor = context.raw;
					    	
					    	// Calculamos el porcentaje
					    	const porcentaje = total > 0 ? ((valor / total) * 100).toFixed(1) + '%' : '0%';
					    	
					    	// Le damos formato de moneda al número (Ej: $ 450.000,00)
					    	const valorFormateado = new Intl.NumberFormat('es-AR', { 
					    	    style: 'currency', 
					    	    currency: 'ARS' 
					    	}).format(valor);
			             	return ` ${valorFormateado} (${porcentaje})`;
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
