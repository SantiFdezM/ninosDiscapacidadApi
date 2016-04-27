<?php 
	class VerifyResult{

		public $message;
		public $exists;
		public $username;

		public static function create($message, $exists, $username){
			$object = new VerifyResult;
			$object->message = $message;
			$object->exists = $exists;
			$object->username = $username;
			return $object;
		}
	}
?>