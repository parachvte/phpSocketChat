<?php
//ryannx6@gmail.com


class PollingController {
	/**
	 * @param ChatRequest $request
	 * @param ChatResponse $response
	 * @return ChatResponse
	 */
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['channel', 'last_mid'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$channel = $request->data['channel'];
		$last_mid = $request->data['last_mid'];

		$cache = MessagePool::get($channel);
		$messages = $cache->get($last_mid + 1);
		$response->setValue('chatItems', $messages);
		$response->setValue('counter', $cache->counter);
		return $response->success();
	}
}
