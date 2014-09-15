<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once('common.php');

require_once('lib/IDartObject.class.php');
require_once('lib/DartResponse.class.php');
require_once('lib/DartRequest.class.php');

require_once('example/objects.php');

$handlerType = 'Update';
$handlers = getHandlers($handlerType);

if(isset($_POST['dartObject'])) {
	$dartObjects = $_POST['dartObject'];
	if(!is_array($dartObjects)) $dartObjects = array($dartObjects);

	foreach($dartObjects as $obj) {
		$req = new DartRequest($obj);
		$phpDartObject = $req->toObject();
		$type = $req->dartObjectType;
		if(in_array($type, $handlers)) {
			$cls = getHandlerClass($type, $handlerType);
			require_once('handlers/' . $cls . '.class.php');
			
			$inst = new $cls($phpDartObject);
		} else {
			echo 'nope';
		}
	}
}

$test = new TestObject();
$test->test1 = array('test');
$test->test2 = 5;
$test->test3 = (double)5;
$test->test4 = 'test item';
$test->test5 = true;

$dartObject = $test->toString();

$validObject = new validDartObj();
$dartObject = $validObject->toString();


//echo json_encode($obj);
