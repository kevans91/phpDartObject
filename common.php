<?php

function getHandlers($type) {
	$classPostfix = $type . 'Handler';
	$handlerFiles = glob('handlers/*' . $classPostfix . '.class.php');
	$handlers = array();
	foreach($handlerFiles as $fname) {
		$matches = array();
		$ret = preg_match('/handlers\/([A-Za-z0-9]+)' . $classPostfix . '\.class\.php/', $fname, $matches);
		if($ret) $handlers[] = $matches[1];
	}

	return $handlers;
}

function getHandlerClass($cls, $type) {
	return $cls . $type . 'Handler';
}
