<?php
if(session_status() === PHP_SESSION_NONE){
	session_start();
}

$plataforma = htmlspecialchars($_POST['plataforma'] ?? 'Netflix', ENT_QUOTES, 'UTF-8');
$dia = intval($_POST['dia'] ?? 1);
$usd = floatval($_POST['usd'] ?? 0);

if(!isset($_SESSION['suscripciones'])){
	$_SESSION['suscripciones'] = [];
}

$existe = false;

foreach ($_SESSION['suscripciones'] as $index => $sub){
	if($sub['plataforma'] === $plataforma){
		$_SESSION['suscripciones'][$index]['dia'] = $dia;
		$_SESSION['suscripciones'][$index]['usd'] = $usd;
		$existe = true;
		break;
	}
}

if(!$existe){
	$_SESSION['suscripciones'][] = [
		'plataforma' => $plataforma,
		'dia' => $dia,
		'usd' => $usd
	];	
}

include '../components/suscripciones_content.php';
exit;
?>










