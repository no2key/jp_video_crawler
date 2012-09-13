<?php
interface idb {
	public function query ($tables, $columns, $conditions);

	public function insert ($tables, $columns, $datas);

	public function update ($tables, $columns, $datas, $conditions);

	public function remove ($tables, $conditions);
}
?>