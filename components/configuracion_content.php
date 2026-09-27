<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombreActual = $_SESSION['nombre_usuario'] ?? 'Usuario Demo';
?>

<div class="panel" style="max-width: 600px; margin: 40px auto; padding: 40px;">
    <h3 style="margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 15px;">Perfil y Configuración</h3>
    
    <!-- Formulario activo con HTMX -->
    <form hx-post="processors/guardar_perfil.php" 
          hx-swap="none" 
          style="display: flex; flex-direction: column; gap: 20px; margin-top: 25px;">
        
        <div class="form-group">
            <label style="font-size: 13px; color: #666; font-weight: bold;">Nombre de Usuario</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nombre_usuario" value="<?= htmlspecialchars($nombreActual) ?>" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; flex: 1; outline: none;">
                <button type="submit" style="background-color: #178A40; color: white; border: none; padding: 0 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">Guardar</button>
            </div>
        </div>

        <div class="form-group">
            <label style="font-size: 13px; color: #666; font-weight: bold;">Moneda Principal</label>
            <select disabled style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f9f9f9; color: #666;">
                <option>ARS ($) - Pesos Argentinos</option>
                <option>USD (u$s) - Dólares</option>
            </select>
        </div>
    </form>

    <!-- Zona de Peligro -->
    <div style="margin-top: 40px; border: 1px solid #ffcccc; background: #fff5f5; padding: 20px; border-radius: 8px;">
        <h4 style="color: #E50914; margin-top: 0; margin-bottom: 10px;">Zona de Peligro</h4>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
            Esto eliminará permanentemente todas tus transacciones, tarjetas, inversiones y suscripciones de la memoria actual.
        </p>
        <a href="index.php?action=reset" style="display: inline-block; background-color: #E50914; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; font-size: 14px; transition: 0.2s;">
            Borrar todos mis datos
        </a>
    </div>
</div>
