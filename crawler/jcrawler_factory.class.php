<?php
require_once("jcrawler.class.php");
require_once("jyouku_crawler.class.php");

class jcrawler_factory {

	public function create ($url) {
		$ary = parse_url($url);

		if (strpos($ary['host'], 'youku.com')) {
			return new jyouku_crawler($url);
		} else if (strpos($ary['host'], 'tudou.com')) {
			return new jtudou_crawler($url);
		} else {
			return NULL;
		}
	}
}
?>