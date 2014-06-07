<?php
//ryannx6@gmail.com


class MessageController {
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['nick', 'content'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$nick = $request->data['nick'];
		$content = $request->data['content'];
		if (!$nick) return $response->failed();

		if ($mid = MessageCache::push($nick, $content)) {
			$response->setValue('mid', $mid);
			return $response->success();
		} else
			return $response->failed();
	}
}
