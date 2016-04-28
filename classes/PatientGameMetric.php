<?php 
	class PatientGameMetric{

		public $id;
		public $id_game;
		public $id_patient;
		public $patient_name;
		public $game_name;
		public $metric;
		public $value;
		public $date;

		public static function create($id, $id_game, $id_patient, $patient_name, $game_name, $metric, $value, $date){
			$object = new PatientGameMetric;
			$object->id = $id;
			$object->id_game = $id_game;
			$object->id_patient = $id_patient;
			$object->patient_name = $patient_name;
			$object->game_name = $game_name;
			$object->metric = $metric;
			$object->value = $value;
			$object->date = $date;
			return $object;
		}
	}
?>