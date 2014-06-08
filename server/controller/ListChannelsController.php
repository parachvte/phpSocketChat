<?php
//ryannx6@gmail.com

class ListChannelsController {
	public function post(ChatRequest $request, ChatResponse $response) {
		$channels = MessagePool::getAll();
		$response->setValue('channels', $channels);
		return $response->success();
	}
}
