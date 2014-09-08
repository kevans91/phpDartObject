
<?php
require_once('DartResponse.class.php');

class IDartObject {
	static $__explicitValidTypes = array('string',
					'integer',
					'boolean',
					'double',
					'array',
					'NULL');

	public function IDartObject() {
		$vars = get_object_vars($this);

		foreach($vars as $k => $v) {
			unset($this->$k);
		}
	}

	public function toResponse() {
		if(!self::isValidSpecification()) {
			trigger_error('Type ' . get_called_class() . ' is not a valid IDartObject specification', E_USER_ERROR);
		} else if(!$this->isValid()) {
			trigger_error('Instance of type ' . get_called_class() . ' is not valid', E_USER_ERROR);
		}

		$response = new DartResponse();
		$response->dartObjectType = get_called_class();
		if(!$this->setDartVariables($response)) {
			return NULL;
		}
	
		return $response;
	}

	private function _dartobj_hasExplicitValidType($val) {
		$type = gettype($val);

		return in_array($type, IDartObject::$__explicitValidTypes);
	}

	private function _dartobj_hasType($val, $type) {
		return (gettype($val) === $type || $this->_dartobj_isValidDartObjType($val, $type));
	}

	private function _dartobj_isValidDartObjType($val, $type) {
		return (is_subclass_of($type, 'IDartObject') && (is_null($val) || get_class($val) == $type));
	}

	private function _dartobj_parseArray(&$arr) {
		foreach($arr as $k => $v) {
			if(is_array($v)) {
				$this->_dartobj_parseArray($arr[$k]);
			} else if(is_subclass_of($v, 'IDartObject')) {
				$arr[$k] = $v->toResponse();
			} else if(!$this->_dartobj_hasExplicitValidType($v)) {
				trigger_error('Instance of type ' . get_called_class() . ' has not explicitly allowed object in an array', E_USER_ERROR); 
			}
		}
	}

	private function _dartobj_setVars(&$response, $varList) {
		if(empty($varList)) return;
		if(is_string(array_keys($varList)[0])) $varList = array_keys($varList);

		foreach($varList as $var) {
			if(!isset($this->$var)) continue;
			$val =& $this->$var;

			if(empty($val)) {
				$response->$var = $val;
			} else if(is_subclass_of($val, 'IDartObject')) {
					// Convert this to a response object.
				$response->$var = $val->toResponse();
			} else if(is_array($val)) {
					// Convert any elements to a response object
				$response->$var = $val;
				
				$this->_dartobj_parseArray($response->$var);
			} else {
				$response->$var = $val;
			}
		}
	}

	public function setDartVariables(&$response) {
		if(get_class($response) != 'DartResponse') return false;

		$allVars = array_merge(self::getRequiredVariables(), self::getOptionalVariables());

		$this->_dartobj_setVars($response, $allVars);

		return true;
	}

	public function isValid() {
		$vars = get_object_vars($this);
		$requiredVars = self::getRequiredVariables();
		$optionalVars = self::getOptionalVariables();
		foreach($requiredVars as $k => $type) {
			if(!isset($this->$k)) {
				return false;
			} else if(!$this->_dartobj_hasType($this->$k, $type)) {
				return false;
			}
		}

		foreach($optionalVars as $k => $type) {
			if(isset($this->$k) && !$this->_dartobj_hasType($this->$k, $type)) {
				return false;
			}	
		}

		return true;
	}

	public static function isValidSpecification() {
		static $valid;

		if(is_bool($valid)) return $valid;

		$vars = get_class_vars(get_called_class());

		$valid = true;

		foreach($vars as $k => $type) {
			if(strpos($k, '__') === 0) continue;

			if(!is_string($type)) {
				$valid = false;
				break;
			} else if(!in_array($type, IDartObject::$__explicitValidTypes)) {
				if(!is_subclass_of($type, 'IDartObject')) {
					$valid = false;
					break;
				} else {
					// Only subclasses of IDartObject are implicitly valid
					continue;
				}
			}
		}

		return $valid;
	}

	public static function getRequiredVariables() {
		static $allRequiredVars;

		$myClass = get_called_class();	

		if(!empty($allRequiredVars[$myClass])) return $allRequiredVars[$myClass];

		$vars = get_class_vars($myClass);

		$requiredVars = array();
		foreach($vars as $k => $v) {
			if(empty($v)) {
				trigger_error('Invalid IDartObject specification: ' . $myClass . '::' . $k . ' is missing a type specification', E_USER_ERROR);
			}

			if($k[0] !== '_') {
				$requiredVars[$k] = $v;
			}
		}

		$allRequiredVars[$myClass] = $requiredVars;

		return $requiredVars;
	}

	public static function getOptionalVariables() {
		static $allOptionalVars;

		$myClass = get_called_class();

		if(!empty($allOptionalVars[$myClass])) return $allOptionalVars[$myClass];

		$vars = get_class_vars($myClass);
	
		$optionalVars = array();
		foreach($vars as $k => $v) {
			if(empty($v)) {
				trigger_error('Invalid IDartObject specification: ' . $myClass . '::' . $k . ' is missing a type specification', E_USER_ERROR);
			}

			if($k[0] === '_' && $k[1] !== '_') {
				$optionalVars[substr($k, 1)] = $v;
			}
		}

		$allOptionalVars[$myClass] = $optionalVars; 

		return $optionalVars;
	}
}
