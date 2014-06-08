<?php
//ryannx6@gmail.com

class UserPool {
	private static $pool = [];

	/**
	 * @param $id
	 * @return UserCache
	 */
	public static function get($id) {
		if (!isset(self::$pool[$id])) {
			self::$pool[$id] = new UserCache();
		}
		return self::$pool[$id];
	}
}
