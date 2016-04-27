<?php
	include 'database.php';

	include 'functionsSession.php';
	include 'classes';	

	function testConnection(){
		try{
			Database::connect();
		}
		catch(PDOException $e){
			$array = [];
			$array['mensaje'] = $e->getMessage();
			$array['status'] = "400";
			$array['result'] = false;
			return json_encode($array);
		}
		Database::disconnect();
		$array = [];
		$array['mensaje'] = "Conexion exitosa";
		$array['status'] = "200";
		$array['result'] = true;
		return json_encode($array);
	}

	function getToken(){
		$token = "";
		for($i = 0; $i < 10; $i++){
			$token .= (string)rand(65,90);
		}
		return sha1($token);
	}

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

	function login($user, $password, $applicationToken){
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
		$res = LoginResult::create("Login successful", true, $token, $data['kind'], $data['name']);
		$id = $data['id'];
	    Database::disconnect();
	}
	catch(PDOException $e){
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}
	
    $pdo = Database::connect();

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
?>