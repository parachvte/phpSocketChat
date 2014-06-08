<?php
//ryannx6@gmail

class UserCache {
	const TIMEOUT = 60;
	private $cache = [];

	/**
	 * Renew $cache and kick out all timeout users
	 * @param $nick
	 */
	public function renew($nick) {
		$t = time();
		$this->cache[$nick] = $t;
		asort($this->cache);
		foreach ($this->cache as $nick => $time) {
			if ($time < $t - self::TIMEOUT)
				unset($this->cache[$nick]);
			else
				break;
		}
	}

	public function getAll() {
		return array_keys($this->cache);
	}
}
