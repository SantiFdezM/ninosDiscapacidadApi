<?php 
	class RegisterResult{

		public $message;
		public $success;
		public $token;

		public static function create($message, $success, $token){
			$object = new RegisterResult;
			$object->message = $message;
			$object->success = $success;
			$object->token = $token;
			return $object;
		}
	}
?>