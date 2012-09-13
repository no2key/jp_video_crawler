<?php
require_once("idb.php");

class mysqlPdo implements idb
{
	private $_host = '';

	private $_db = '';

	private $_user = '';

	private $_passwd = '';

	private $_port = '';

	private $_conn = NULL;


	public function __construct ($user, $passwd, $db, $host = 'localhost', $port = '3306') {
		$this->_db = $db;
		$this->_host = $host;
		$this->_port = $port;

		$this->_user = $user;
		$this->_passwd = $passwd;

		$q = 'mysql:host=' . $this->_host . ';port=' . $this->_port . ';dbname=' . $this->_db;
		$this->_conn = new PDO($q, $this->_user, $this->_passwd, array(PDO :: ATTR_PERSISTENT => false));
		if (!$this->_conn) {
			throw Exception('mysql pdo db connection error.');
		}
	}

	public function query ($tables, $columns, $conditions) {
		$result = array();
		for ($i = 0; $i < count($tables); $i++) {
			$t = $tables[$i];
			$c = $this->organize_columns($columns[$t]);
			$w = $this->organize_conditions($conditions[$t]);
			$re = $this->_conn->query('set names utf8');
			$q = 'select ' . $c . ' from `' . $t . '` where ' . $w;
			$re = $this->_conn->query($q);
			if ($re) {
				$result[$t] = $re->fetchAll(2);
			} else {
				$result[$t] = false;
			}
		}
		return $result;
	}

	public function insert ($tables, $columns, $datas) {
		$result = array();
		for ($i = 0; $i < count($tables); $i++) {
			$t = $tables[$i];
			$c = $this->organize_columns($columns[$t]);
			$d = $this->organize_insert_datas($datas[$t]);
			$re = $this->_conn->query('set names utf8');
			$q = 'insert into `' . $t . '` (' . $c . ') values ' . $d;
			$re = $this->_conn->query($q);
			if ($re) {
				$result[$t] = $this->_conn->lastInsertId();
			} else {
				$result[$t] = false;
			}
		}
		return $result;
	}

	public function update ($tables, $columns, $datas, $conditions) {
		$result = array();
		for ($i = 0; $i < count($tables); $i++) {
			$t = $tables[$i];
			$d = $this->organize_update_datas($columns[$t], $datas[$t]);
			$w = $this->organize_conditions($conditions[$t]);
			$re = $this->_conn->query('set names utf8');
			$q = 'update `' . $t . '` set ' . $d . ' where ' . $w;
			$re = $this->_conn->query($q);
			$result[$t] = $re ? true : false;
		}
		return $result;
	}

	public function remove ($tables, $conditions) {
		$result = array();
		for ($i = 0; $i < count($tables); $i++) {
			$t = $tables[$i];
			$w = $this->organize_conditions($conditions[$t]);
			$q = 'delete from `' . $t . '` where ' . $w;
			$re = $this->_conn->query($q);
			$result[$t] = $re ? true : false;
		}
		return $result;
	}

	private function organize_columns ($columns) { return $columns; }

	private function organize_conditions ($conditions) { return $conditions; }

	private function organize_insert_datas ($datas) { return $datas; }

	private function organize_update_datas ($columns, $datas) {
		$str = '';
		for ($i = 0; $i < count($columns); $i++) {
			$str .= ($str ? ',' : '') . $columns[$i] . '=' . $datas[$i];
		}
		return $str;
	}
}
?>