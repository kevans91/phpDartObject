<?php
class DartRequest {
	static $__callbacks = array();

	public function DartRequest($json, $autoConvert = TRUE) {
		$object = json_decode($json);

		if($object === NULL) return NULL;

		$vars = get_object_vars($object);
		
		foreach($vars as $var => $val) {
			$this->$var = $val;
		}

			// Invoke toObject automagically,
			// so that creating a request and passing it an object will
			// just *work* and eventually call the user-defined handler
		if($autoConvert == TRUE) $this->toObject();
	}

	public function toObject() {
		if(!isset($this->dartObjectType) || !is_subclass_of($this->dartObjectType, 'IDartObject')) return NULL;

		if(!call_user_func_array(array($this->dartObjectType, 'isValidRequest'), array($this))) {
			trigger_error('Invalid request', E_USER_ERROR);
		}

		$dartObj = new $this->dartObjectType();
		if(!$dartObj->fromRequest($this)) {
			return NULL;
		}

		if(!empty(self::$__callbacks[$this->dartObjectType])) {
				// Call each registered callback, until we find one
				// that has officially handled the object
			foreach(self::$__callbacks[$this->dartObjectType] as $callable) {
				if(call_user_func($callable, $dartObj) === TRUE) {
					break;
				}
			}
		}

		return $dartObj;
	}

	public static function registerHandler($objectType, $callable) {
		if(!isset(self::$__callbacks[$objectType])) self::$__callbacks[$objectType] = array();

		self::$__callbacks[$objectType][] = $callable;
	}
}
