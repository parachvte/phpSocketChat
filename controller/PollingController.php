<?php
//ryannx6@gmail.com


class PollingController {
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['last_mid'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$last_mid = $request->data['last_mid'];

		$messages = MessageCache::get($last_mid + 1);
		$response->setValue('chatItems', $messages);
		$response->setValue('counter', MessageCache::$counter);
		return $response->success();
	}
}
