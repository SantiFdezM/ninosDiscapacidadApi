<?php 

include 'functions.php';

if(empty($_POST)){
	echo 'error';
	exit();
}

$funcion = $_POST['funcion'];

switch ($funcion) {
	case 'testConnection':
		echo testConnection();
		break;
	case 'login':
		//echo login($_POST['nombre_usuario'], $_POST['password'], $_POST['nombre_dispositivo'], $_POST['os'], $_POST['guid']);
		break;
	case 'logout':
		//echo logout($_POST['token']);
		break;
	// case 'registro':
	// 	echo registro($_POST['nombre'], $_POST['apellido'], $_POST['email'], $_POST['nombre_usuario'], $_POST['password'], $_POST['nombre_dispositivo'], $_POST['os'], $_POST['guid']);
	// 	break;
	default:
		echo json_encode(0);
		break;
}



?>