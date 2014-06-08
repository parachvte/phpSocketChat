<?php
//ryannx6@gmail.com

class DeleteChannelController {
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['channel'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$channel = $request->data['channel'];

		MessagePool::delete($channel);
		return $response->success();
	}
}
