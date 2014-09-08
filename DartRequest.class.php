<?php
class DartRequest {
	public function DartRequest($json) {
		$object = json_decode($json);

		if($object === NULL) return NULL;

		$vars = get_object_vars($object);
		
		foreach($vars as $var => $val) {
			$this->$var = $val;
		}
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

		return $dartObj;
	}
}
