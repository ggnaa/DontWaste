<?php

// 1. Recibimos los datos del formulario (por ahora sin validaciones complejas)
$tipo = $_POST['tipo'] ?? 'gasto';
$monto = $_POST['monto'] ?? 0;
$categoria = $_POST['categoria'] ?? 'Varios';
$descripcion = $_POST['descripcion'] ?? '';

// Si el usuario no escribió descripción, usamos la categoría como título
$titulo = ($descripcion !== '') ? $descripcion : $categoria;

// 2. Formateamos la plata y los colores según el tipo
// number_format pone los puntos de miles y comas de decimales
$montoFormateado = number_format((float)$monto, 2, ',', '.'); 
$claseMonto = ($tipo === 'ingreso') ? 'positive' : 'negative';
$signo = ($tipo === 'ingreso') ? '+ $' : '- $';

// Simulamos un retraso de medio segundo para que parezca una carga real a un servidor
usleep(500000); 

// 3. Imprimimos el HTML crudo. HTMX agarrará esto y lo inyectará en la lista.
?>
<li>
    <div class="history-info">
        <strong><?= htmlspecialchars($titulo) ?></strong>
        <span><?= htmlspecialchars($categoria) ?></span>
    </div>
    <div class="history-amount <?= $claseMonto ?>">
        <?= $signo ?> <?= $montoFormateado ?>
    </div>
</li>
