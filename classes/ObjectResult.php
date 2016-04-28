<?php 
	class ObjectResult{

		public $message;
		public $success;
		public $array;

		public static function create($message, $success, $array){
			$object = new ObjectResult;
			$object->message = $message;
			$object->success = $success;
			$object->array = $array;
			return $object;
		}
	}
?>