<?php
require_once("core/table.class.php");

class source_table extends table {
	var $id = 0;

	var $name = '';

	var $last_update_time = 'NOW()';
}
?>
