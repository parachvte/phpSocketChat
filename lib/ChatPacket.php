<?php
//ryannx6@gmail.com

/**
 * Class ChatPacket
 *
 * Parse $data in a JSON-like way
 */
class ChatPacket {
	protected $rawData;
	public $data = [];

	public function __construct($rawData = null) {
		if ($rawData) {
			$this->rawData = $rawData;
			try {
				$this->data = json_decode($rawData, true);
			} catch (UnexpectedValueException $e) {
				echo $e;
			}
		}
	}

	public function setValue($key, $value) {
		$this->data[$key] = $value;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function flush() {
		if (!$this->rawData) {
			$this->rawData = json_encode($this->data);
		}
		return $this->rawData;
	}
}
