<?php
//ryannx6@gmail.com


class ChatResponse extends ChatPacket {
	public function __construct() {
		parent::__construct();
	}

	public function success() {
		$this->setValue('status', 0);
		return $this;
	}

	public function failed() {
		$this->setValue('status', 1);
		return $this;
	}
}
