<?php
require_once("core/table.class.php");

class video_table extends table {
	var $id = 0;

	var $url = '';

	var $hash = '';

	var $program_id = 0;

	var $source_id = 0;

	var $last_update_time = 'NOW()';
}
?>
