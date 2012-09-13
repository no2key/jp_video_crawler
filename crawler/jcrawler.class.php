<?php
include_once('lib/crawler/Crawler.php');

function SimpleHash($str){   
    $n = 0;
    for ($c=0; $c < strlen($str); $c++)
        $n += ord($str[$c]);
    return $n;
}

abstract class jcrawler {
	private $_crawler = NULL;

	private $_page_urls = array();

	protected $_url = '';


	public function __construct($url) {
		$this->_url = $url;
		$this->_crawler = new Crawler();

		$this->_crawler->setScanDepth(1)
			->setFollowExternalLinks(false)
			->addExcludedLink('advertising$');
	}

	abstract public function check_url ();

	abstract protected function _parse_html ($head, $body, $url);

	abstract public function save_page_urls ();

	public function parse_url () {
		$this->_crawler->addOnProcessPage($this);
		$raw_links = $this->_crawler->process($this->_url)->getLinks();
		$this->_page_urls = $raw_links[0]['children'];
	}

	public function fire ($head, $body, $url) {
		$this->_parse_html($head, $body, $url);
	}

	public function set_url($url) { $this->_url = $url; }

	public function get_url() { return $this->_url; }

	public function get_page_urls () { return $this->_page_urls; }
}
?>
