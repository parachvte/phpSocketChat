<?php
//ryannx6@gmail.com

class MessageCache {
	public $counter = 0;
	private $cache = [];

	/**
	 * @param $nick
	 * @param $content
	 * @return int
	 */
	public function push($nick, $content) {
		$item = ['nick' => $nick, 'content' => $content];
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
