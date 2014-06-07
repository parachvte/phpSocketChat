<?php
//ryannx6@gmail.com

require('API.php');

define('DEBUG', false);
//define('DEBUG', true);
if (DEBUG) {
	sendRequest([
		'method' => 'post',
		'action' => 'message',
		'nick' => 'Ryan',
		'content' => 'x'
	]);
	die;
}


try {
	$method = $_SERVER['REQUEST_METHOD'];
	$action = $_POST['action'];
	$data = [
		'method' => $method,
		'action' => $action
	];
	switch ($action) {
		case 'message':
			$data += [
				'nick' => $_POST['nick'],
				'content' => $_POST['content']
			];
			break;
		case 'polling':
			$data += [
				'last_mid' => $_POST['last_mid']
			];
			break;
	}
	sendRequest($data);
} catch (Exception $e) {
	echo json_encode(['status' => 1]);
}

