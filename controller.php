<?php 

include 'functions.php';

if(empty($_POST)){
	echo 'error';
	exit();
}

if(!isset($_POST['funcion'])){
	echo json_encode(0);
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
	case 'update_user':
		echo update_user($_POST['id'],$_POST['name'], $_POST['username'], $_POST['mail'], $_POST['cellphone'], $_POST['password'], $_POST['application_token']);
		break;
	case 'verify_user_username_exists':
		echo verify_user_username_exists($_POST['username'], $_POST['application_token']);
		break;
	case 'register_application':
		echo register_application($_POST['name'], $_POST['developer'], $_POST['mail']);
		break;
	case 'register_game':
		echo register_game($_POST['name'], $_POST['developer'], $_POST['mail']);
		break;
	case 'register_patient':
		echo register_patient($_POST['name'], $_POST['username'], $_POST['password'], $_POST['application_token'], $_POST['id_doctor']);
		break;
	case 'login_patient':
		echo login_patient($_POST['username'], $_POST['password'], $_POST['game_token']);
		break;
	case 'update_patient':
		echo update_patient($_POST['id'],$_POST['name'], $_POST['username'], $_POST['password'], $_POST['application_token']);
		break;
	case 'verify_patient_session':
		echo verify_patient_session($_POST['token'], $_POST['game_token']);
		break;
	case 'logout_patient':
		echo logout_patient($_POST['token'], $_POST['game_token']);
		break;
	case 'deactivate_patient':
		echo deactivate_patient($_POST['username'], $_POST['application_token']);
		break;
	case 'activate_patient':
		echo activate_patient($_POST['username'], $_POST['application_token']);
		break;
	case 'verify_patient_username_exists':
		echo verify_patient_username_exists($_POST['username'], $_POST['application_token']);
		break;
	case 'add_patient_parent':
		echo add_patient_parent($_POST['id_patient'], $_POST['id_parent'], $_POST['application_token']);
		break;
	case 'add_patient_doctor':
		echo add_patient_doctor($_POST['id_patient'], $_POST['id_doctor'], $_POST['application_token']);
		break;
	case 'add_game_patient_metric':
		echo add_game_patient_metric($_POST['metric'], $_POST['id_patient'], $_POST['game_token'], $_POST['date'], $_POST['value']);
		break;
	case 'get_all_games':
		echo get_all_games($_POST['application_token']);
		break;
	case 'get_all_patient_game_metrics':
		echo get_all_patient_game_metrics($_POST['id_patient'],$_POST['id_game'],$_POST['application_token']);
		break;
	case 'get_all_patient_games':
		echo get_all_patient_games($_POST['id_patient'],$_POST['application_token']);
		break;
	case 'get_patient_doctors':
		echo get_patient_doctors($_POST['id_patient'],$_POST['application_token']);
		break;
	case 'get_patient_parents':
		echo get_patient_parents($_POST['id_patient'],$_POST['application_token']);
		break;
	case 'get_all_doctor_patients':
		echo get_all_doctor_patients($_POST['id_doctor'],$_POST['application_token']);
		break;
	case 'get_all_parent_patients':
		echo get_all_parent_patients($_POST['id_parent'],$_POST['application_token']);
		break;
	default:
		echo json_encode(0);
		break;
}



?>