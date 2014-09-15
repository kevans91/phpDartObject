<?php
class DartResponse {
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
}
