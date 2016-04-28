<?php 
	function verifyGameToken($token){
		if(strlen($token) != 40){
			return false;
		}
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id from games where token = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($token));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();
		if($data == null){
			return false;
		}
		return true;
	}

	function login_patient($user, $password, $gameToken){
	if(!verifyGameToken($gameToken)){
		return json_encode(LoginResult::create("Game is not permited to do requests, invalid game token", false, "", -1, ""));
	}

	$user = strtolower($user);

	$res = null;
	$token = "";
	$id = null;
	$pdo = Database::connect();

	try{
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id, username, password, active, name from patient where username = ?";
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
		$res = LoginResult::createId("Login successful", true, $token, -1, $data['name'], $id);
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

		$pdo->exec("UPDATE patient set token = '$token' where id = '$id'");
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

function getPatientId($username){
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from patient where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
	return $data['id'];
}

function register_patient($name, $username, $password, $applicationToken, $doctor_id){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$username = strtolower($username);

	$verifyResult = verify_patient_username_existsI($username);

	if($verifyResult){
		return json_encode(RegisterResult::create("Error the username ".$username." is already taken", false, ""));
	}

	if(!id_doctor_exists($doctor_id)){
		return json_encode(RegisterResult::create("Doctor id doesn't exist", false, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("INSERT INTO patient (id,name,username,password,active,token) VALUES(null,'$name','$username',sha1('$password'),1,'')");
		$id = getPatientId($username);
		$pdo->exec("INSERT INTO patient_doctor (id,id_patient, id_doctor) VALUES(null,'$id', '$doctor_id')");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(RegisterResult::create("Error in the server when registering patient: ".$e->getMessage(), false, ""));
	}

	return json_encode(RegisterResult::create("The patient was registered successful", true, ""));
}

function update_patient($id, $name, $username, $password, $applicationToken){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(RegisterResult::create("Application is not permited to do requests, invalid application token", false, ""));
	}

	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE patient set name = '$name', username = '$username', password = sha1('$password') where id = '$id'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(RegisterResult::create("Error in the server when updating patient: ".$e->getMessage(), false, ""));
	}
	return json_encode(RegisterResult::create("The patient was updated successful", true, ""));
}

function verify_patient_session($token, $gameToken){

	if(!verifyGameToken($gameToken)){
		return json_encode(LoginResult::create("Game is not permited to do requests, invalid game token", false, "", -1, ""));
	}

	if(strlen($token) != 40){
		return json_encode(LoginResult::create("Invalid patient token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$res = null;

	try{
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id, active, name from patient where token = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($token));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();

		if($data == null){
			return json_encode(LoginResult::create("The patient session token doesn't exists", false, "", -1, ""));
		}

		if($data['active'] == 0){
			return json_encode(LoginResult::create("The patient is no longer active", false, "", -1, ""));
		}

		$res = LoginResult::createId("The session is valid", true, $token, -1, $data['name'], $data['id']);
	}
	catch(PDOException $e){
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode($res);
}

function logout_patient($token, $gameToken){

	if(!verifyGameToken($gameToken)){
		return json_encode(LoginResult::create("Game is not permited to do requests, invalid game token", false, "", -1, ""));
	}

	if(strlen($token) != 40){
		return json_encode(LoginResult::create("Invalid patient token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE patient set token = '' where token = '$token'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(RegisterResult::create("Logout from patient successful", true, ""));
}

function deactivate_patient($username, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE patient set active = 0 where username = '$username'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(RegisterResult::create("Patient was deactivated correctly", true, ""));
}


function activate_patient($username, $applicationToken){

	if(!verifyApplicationToken($applicationToken)){
		return json_encode(LoginResult::create("Application is not permited to do requests, invalid application token", false, "", -1, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	
	try{
		$pdo->exec("UPDATE patient set active = 1 where username = '$username'");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(LoginResult::create("There was an error on the server, try again: ".$e->getMessage(), false, "", -1, ""));
	}

	return json_encode(RegisterResult::create("Patient activated correctly", true, ""));
}

function verify_patient_username_exists($username, $applicationToken){

	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from patient where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return json_encode(VerifyResult::create("The username doesn't exist", false, $username));
	}

	return json_encode(VerifyResult::create("The username already exists", true, $username));
}

function verify_patient_username_existsI($username){
	
	$username = strtolower($username);

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from patient where username = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($username));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return false;
	}

	return true;
}

function add_patient_parent($id_patient, $id_parent, $applicationToken){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(RegisterResult::create("Application is not permited to do requests, invalid application token", false, ""));
	}

	if(!id_patient_exists($id_patient)){
		return json_encode(RegisterResult::create("Patient id doesn't exist", false, ""));
	}

	if(!id_parent_exists($id_parent)){
		return json_encode(RegisterResult::create("Parent id doesn't exist", false, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	try{
		$pdo->exec("INSERT INTO patient_parent (id,id_patient, id_parent) VALUES(null,'$id_patient', '$id_parent')");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(RegisterResult::create("Error in the server when registering parent to patient: ".$e->getMessage(), false, ""));
	}

	return json_encode(RegisterResult::create("The parent was added to patient successfuly", true, ""));

}

function add_patient_doctor($id_patient, $id_doctor, $applicationToken){
	if(!verifyApplicationToken($applicationToken)){
		return json_encode(RegisterResult::create("Application is not permited to do requests, invalid application token", false, ""));
	}

	if(!id_patient_exists($id_patient)){
		return json_encode(RegisterResult::create("Patient id doesn't exist", false, ""));
	}

	if(!id_doctor_exists($id_doctor)){
		return json_encode(RegisterResult::create("Doctor id doesn't exist", false, ""));
	}

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->beginTransaction();
	try{
		$pdo->exec("INSERT INTO patient_doctor (id,id_patient, id_doctor) VALUES(null,'$id_patient', '$id_doctor')");
		$pdo->commit();
	}
	catch(PDOException $e){
		$pdo->rollback();
		return json_encode(RegisterResult::create("Error in the server when registering doctor to patient: ".$e->getMessage(), false, ""));
	}

	return json_encode(RegisterResult::create("The doctor was added to patient successfuly", true, ""));

}

function id_patient_exists($id_patient){
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from patient where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id_patient));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return false;
	}

	return true;
}

function id_parent_exists($id_parent){
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from user where id = ? and kind = 2";
	$q = $pdo->prepare($sql);
	$q->execute(array($id_parent));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return false;
	}

	return true;
}

function id_doctor_exists($id_doctor){
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT id from user where id = ? and kind = 1";
	$q = $pdo->prepare($sql);
	$q->execute(array($id_doctor));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();

	if($data == null){
		return false;
	}

	return true;
}

?>