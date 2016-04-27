<?php
	include_once 'database.php';

	include_once 'functionsSessionUser.php';

	include_once 'functionsPatient.php';
	
	foreach (glob("classes/*.php") as $filename)
	{
	    include_once $filename;
	}

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

	function register_game($name, $developer, $mail){
		$token = getToken();
		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO games (id,name,developer,mail, token) VALUES(?,?,?,?,?)";
			$q = $pdo->prepare($sql);
			$result = $q -> execute(array(null, $name, $developer, $mail, $token));
			Database::disconnect();
		}
		catch(PDOException $e){
			return json_encode(RegisterResult::create("Error in the server when registering game", false, ""));
		}
		return json_encode(RegisterResult::create("The game was registered successful", true, $token));
	}
?>