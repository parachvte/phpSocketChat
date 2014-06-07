<?php
//ryannx6@gmail.com

class SocketServer {
	const BUFFER_SIZE = 2048;
	const QUEUE_SIZE = 100;

	private $host, $port;
	private $socket;
	private $processFunc;

	private $queue = [];

	public function __construct($config, $processFunc) {
		$this->host = $config['host'];
		$this->port = $config['port'];
		$this->processFunc = $processFunc;

		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_bind($this->socket, $this->host, $this->port) or die('Could not bind socket');
	}

	/**
	 * Receive `$buffer`s and enqueue to $queue, and reassembly
	 *
	 * @return string $data which is reassembled
	 */
	private function receive() {
		do {
			socket_recvfrom($this->socket, $buffer, self::BUFFER_SIZE, 0, $this->host, $this->port);

			$packet = new SocketPacket();
			$res = $packet->setPacketData($buffer);
			if (!$res) continue;

			$data = $this->enqueue($packet);
			if ($data) return $data;
		} while (true);
	}

	private function send(ChatResponse $response) {
		$packets = SocketPacket::buildPackets($response->flush());
		foreach ($packets as $packet) {
			$data = $packet->getPacketData();
			socket_sendto($this->socket, $data, strlen($data), 0, $this->host, $this->port);
		}
	}

	public function loop() {
		echo "Listen to " . $this->host . ":" . $this->port . "\n";
		while (true) {
			$data = $this->receive();
			$request = new ChatRequest($data);
			$response = call_user_func($this->processFunc, $request);
			$this->send($response);
		}
	}

	/**
	 * 把一个SocketPacket入队，分析其是否所有的包都已经收到了，重组完毕把数据扔出去。
	 * todo: 该方法和SocketPacket的结构紧密相连，考虑拉到一个别处，比如Class SocketPacketQueue
	 *
	 * @param SocketPacket $packet
	 * @return bool|string
	 */
	private function enqueue(SocketPacket $packet) {
		if ($packet->noFragment) return $packet->data;
		$id = $packet->identifier;
		if (!isset($queue[$id])) {
			$queue[$id] = [$packet->offset => $packet];
		} else {
			$queue[$id][$packet->offset] = $packet;
		}
		if ($packet->moreFragment === '0') {
			$queue[$id]['maxOffset'] = $packet->offset;
		}
		if (isset($queue[$id]['maxOffset'])) {
			for ($i = 0; $i < $queue[$id]['maxOffset']; $i++)
				if (!isset($queue[$id][$i])) return false;
			$data = '';
			for ($i = 0; $i < $queue[$id]['maxOffset']; $i++)
				$data .= $queue[$id][$i]->data;

			return $data;
		}
		return false;
	}

	public function __destruct() {
		socket_close($this->socket);
	}
}
