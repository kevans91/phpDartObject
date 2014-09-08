<?php
class DartResponse {
	public function DartResponse($json = '') {
		if(isset($json)) {
			$object = json_decode($json);

			if($object === NULL) return NULL;

			$vars = get_object_vars($object);
		
			foreach($vars as $var => $val) {
				$this->$var = $val;
			}
		}
	}

	public function toString() {	
		$vars = get_object_vars($this);
		
			// Filter out __variables
		$response = new stdClass();
		foreach($vars as $var => $val) {
			if(strpos($var, '__') === 0) continue;

			$response->$var = $val;
		}

		return json_encode($response);
	}

	public function toObject() {
		if(!isset($this->dartObjectType) || !is_subclass_of($this->dartObjectType, 'IDartObject')) return NULL;

		if(!call_user_func_array(array($this->dartObjectType, 'isValidResponse'), array($this))) {
			trigger_error('Invalid response', E_USER_ERROR);
		}

		$dartObj = new $this->dartObjectType();
		if(!$dartObj->fromResponse($this)) {
			return NULL;
		}

		return $dartObj;
	}
}
