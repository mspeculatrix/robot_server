<?php
define ( 'APP_NAME', 'sheldonServer' );
define ( 'PAGE_TYPE',	'ajaxSvr' );

/*
	The purpose of this app is to provide a web-based API for the
	Sheldon robot.

	The server might return simple text responses, or it might
	return JSON data.

	All requests should start with fn=<function name>

	https://www.tutorialrepublic.com/php-tutorial/php-json-parsing.php

	json_encode() and json_decode() for converting between associative
	arrays and JSON objects.

	Messages will have the following format:
	char	meaning
	0		always 'H' for HTTP messages
	1		Category 	- eg, 'M' for motor-related messages
	2		Type		- eg, 'E' for error
	3-> 	Content		- length will be dependent on context

*/

// ***** DO NOT EDIT *****
require_once ( $_SERVER['DOCUMENT_ROOT'].'/include/std_config.php' );
$pagemode = pagemode();
// ----- END OF DO NOT EDIT -----

date_default_timezone_set("Europe/Paris");

function logmsg($msg) {
	$logline = date('YmdHis').' '.$msg;
	$fh = fopen('log/sheldon.log', 'a');
	fwrite($fh, $logline."\n");
	fclose($fh);
}

$response = array();	// might want to switch to JSON ???
$responseType = 'plain';	// other options are: 'xml', 'json'

$msg = clean_request('msg');

$msgCat = substr($msg, 1, 1);
$msgType = substr($msg, 2, 1);
$msgContent = trim(substr($msg, 3));

switch ($msgCat) {
	case 'M':						// motor stuff
		$responseType = 'plain';
		switch ($msgType) {
			case 'E':				// errors
				switch($msgContent) {
					case 'S':
						logmsg('stalled');
						array_push($response, 'OK');
						break;
				}
				break;
		}
		break;
	// if we want to output Json, create a new array (probably associative) called $json,
	// then built the elements of the array,
	// then convert to a string with json_encode(),
	// then push this string on to $response
}

/**********************************************************************************************
 ***** OUTPUT                                                                             *****
 **********************************************************************************************/
if ( $response ) {
//	sleep(5);
	switch($responseType) {
		case 'plain':
			header('content-type: text/plain');
			break;
		case 'xml':
			header('content-type: application/xhtml+xml; charset=utf-8');
			break;
		case 'json':
			header('content-type: application/json; charset=utf-8');
			break;
	}
	header('Cache-Control: no-cache, must-revalidate');
	foreach($response as $line) {
		echo $line.NL;
	}


} else {
	// header('content-type: text/html');
	// header("Cache-Control: no-cache, must-revalidate");
	// echo '<html><head><title>Error</title><body><p>Error</p>'.$errorinfo.'</body></html>'.NL;
	header('content-type: text/plain');
	header('Cache-Control: no-cache, must-revalidate');
	echo 'ERROR';
	
}

?>
