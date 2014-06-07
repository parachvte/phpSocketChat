<?php
//ryannx6@gmail.com

class ChatRequest extends ChatPacket {

	public function __construct($rawData = null) {
		if ($rawData) {
			parent::__construct($rawData);
			$this->action = $this->data['action'];
		}
	}

	public function ensure($key) {
		return isset($this->data[$key]);
	}

	public function flush() {
		return parent::flush();
	}
}
