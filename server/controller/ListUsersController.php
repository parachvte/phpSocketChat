<?php
//ryannx6@gmail.com

class ListUsersController {
	public function post(ChatRequest $request, ChatResponse $response) {
		$params = ['channel'];
		foreach ($params as $param) if (!$request->ensure($param)) return $response->failed();

		$channel = $request->data['channel'];

		$users = UserPool::get($channel)->getAll();
		$response->setValue('users', $users);
		return $response->success();
	}
}
