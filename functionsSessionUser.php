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

	$user = strtolower($user);

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
		$res = LoginResult::createId("Login successful", true, $token, $data['kind'], $data['name'], $id);
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
			return json_encode(LoginResult::create("There was an error on the server, try again", false, "", -1, ""));
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

/** 
	Kind values:
	- 1 for doctor 
	- 2 for patient parent
*/
function register_user($name, $username, $mail, $cellphone, $password, $kind){
	$username = strtolower($username);

	$verifyResult = verify_user_username_existsI($username);
	
	if($verifyResult){
		return json_encode(RegisterResult::create("Error the username ".$username." is already taken", false, ""));
	}

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

	return json_encode(LoginResult::createId("The user was registered successful", true, "", $kind, $name, getUserId($username)));
}

function update_user($id, $name, $username, $mail, $cellphone, $password, $applicationToken){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(RegisterResult::create("Application is not permited to do requests, invalid application token", false, ""));
	}

	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE user set name = '$name', username = '$username', mail = '$mail', cellphone = '$cellphone', password = sha1('$password') where id = '$id'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(RegisterResult::create("Error in the server when updating user: ".$e->getMessage(), false, ""));
	}
	return json_encode(RegisterResult::create("The user was updated successful", true, ""));
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
		$sql = "SELECT id, active, kind, name from user where token = ? and active = 1";
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

	return json_encode(RegisterResult::create("Logout from user successful", true, ""));
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

	return json_encode(RegisterResult::create("User was deactivated correctly", true, ""));
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

	return json_encode(RegisterResult::create("User activated correctly", true, ""));
}

function verify_user_username_exists($username, $applicationToken){
	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from user where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return json_encode(VerifyResult::create("The username doesn't exist", false, $username));
	}

	return json_encode(VerifyResult::create("The username already exists", true, $username));
}

function verify_user_username_existsI($username){

	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from user where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return false;
	}

	return true;
}

function getUserId($username){

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from user where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return null;
	}

	return $data['id'];
}


	
?>