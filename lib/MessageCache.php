<?php
//ryannx6@gmail.com

class MessageCache {
	public static $counter = 0;
	private static $cache = [];

	/**
	 * @param $nick
	 * @param $content
	 * @return int
	 */
	public static function push($nick, $content) {
		$item = ['nick' => $nick, 'content' => $content];
		self::$cache[++self::$counter] = $item;
		return self::$counter;
	}

	/**
	 * @param $start
	 * @return array
	 */
	public static function get($start) {
		$result = [];
		for ($id = $start; $id <= self::$counter; $id++) {
			if (isset(self::$cache[$id])) $result[$id] = self::$cache[$id];
		}
		return $result;
	}
}
