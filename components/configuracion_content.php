<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombreActual = $_SESSION['nombre_usuario'] ?? 'Usuario Demo';
?>

<div class="panel config-panel">
    <h3 class="config-title">Perfil y Configuración</h3>
    
    <form hx-post="processors/guardar_perfil.php" hx-swap="none" class="config-form">
        
        <div class="form-group config-group">
            <label>Nombre de Usuario</label>
            <div class="config-input-row">
                <input type="text" name="nombre_usuario" value="<?= htmlspecialchars($nombreActual) ?>" required maxlength="12" class="config-input">
                <button type="submit" class="btn-primary btn-save">Guardar</button>
            </div>
        </div>

        <div class="form-group config-group">
            <label>Moneda Principal</label>
            <select disabled class="config-input disabled-input">
                <option>ARS ($) - Pesos Argentinos</option>
                <option>USD (u$s) - Dólares</option>
            </select>
        </div>
    </form>

    <div class="danger-zone">
        <h4 class="danger-title">Zona de Peligro</h4>
        <p class="danger-desc">
            Esto eliminará permanentemente todas tus transacciones, tarjetas, inversiones y suscripciones de la memoria actual.
        </p>
        <a href="index.php?action=reset" class="btn-danger">
            Borrar todos mis datos
        </a>
    </div>
</div>
