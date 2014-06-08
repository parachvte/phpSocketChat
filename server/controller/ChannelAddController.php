<?php
//ryannx6@gmail.com


class ChannelAddController {
	/**
	 * @param ChatRequest $request
	 * @param ChatResponse $response
	 * @return ChatResponse
	 */
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['channelName'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$channelName = $request->data['channelName'];
		if (!$channelName) return $response->failed();

		if ($cid = MessagePool::create($channelName)) {
			$response->setValue('cid', $cid);
			return $response->success();
		} else
			return $response->failed();
	}
}
