<?php
//ryannx6@gmail.com


class SocketClient {
	const BUFFER_SIZE = 2048;

	private $host, $port;
	private $socket;

	public function __construct($config) {
		$this->host = $config['host'];
		$this->port = $config['port'];

		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	}

	public function send(ChatRequest $request) {
		$packets = SocketPacket::buildPackets($request->flush());
		foreach ($packets as $packet) {
			$data = $packet->getPacketData();
			socket_sendto($this->socket, $data, strlen($data), 0, $this->host, $this->port);
		}
	}

	public function receive() {
		do {
			socket_recvfrom($this->socket, $buffer, self::BUFFER_SIZE, 0, $this->host, $this->port);

			$packet = new SocketPacket();
			$res = $packet->setPacketData($buffer);
		} while (!$res);

		$response = new ChatResponse();
		$response->setData($packet->data);
		return $response;
	}

	public function __destruct() {
		socket_close($this->socket);
	}
}


