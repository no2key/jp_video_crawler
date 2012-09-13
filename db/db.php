<?php
require_once("mysqlpdo.db.php");

class db {
	private static $_instance = NULL;

	private function __construct() {
	}

	public function get_instance() {
		if (!db :: $_instance) {
			db :: $_instance = new mysqlpdo('root', '123', 'jp_videos');
		}
		return db :: $_instance;
	}
}
?>
