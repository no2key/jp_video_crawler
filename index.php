<?php
set_time_limit(0);

include_once('crawler/jcrawler_factory.class.php');
require_once('lib/io/FileExtend.php');
require_once('lib/io/FileReader.php');
require_once('lib/io/FileWriter.php');
require_once('lib/io/FormatFileSize.php');

$d = directory_table :: search('directory', 'closed', '0');
for ($i = 0; $i < count($d); $i++) {
	$c = jcrawler_factory :: create($d[$i]->get_url());
	if ($c->check_url()) {
		// 解析页面地址的HTML内容
		$c->parse_url();
		$c->save_page_urls();
	}
}
?>
