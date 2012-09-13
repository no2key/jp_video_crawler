<?php

require_once("IOException.php");

class FileException extends IOException {

	public function __construct($msg="") {
		parent::__construct($msg);
	}
}
?>