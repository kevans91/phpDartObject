<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once('lib/IDartObject.class.php');
require_once('lib/DartResponse.class.php');
require_once('lib/DartRequest.class.php');

class TestObject extends IDartObject {
	var $test1 = 'array';
	var $test2 = 'integer';
	var $_test3 = 'double';
	var $_test4 = 'string';
	var $_test5 = 'boolean';
}


class validDartObj extends IDartObject {}

function testObjectHandler($object) {
	var_dump(__FUNCTION__);
	var_dump($object);
	return true;
}

function moreTestObjectHandler($object) {
	var_dump(__FUNCTION__);
	var_dump($object);
}

function validObjectHandler($object) {
	var_dump(__FUNCTION__);
	var_dump($object);
}

DartRequest::registerHandler('TestObject', 'testObjectHandler');
DartRequest::registerHandler('TestObject', 'moreTestObjectHandler');
DartRequest::registerHandler('validDartObj', 'validObjectHandler');

$test = new TestObject();
$test->test1 = array('test');
$test->test2 = 5;
$test->test3 = (double)5;
$test->test4 = 'test item';
$test->test5 = true;

$dartObject = $test->toString();
$req = new DartRequest($dartObject);

$validObject = new validDartObj();
$dartObject = $validObject->toString();
$req = new DartRequest($dartObject);

//echo json_encode($obj);
