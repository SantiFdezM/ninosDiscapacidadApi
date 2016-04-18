<?php
	include 'database.php';

	include 'functionsSession.php';	

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
?>