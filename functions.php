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

	/**
		If metric is time value should be in seconds.
		Date has to be as string in the following format:
		YYYY-MM-DD H:MM:SS.mmm
		example: 2018-03-22 12:02:38.000
	*/
	function add_game_patient_metric($metric, $id_patient, $game_token, $date, $value){
		if($date == "" or $metric == "" or $value == ""){
			return json_encode(RegisterResult::create("Invalid arguments for game metric", false, "")); 
		}

		if(!id_patient_exists($id_patient)){
			return json_encode(RegisterResult::create("Patient id doesn't exist", false, ""));
		}

		$game_id = getGameId($game_token);

		if($game_id == null){
			return json_encode(RegisterResult::create("Game token invalid", false, ""));
		}

		$metric = strtoupper($metric);

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO game_metrics (id,metric,value,id_patient, date, id_game) VALUES(?,?,?,?,?,?)";
			$q = $pdo->prepare($sql);
			$result = $q -> execute(array(null, $metric, $value, $id_patient, $date, $game_id));
			Database::disconnect();
		}
		catch(PDOException $e){
			return json_encode(RegisterResult::create("Error in the server when registering game", false, ""));
		}

		return json_encode(RegisterResult::create("Game metric registered successfuly", true, ""));

	}

	function getGameId($token){
		if(strlen($token) != 40){
			return null;
		}
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id from games where token = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($token));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();
		if($data == null){
			return null;
		}
		return $data['id'];
	}

	function get_all_games($applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT id, name, developer, mail FROM games";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}
		
		$array = [];
		foreach ($data as $key) {
			array_push($array, Game::create($key['id'], $key['name'], $key['developer'], $key['mail']));
		}

		return json_encode(ObjectResult::create("Games fetched correctly", true, $array));
	}

	function get_all_patient_game_metrics($id_patient, $id_game, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_patient_exists($id_patient)){
			return json_encode(ObjectResult::create("Patient id doesn't exist", false, null));
		}

		if(!id_game_exists($id_game)){
			return json_encode(ObjectResult::create("Game doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT gm.id as id, id_game, id_patient, p.name as patient_name, g.name as game_name, metric, value, 
					date from game_metrics as gm, patient as p, games as g WHERE p.id = id_patient and g.id = id_game and 
					id_patient = '$id_patient' and id_game = '$id_game' order by id_game asc, metric asc, date asc";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, PatientGameMetric::create($key['id'], $key['id_game'], $key['id_patient'], $key['patient_name'], $key['game_name'],$key['metric'], $key['value'], $key['date']));
		}

		return json_encode(ObjectResult::create("Games fetched correctly", true, $array));

	}

	function id_game_exists($id_game){
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT id from games where id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id_game));
		$data = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();

		if($data == null){
			return false;
		}

		return true;
	}

	function get_all_patient_games($id_patient, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_patient_exists($id_patient)){
			return json_encode(ObjectResult::create("Patient id doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT g.id as id, name, developer, mail FROM games as g, game_metrics where id_patient = '$id_patient'
					and id_game = g.id";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, Game::create($key['id'], $key['name'], $key['developer'], $key['mail']));
		}

		return json_encode(ObjectResult::create("Games fetched correctly", true, $array));
	}

	function get_patient_doctors($id_patient, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_patient_exists($id_patient)){
			return json_encode(ObjectResult::create("Patient id doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT u.id as id, u.name as name, mail, cellphone, u.active as active, kind
					from user as u, patient as p, patient_doctor where p.id = '$id_patient' and 
					p.id = id_patient and u.id = id_doctor";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, User::create($key['id'], $key['name'], $key['mail'], $key['cellphone'], $key['active'], $key['kind']));
		}

		return json_encode(ObjectResult::create("Doctors fetched correctly", true, $array));
	}

	function get_patient_parents($id_patient, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_patient_exists($id_patient)){
			return json_encode(ObjectResult::create("Patient id doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT u.id as id, u.name as name, mail, cellphone, u.active as active, kind
					from user as u, patient as p, patient_parent where p.id = '$id_patient' and 
					p.id = id_patient and u.id = id_parent";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, User::create($key['id'], $key['name'], $key['mail'], $key['cellphone'], $key['active'], $key['kind']));
		}

		return json_encode(ObjectResult::create("Parents fetched correctly", true, $array));
	}

	function get_all_doctor_patients($id_doctor, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_doctor_exists($id_doctor)){
			return json_encode(ObjectResult::create("Doctor id doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT p.id as id, p.name as name, p.username as username, p.active as active from patient as p, user as u, patient_doctor where u.id = '$id_doctor' and u.id = id_doctor and p.id = id_patient;";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, Patient::create($key['id'], $key['name'], $key['username'], $key['active']));
		}

		return json_encode(ObjectResult::create("Patients fetched correctly", true, $array));

	}

	function get_all_parent_patients($id_parent, $applicationToken){
		if(!verifyApplicationToken($applicationToken)){
			return json_encode(ObjectResult::create("Application not authorized to do requests, invalid token", false, null));
		}

		if(!id_parent_exists($id_parent)){
			return json_encode(ObjectResult::create("Parent id doesn't exist", false, null));
		}

		try{
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "SELECT p.id as id, p.name as name, p.username as username, p.active as active from patient as p, user as u, patient_parent where u.id = '$id_parent' and u.id = id_parent and p.id = id_patient;";
			$data = $pdo->query($sql);
			Database::disconnect();
		} catch(PDOException $e){
			return json_encode(ObjectResult::create("Error in server: ".$e->getMessage(), false, null));
		}

		$array = [];
		foreach ($data as $key) {
			array_push($array, Patient::create($key['id'], $key['name'], $key['username'], $key['active']));
		}

		return json_encode(ObjectResult::create("Patients fetched correctly", true, $array));

	}
?>