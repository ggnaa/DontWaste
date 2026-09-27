<?php
session_start();

// Leemos el nuevo nombre o ponemos uno por defecto si viene vacío
$nuevoNombre = trim($_POST['nombre_usuario'] ?? '');
if ($nuevoNombre === '') {
    $nuevoNombre = 'Usuario Demo';
}

// Actualizamos la memoria
$_SESSION['nombre_usuario'] = $nuevoNombre;
?>

<!-- HTMX inyectará esto automáticamente en el elemento con el mismo ID -->
<span class="avatar-text" id="nombre-usuario-nav" hx-swap-oob="true"><?= htmlspecialchars($nuevoNombre) ?></span>
<script>alert('¡Perfil actualizado con éxito!');</script>
