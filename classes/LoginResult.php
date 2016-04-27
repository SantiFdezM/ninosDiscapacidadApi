<?php 
	class LoginResult{

		public $message;
		public $success;
		public $token;
		public $user_type;
		public $user_fullname;

		public static function create($message, $success, $token, $user_type, $user_fullname){
			$object = new LoginResult;
			$object->$message = $message;
			$object->$success = $success;
			$object->$token = $token;
			$object->$user_type = $user_type;
			$object->$user_fullname = $user_fullname;
			return $object;
		}
	}
?>