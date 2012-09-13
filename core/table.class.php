<?php
require_once("db/db.php");

class table {
	private $_primary_key = 'id';

	private $_changed_columns = array('last_update_time');

	protected $_table = '';


	public function __construct () {
		$ary = explode('_', get_class($this));
		if (count($ary) > 1) {
			$name = '';
			for ($i = 0; $i < (count($ary) - 1); $i++) {
				$name .= ($name ? '_' : '') . $ary[$i];
			}
			$this->_table = $name;
		}
	}

	public function __call($m, $a) {
		$ary = explode('_', $m);
		if (count($ary) > 1) {
			if ($ary[0] == 'set') {
				$name = '';
				for ($i = 1; $i < count($ary); $i++) {
					$name .= ($name ? '_' : '') . $ary[$i];
				}
				$this->_changed_columns[] = $name;
				$this->$name = $a[0];
			} else if ($ary[0] == 'get') {
				$name = '';
				for ($i = 1; $i < count($ary); $i++) {
					$name .= ($name ? '_' : '') . $ary[$i];
				}
				return $this->$name;
			}
		}
	}

	public function load($table, $id) {
		$db = db :: get_instance();
		$datas = $db->query(
			array($table), 
			array(
				$table => implode(',', table :: get_columns(true, $table))
			), 
			array(
				$table => 'id="' . $id . '"'
			)
		);
		$data = $datas[$table][0];
		if ($data) {
			eval('$ret = new ' . $table . '_table();');
			foreach ($data as $key => $value) {
				eval('$ret->set_' . $key . '("' . $value . '");');
			}
			return $ret;
		}
		return NULL;
	}

	public function save () {
		$db = db :: get_instance();
		$primary_key = $this->_primary_key;
		if ($this->$primary_key) {
			$datas = $this->get_datas();
			for ($i = 0; $i < count($datas); $i++) {
				if ($datas[$i] != 'NOW()')
					$datas[$i] = '"' . $datas[$i] . '"';
			}
			$datas = $db->update(
				array($this->_table), 
				array(
					$this->_table => $this->get_columns()
				), 
				array(
					$this->_table => $datas
				),
				array(
					$this->_table => $this->_primary_key . '="' . $this->$primary_key . '"'
				)
			);
			return $datas[$this->_table];
		} else {
			$datas = $this->get_datas();
			for ($i = 0; $i < count($datas); $i++) {
				if ($datas[$i] != 'NOW()')
					$datas[$i] = '"' . $datas[$i] . '"';
			}
			$datas = $db->insert(
				array($this->_table), 
				array(
					$this->_table => implode(',', $this->get_columns())
				), 
				array(
					$this->_table => '(' . implode(',', $datas) . ')'
				)
			);
			$this->$primary_key = $datas[$this->_table];
			return $datas[$this->_table];
		}
	}

	public function delete ($table, $id) {
		$db = db :: get_instance();
		return $db->remove(
			array($table), 
			array(
				$table => 'id="' . $id . '"'
			)
		);
	}

	public function check ($table, $key, $word) {
		$db = db :: get_instance();
		$datas = $db->query(
			array($table),
			array(
				$table => 'id'
			),
			array(
				$table => $key . ' like "' . $word . '"'
			)
		);
		//echo "<xmp>";var_dump($datas);echo "</xmp>";
		$data = $datas[$table][0];
		if ($data) {
			eval('$ret = new ' . $table . '_table();');
			$ret->set_id($data['id']);
			return $ret;
		}
		return NULL;
	}

	public function search ($table, $key, $word) {
		$db = db :: get_instance();
		$datas = $db->query(
			array($table), 
			array(
				$table => implode(',', table :: get_columns(true, $table))
			), 
			array(
				$table => $key . ' like "' . $word . '"'
			)
		);
		$datas = $datas[$table];
		$ret = array();
		for ($i = 0; $i < count($datas); $i++) {
			eval('$ret[$i] = new ' . $table . '_table();');
			foreach ($datas[$i] as $key => $value) {
				eval('$ret[$i]->set_' . $key . '("' . $value . '");');
			}
		}
		return count($ret) > 0 ? $ret : NULL;
	}

	public function get_primary_key () {
		return $this->_primary_key;
	}

	public function set_primary_key ($key) {
		$this->_primary_key = $key;
	}

	private function get_columns ($with_pk = false, $table = '') {
		if (!$table) {
			$ary = $this->_changed_columns;
			$columns = array();
			for ($i = 0; $i < count($ary); $i++) {
				if ($ary[$i][0] == '_')
					continue;
				if (!$with_pk && $ary[$i] == $this->_primary_key)
					continue;
				$columns[] = $ary[$i];
			}
			return $columns;
		} else {
			$ary = get_class_vars($table . '_table');
			$columns = array();
			foreach ($ary as $key => $value) {
				if ($key[0] == '_')
					continue;
				$columns[] = $key;
			}
			return $columns;
		}
	}

	private function get_datas ($with_pk = false) {
		$ary = $this->_changed_columns;
		$datas = array();
		for ($i = 0; $i < count($ary); $i++) {
			if ($ary[$i][0] == '_')
				continue;
			if (!$with_pk && $ary[$i] == $this->_primary_key)
				continue;
			$key = $ary[$i];
			$datas[] = $this->$key;
		}
		return $datas;
	}
}
?>
