
<?php
require_once('DartResponse.class.php');

class IDartObject {
	static $__validPrimitiveTypes = array('string',
					'integer',
					'boolean',
					'double',
					'array');

	public function IDartObject() {
		$vars = get_object_vars($this);

		foreach($vars as $k => $v) {
			unset($this->$k);
		}
	}

	public function toResponse() {
		if(!self::isValidSpecification() || !this->isValid()) return NULL;

		$response = new DartResponse();
		$response->dartObjectType = get_called_class();
		if(!$this->setDartVariables($response)) {
			return NULL;
		}
	
		return $response;
	}

	private function _dartobj_parseArray(&$arr) {
		foreach($arr as $k => $v) {
			if(is_array($v)) {
				$this->_dartobj_parseArray($arr[$k]);
			} else if(is_subclass_of($v, 'IDartObject')) {
				$arr[$k] = $v->toResponse();
			}
		}
	}

	private function _dartobj_setVars(&$response, $varList) {
		if(is_string(array_keys($varList)[0])) $varList = array_keys($varList);

		foreach($varList as $var) {
			if(!isset($this->$var)) continue;
			$val =& $this->$var;

			if(empty($val)) {
				$this->$var = $val;
			} else if(is_subclass_of($val, 'IDartObject')) {
					// Convert this to a response object.
				$response->$var = $val->toResponse();
			} else if(is_array($val)) {
					// Convert any elements to a response object
				$response->$var = $val;
				
				$this->_dartobj_parseArray($response->$var);
			} else {
				$this->$var = $val;
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
			if(!isset($this->$k) || gettype($this->$k) !== $type) {
				return false;
			}
		}

		foreach($optionalVars as $k => $type) {
			if(isset($this->$k) && gettype($this->$k) !== $type) {
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
		static $requiredVars;

		if(!empty($requiredVars)) return $requiredVars;

		$vars = get_class_vars(get_called_class());

		$requiredVars = array();
		foreach($vars as $k => $v) {
			if(empty($v)) {
				trigger_error('Invalid IDartObject specification: ' . get_called_class() . '::' . $k . ' is missing a type specification', E_USER_ERROR);
			}

			if($k[0] !== '_') {
				$requiredVars[$k] = $v;
			}
		}

		return $requiredVars;
	}

	public static function getOptionalVariables() {
		static $optionalVars;

		if(!empty($optionalVars)) return $optionalVars;

		$vars = array_keys(get_class_vars(get_called_class()));
	
		$optionalVars = array();
		foreach($vars as $k => $v) {
			if(empty($v)) {
				trigger_error('Invalid IDartObject specification: ' . get_called_class() . '::' . $k . ' is missing a type specification', E_USER_ERROR);
			}

			if($k[0] === '_' && $k[1] !== '_') {
				$optionalVars[$k] = $v;
			}
		}

		return $optionalVars;
	}
}
