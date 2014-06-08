<?php
//ryannx6@gmail.com

// don't timeout!
set_time_limit(0);

require_once('../init.php');
$config = include(ROOT . '/config.php');

$routing = [
	'message' => 'MessageController',
	'polling' => 'PollingController',
	'channelAdd' => 'ChannelAddController',
	'listChannels' => 'ListChannelsController',
	'deleteChannel' => 'DeleteChannelController'
];

$server = new SocketServer($config, function (ChatRequest $request) {
	$response = new ChatResponse();

	echo "Request " . $request->action. "\n";

	global $routing;
	foreach ($routing as $action => $ctlName)
		if ($request->action == $action) {
			$controller = new $ctlName();
			$method = $request->data['method'];
			if (is_callable([$controller, $method])) {
				return $controller->$method($request, $response);
			}
		}

	$response->setValue('status', 'Action unrecognized');
	return $response;
});
$server->loop();
