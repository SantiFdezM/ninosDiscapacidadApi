<?php 
	class LoginResult{

		public $message;
		public $success;
		public $token;
		public $user_type;
		public $user_fullname;
		public $id_user;

		public static function create($message, $success, $token, $user_type, $user_fullname){
			$object = new LoginResult;
			$object->message = $message;
			$object->success = $success;
			$object->token = $token;
			$object->user_type = $user_type;
			$object->user_fullname = $user_fullname;
			return $object;
		}

		public static function createId($message, $success, $token, $user_type, $user_fullname, $id_user){
			$object = new LoginResult;
			$object->message = $message;
			$object->success = $success;
			$object->token = $token;
			$object->user_type = $user_type;
			$object->user_fullname = $user_fullname;
			$object->id_user = $id_user;
			return $object;
		}
	}
?>