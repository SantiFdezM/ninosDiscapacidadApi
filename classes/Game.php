<?php 
	class Game{

		public $id;
		public $name;
		public $developer;
		public $mail;

		public static function create($id, $name, $developer, $mail){
			$object = new Game;
			$object->id = $id;
			$object->name = $name;
			$object->developer = $developer;
			$object->mail = $mail;
			return $object;
		}
	}
?>