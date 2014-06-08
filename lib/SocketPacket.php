<?php
//ryannx6@gmail.com

/**
 * Definition of SocketPacket
 *
 * Simply Thoughts:
 * 1. Protocol		: 2 bytes, here it must be 0x58 (the last two digits of my SID)
 * 2. No Fragment	: 1 byte
 * 3. More Fragment	: 1 byte
 * 4. Identifier	: 4 bytes
 * 5. Length		: 4 bytes (useless)
 * 6. Offset		: 4 bytes
 * 7. Data			: all others
 */
class SocketPacket {
	const MAX_PACKET_SIZE = 1516;
	const MAX_DATA_SIZE = 1516;

	private static $idCounter = 0;

	// 16 bytes header
	public $protocol;
	public $noFragment;
	public $moreFragment;
	public $identifier; //todo: here $identifier, $length, $offset are decimals, convert to hexadecimal someday
	public $length;
	public $offset;
	// data
	public $data;

	public function setPacketData($packetData) {
		$this->protocol = substr($packetData, 0, 2);
		if ($this->protocol !== '58') return false;
		$this->noFragment = $packetData[2] === '1';
		$this->moreFragment = $packetData[3] === '1';
		$this->identifier = substr($packetData, 4, 4);
		try {
			$this->length = (int)substr($packetData, 8, 4);
			$this->offset = (int)substr($packetData, 12, 4);
		} catch (Exception $e) {
			echo "length and offset in packet header must be integers\n" . $e->getMessage() . "\n";
			return false;
		}
		$this->data = substr($packetData, 16);
		return true;
	}

	public function getPacketData() {
		$data = $this->protocol;
		$data .= $this->noFragment ? '1' : '0';
		$data .= $this->moreFragment ? '1' : '0';
		$data .= sprintf("%04d", $this->identifier);
		$data .= sprintf("%04d", $this->length);
		$data .= sprintf("%04d", $this->offset);
		$data .= $this->data;
		return $data;
	}

	public static function buildPackets($data) {
		self::$idCounter = (self::$idCounter + 1) % 10000;

		$number = ceil((strlen($data) - 0.1) / self::MAX_DATA_SIZE);
		$packets = [];
		for ($i = 0; $i < $number; $i++) {
			$packet = new SocketPacket();
			$packet->protocol = '58';
			$packet->noFragment = ($number == 1);
			$packet->moreFragment = ($i != $number - 1);
			$packet->identifier = self::$idCounter;
			$packet->length = ($i == $number - 1) ? (strlen($data) - $i * self::MAX_DATA_SIZE) : self::MAX_DATA_SIZE;
			$packet->offset = $i; // just $i, then $offset can be abandoned
			$packet->data = substr($data, $i * self::MAX_DATA_SIZE, $packet->length);

			$packets[] = $packet;
		}
		return $packets;
	}
}
