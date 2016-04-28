<?php 
	class User{

		public $id;
		public $name;
		public $mail;
		public $cellphone;
		public $active;
		public $kind;

		public static function create($id, $name, $mail, $cellphone, $active, $kind){
			$object = new User;
			$object->id = $id;
			$object->name = $name;
			$object->mail = $mail;
			$object->cellphone = $cellphone;
			$object->active = $active;
			$object->kind = $kind;
			return $object;
		}
	}
?>