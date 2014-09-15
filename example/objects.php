<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once('../lib/IDartObject.class.php');

class TestObject extends IDartObject {
	var $test1 = 'array';
	var $test2 = 'integer';
	var $_test3 = 'double';
	var $_test4 = 'string';
	var $_test5 = 'boolean';
}

class validDartObj extends IDartObject {}
