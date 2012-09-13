<?php
include_once 'IReader.php';

class CrawlerReader implements IReader {
	
	public function read($url)
	{
		return @file_get_contents($url);
	}
}