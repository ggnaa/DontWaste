<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$plataformaBorrar = $_POST['plataforma'] ?? '';

if (isset($_SESSION['suscripciones'])) {
	foreach($_SESSION['suscripciones'] as $index => $sub){
		if($sub['plataforma'] === $plataformaBorrar) {
			unset($_SESSION['suscripciones'][$index]);
		}
	}

	$_SESSION['suscripciones'] = array_values($_SESSION['suscripciones']);
}

include '../components/suscripciones_content.php';
?>
