<?php
require_once("core/table.class.php");

class program_table extends table {
	var $id = 0;

	var $name = '';

	var $summary = '';

	var $poster = '';

	var $url = '';

	var $parent_id = 0;

	var $labels = '';

	var $sources = '';

	var $last_update_time = 'NOW()';
}
?>
