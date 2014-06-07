<?php
//ryannx6@gmail.com


/**
 * @param $data: An array
 */
function sendRequest($data) {
	require_once('../init.php');
	$config = include(ROOT . '/config.php');

	$client = new SocketClient($config);

	$request = new ChatRequest();
	$request->setData($data);

	$client->send($request);
	$response = $client->receive();
	echo $response->data;
}
