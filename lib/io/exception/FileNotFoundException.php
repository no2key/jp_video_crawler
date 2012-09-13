<?php

require_once("IOException.php");

class FileNotFoundException extends IOException {

	public function __construct($msg="") {
		parent::__construct($msg);
	}
}
?>