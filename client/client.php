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
				'channel' => $_POST['channel'],
				'nick' => $_POST['nick'],
				'content' => $_POST['content']
			];
			break;
		case 'polling':
			$data += [
				'channel' => $_POST['channel'],
				'last_mid' => $_POST['last_mid'],
				'nick' => $_POST['nick']
			];
			break;
		case 'channelAdd':
			$data += [
				'channelName' => $_POST['channelName'],
			];
			break;
		case 'listChannels':
			break;
		case 'deleteChannel':
			$data += [
				'channel' => $_POST['channel']
			];
			break;
		case 'listUsers':
			$data += [
				'channel' => $_POST['channel']
			];
			break;
		default:
			throw new UnexpectedValueException('Action unrecognized.');
	}
	sendRequest($data);
} catch (Exception $e) {
	echo json_encode(['status' => 1]);
}

