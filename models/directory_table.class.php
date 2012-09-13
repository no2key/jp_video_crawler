<?php
require_once("core/table.class.php");

class directory_table extends table {
	var $id = 0;

	var $name = '';

	var $url = '';

	var $program_id = 0;

	var $label_id = 0;

	var $source_id = 0;

	var $closed = 0;

	var $last_update_time = 'NOW()';
}
?>