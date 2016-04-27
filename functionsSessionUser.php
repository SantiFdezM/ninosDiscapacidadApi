<?php 
	/* Este archivo contiene las funciones que controlan la sesion de un usuario, login, logout, verifySession */

	function verifyApplicationToken($token){
		if(strlen($token) != 40){
			return false;
		}
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id from applications where token = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($token));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();
		if($data == null){
			return false;
		}
		return true;
	}

	function login_user($user, $password, $applicationToken){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$res = null;
	$token = "";
	$id = null;
	$pdo = Database::connect();

	try{
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id, username, password, active, kind, name from user where username = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($user));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		
		if($data == null){
			Database::disconnect(); 
			return json_encode(LoginResult::create("Invalid username or password", false, "", -1, ""));
		}
		
		if($user != $data['username']){
			Database::disconnect();
			return json_encode(LoginResult::create("Invalid username or password", false, "", -1, ""));
		}
		
		if($data['password'] != sha1($password) || $data['active'] == 0){
			Database::disconnect();
			return json_encode(LoginResult::create("Invalid username or password", false, "", -1, ""));
		}

		$token = getToken();
		$id = $data['id'];
		$res = LoginResult::create("Login successful", true, $token, $data['kind'], $data['name'], $id);
	    Database::disconnect();
	}
	catch(PDOException $e){
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}
	
    $pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	try{

		if($id == null){
			return json_encode(LoginResult::createId("There was an error on the server, try again", false, "", -1, ""));
		}

		$pdo->exec("UPDATE user set token = '$token' where id = '$id'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	if($res == null){
		return json_encode(LoginResult::create("There was an error on the server, try again", false, "", -1, ""));
	}

	return json_encode($res);
}

function register_application($name, $developer, $mail){
	$token = getToken();
	try{
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO applications (id,name,developer,mail, token) VALUES(?,?,?,?,?)";
		$q = $pdo->prepare($sql);
		$result = $q -> execute(array(null, $name, $developer, $mail, $token));
		Database::disconnect();
	}
	catch(PDOException $e){
		return json_encode(RegisterResult::create("Error in the server when registering application", false, ""));
	}
	return json_encode(RegisterResult::create("The application was registered successful", true, $token));
}

/** 
	Kind values:
	- 1 for doctor 
	- 2 for patient parent
*/
function register_user($name, $username, $mail, $cellphone, $password, $kind){
	try{
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO user (id,name,username,mail,cellphone,password,active,token,kind) VALUES(?,?,?,?,?,?,?,?,?)";
		$q = $pdo->prepare($sql);
		$result = $q -> execute(array(null, $name, $username, $mail, $cellphone, sha1($password), 1, "", $kind));
		Database::disconnect();
	}
	catch(PDOException $e){
		return json_encode(RegisterResult::create("Error in the server when registering user", false, ""));
	}
	return json_encode(RegisterResult::create("The user was registered successful", true, ""));
}

function verify_user_session($token, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	if(strlen($token) != 40){
		return json_encode(LoginResult::create("Invalid user token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$res = null;

	try{
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id, active, kind, name from user where token = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($token));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();

		if($data == null){
			return json_encode(LoginResult::create("The user session token doesn't exists", false, "", -1, ""));
		}

		if($data['active'] == 0){
			return json_encode(LoginResult::create("The user is no longer active", false, "", -1, ""));
		}

		$res = LoginResult::createId("The session is valid", true, $token, $data['kind'], $data['name'], $data['id']);
	}
	catch(PDOException $e){
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode($res);
}

function logout_user($token, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	if(strlen($token) != 40){
		return json_encode(LoginResult::create("Invalid user token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE user set token = '' where token = '$token'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(LoginResult::create("Logout from user successful", true, "", -1, ""));
}

function deactivate_user($username, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE user set active = 0 where username = '$username'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(LoginResult::create("User was deactivated correctly", true, "", -1, ""));
}

function activate_user($username, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE user set active = 1 where username = '$username'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(LoginResult::create("User activated correctly", true, "", -1, ""));
}

	
?>