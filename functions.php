<?php
	include 'database.php';

	include 'functionsSessionUser.php';
	
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
?>