<?php
//ryannx6@gmail.com

class MessageCache {
	public $counter = 0;
	private $cache = [];

	/**
	 * @param $nick
	 * @param $content
	 * @param $toNick : If $toNick is empty, message will be broadcast
	 * @return int
	 */
	public function push($nick, $content, $toNick) {
		$item = ['nick' => $nick, 'content' => $content, 'toNick' => $toNick];
		$this->cache[++$this->counter] = $item;
		return $this->counter;
	}

	/**
	 * @param $start
	 * @return array
	 */
	public function get($start) {
		$result = [];
		for ($id = $start; $id <= $this->counter; $id++) {
			if (isset($this->cache[$id])) $result[$id] = $this->cache[$id];
		}
		return $result;
	}
}
