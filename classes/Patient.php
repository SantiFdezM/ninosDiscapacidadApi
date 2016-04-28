<?php 
	class Patient{

		public $id;
		public $name;
		public $username;
		public $active;

		public static function create($id, $name, $username, $active){
			$object = new Patient;
			$object->id = $id;
			$object->name = $name;
			$object->username = $username;
			$object->active = $active;
			return $object;
		}
	}
?>