<?php
//ryannx6@gmail.com

class MessagePool {
	private static $pool = [];
	private static $id2name = [];
	private static $counter = 0;

	/**
	 * @param $id
	 * @return MessageCache
	 */
	public static function get($id) {
		if (!isset(self::$pool[$id])) {
			self::$pool[$id] = new MessageCache();
		}
		return self::$pool[$id];
	}

	/**
	 * @param $newName
	 * @return bool|int
	 */
	public static function create($newName) {
		foreach (self::$id2name as $id => $name)
			if ($name == $newName) return false;
		self::$id2name[++self::$counter] = $newName;
		return self::$counter;
	}

	/**
	 * @return array
	 */
	public static function getAll(){
		return self::$id2name;
	}

	public static function delete($id){
		if (isset(self::$id2name[$id])){
			unset(self::$id2name[$id]);
			unset(self::$pool[$id]);
		}
	}
}
