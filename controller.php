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
	case 'login_user':
		echo login_user($_POST['username'], $_POST['password'], $_POST['application_token']);
		break;
	case 'logout_user':
		echo logout_user($_POST['token'], $_POST['application_token']);
		break;
	case 'register_application':
		echo register_application($_POST['name'], $_POST['developer'], $_POST['mail']);
		break;
	case 'register_user':
		echo register_user($_POST['name'], $_POST['username'], $_POST['mail'], $_POST['cellphone'], $_POST['password'], $_POST['kind']);
		break;
	case 'verify_user_session':
		echo verify_user_session($_POST['token'], $_POST['application_token']);
		break;
	case 'deactivate_user':
		echo deactivate_user($_POST['username'], $_POST['application_token']);
		break;
	case 'activate_user':
		echo activate_user($_POST['username'], $_POST['application_token']);
		break;
	default:
		echo json_encode(0);
		break;
}



?>