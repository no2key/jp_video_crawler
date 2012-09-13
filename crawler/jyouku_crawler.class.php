<?php
require_once("jcrawler.class.php");
require_once("models/directory_table.class.php");
require_once("models/source_table.class.php");
require_once("models/label_table.class.php");
require_once("models/program_table.class.php");
require_once("models/video_table.class.php");

class jyouku_crawler extends jcrawler {
	var $source_id = 0;

	var $page_type = 0;

	var $directory_name = '';


	public function check_url () {
		$s = source_table :: check('source', 'name', '%优酷%');
		if (!$s) {
			$s = new source_table();
			$s->set_name('优酷');
			$s->save();
		}
		$this->source_id = $s->get_id();

		$url = $this->get_url();
		$ary = array();
		if (preg_match('/http:\/\/([a-z0-9_]*)\.youku\.com\/([^\/]+)(.*)/si', $url, $ary)) {
			if ($ary[1] == 'v' && $ary[2] == 'v_show') {
				$this->page_type = 3;
				return true;
			} else if ($ary[1] == 'movie' && $ary[2] == 'search') { 
				$this->page_type = 2;
				return true;
			} else if ($ary[1] == 'tv' && $ary[2] == 'search') {
				$this->page_type = 2;
				return true;
			} else if ($ary[1] == 'comic' && $ary[2] == 'search') {
				$this->page_type = 2;
				return true;
			} else if ($ary[1] == 'tv' && $ary[2] == 'search') {
				$this->page_type = 2;
				return true;
			} else if ($ary[1] == 'www' && $ary[2] == 'show_page') {
				$this->page_type = 1;
				return true;
			}
		}
		return false;
	}

	protected function _parse_html ($head, $body, $url) {
		switch ($this->page_type) {
		case '1':
			$this->_parse_show_page($body);
			break;
		case '2':
			$this->_parse_search_page($head);
			break;
		case '3':
			$this->_parse_video_page($body);
			break;
		}
	}

	private function _parse_search_page ($html) {
		$ary = array();
		preg_match('/<title>(.*)<\/title>/siU', $html, $ary);
		$this->directory_name = $ary[1];

		$d = directory_table :: check('directory', 'url', $this->get_url());
		if (!$d) {
			$d = new directory_table();
			$d->set_url($this->get_url());
			$d->set_name($this->directory_name);
			$d->set_source_id($this->source_id);
			$d->save();
		} else {
			$d->set_name($this->directory_name);
			$d->set_source_id($this->source_id);
			$d->save();
		}
	}

	private function _parse_show_page ($html) {
		$labels = array();
		$ary = array();
		preg_match('/<h1 class="title">\s*<span[^>]*>.*<\/span>\s*<span class="name">(.*)<\/span>\s*<span class="pub">(.*)<\/span>\s*<\/h1>/siU', $html, $ary);
		if ($ary[2]) {
			$l = label_table :: check('label', 'name', $ary[2]);
			if (!$l) {
				$l = new label_table();
				$l->set_name($ary[2]);
				$l->save();
			}
			$labels[] = $l->get_id();
		}
		$name = $ary[1];

		$ary = array();
		preg_match('/<div class="detail" id="Detail">\s*<span class="short" style="display:block;">(.*)<\/span>.*<\/div>/siU', $html, $ary);
		$summary = $ary[1];
		if (!$summary) {
			$ary = array();
			preg_match('/<div class="detail">\s*<span class="short" id="show_info_short" style="display: inline;">\s*<span>(.*)<\/span>.*<\/span>.*<\/div>/siU', $html, $ary);
			$summary = $ary[1];
		}
		if (!$summary) {
			$ary = array();
			preg_match('/<div class="detail">\s*<span class="short" id="show_info_short" style="display: inline;">(.*)<\/span>.*<\/div>/siU', $html, $ary);
			$summary = $ary[1];
		}

		$ary = array();
		preg_match('/<ul class="baseinfo">\s*<li class="link">.*<\/a><\/li>\s*<li class="thumb">\s*<img.*src=[\'"]{1}(.*)[\'"]{1}[^>]*><\/li>.*<li class="row1 rate">.*<\/li>\s*<li class="row1 alias">.*<\/li>\s*(.*)<\/ul>/siU', $html, $ary);
		$poster = $ary[1];

		$html = $ary[2];
		$ary = array();
		preg_match('/<span class="area">\s*<label>地区:<\/label>(.*)<\/span>/siU', $html, $ary);
		$html2 = $ary[1];
		$ary = array();
		preg_match_all('/<a[^>]*>(.*)<\/a>/siU', $html2, $ary);
		for ($i = 0; $i < count($ary[1]); $i++) {
			$l = label_table :: check('label', 'name', $ary[1][$i]);
			if (!$l) {
				$l = new label_table();
				$l->set_name($ary[1][$i]);
				$l->save();
			}
			$labels[] = $l->get_id();
		}
		$ary = array();
		preg_match('/<span class="type">\s*<label>类型:<\/label>(.*)<\/span>/siU', $html, $ary);
		$html2 = $ary[1];
		$ary = array();
		preg_match_all('/<a[^>]*>(.*)<\/a>/siU', $html2, $ary);
		for ($i = 0; $i < count($ary[1]); $i++) {
			$l = label_table :: check('label', 'name', $ary[1][$i]);
			if (!$l) {
				$l = new label_table();
				$l->set_name($ary[1][$i]);
				$l->save();
			}
			$labels[] = $l->get_id();
		}

		$p = program_table :: search('program', 'url', str_replace('#anchor', '', $this->get_url()));
		echo str_replace('#anchor', '', $this->get_url());echo '<br />';
		flush();
		if ($p[0]) {
			$p[0]->set_poster($poster);
			$p[0]->set_summary($summary);
			$labels_p = $p[0]->get_labels();
			for ($i = 0; $i < count($labels); $i++) {
				$labels_p[$labels[$i]] = '1';
			}
			$sources_p = $p[0]->get_sources();
			$sources_p[$this->source_id] = '1';
			$p[0]->set_labels($labels_p);
			$p[0]->set_sources($sources_p);
			$p[0]->save();
		} else {
			$p = new program_table();
			$p->set_name($name);
			$p->set_url(str_replace('#anchor', '', $this->get_url()));
			$p->set_poster($poster);
			$p->set_summary($summary);
			$labels_p = '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
			$sources_p = '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
			for ($i = 0; $i < count($labels); $i++) {
				$labels_p[$labels[$i]] = '1';
			}
			$sources_p[$this->source_id] = '1';
			$p->set_labels($labels_p);
			$p->set_sources($sources_p);
			$p->save();
		}

		$d = directory_table :: check('directory', 'url', $this->get_url());
		if ($d) {
			$d->set_closed('1');
			$d->save();
		}
	}

	private function _parse_video_page ($html) {
		$ary = array();
		preg_match('/<div class="crumbs">\s*(.*)<\/div>/siU', $html, $ary);
		$html2 = $ary[1];
		$ary = array();
		preg_match_all('/<a[^>]*>(.*)<\/a>/siU', $html2, $ary);
		$ary = $ary[1];
		$labels = '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
		$sources = '---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
		for ($i = 0; $i < count($ary); $i++) {
			$l = label_table :: check('label', 'name', $ary[$i]);
			if (!$l) {
				$l = new label_table();
				$l->set_name($ary[$i]);
				$l->save();
			}
			$labels[$l->get_id()] = '1';
		}
		$sources[$this->source_id] = '1';

		$ary = array();
		preg_match('/<h1 class="title">\s*(<a.*href\s*=[\'|"](.*)[\'|"][^>]*>(.*)<\/a>)?\s*<span[^>]*>(.*)<\/span>\s*<\/h1>/siU', $html, $ary);
		//echo "<xmp>";var_dump($ary);echo "</xmp>";
		$p = program_table :: check('program', 'url', $ary[2] ? $ary[2] : $this->get_url());
		if (!$p) {
			$p = new program_table();
			$p->set_name($ary[3] ? $ary[3] : $ary[4]);
			$p->set_url($ary[2] ? $ary[2] : $this->get_url());
			$p->set_labels($labels);
			$p->set_sources($sources);
			$p->save();
		}

		$v = video_table :: check('video', 'url', $this->get_url());
		if (!$v) {
			$v = new video_table();
			$v->set_url($this->get_url());
			$v->set_hash(SimpleHash($this->get_url()) % 1000000);
			$v->set_program_id($p->get_id());
			$v->set_source_id($this->source_id);
			$v->save();
		}

		$d = directory_table :: check('directory', 'url', $this->get_url());
		if ($d) {
			$d->set_closed('1');
			$d->save();
		}
	}

	public function save_page_urls () {
		$urls = $this->get_page_urls();
		for ($i = 0; $i < count($urls); $i++) {
			$ary = array();
			if (preg_match('/^http:\/\/([a-z0-9_]*)\.youku\.com\/([^\/]+)(.*)/si', $urls[$i]['url'], $ary)) {
				if ($ary[1] == 'v' && $ary[2] == 'v_show') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($urls[$i]['anchor']);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				} else if ($ary[1] == 'movie' && $ary[2] == 'search') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($this->directory_name);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				} else if ($ary[1] == 'tv' && $ary[2] == 'search') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($this->directory_name);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				} else if ($ary[1] == 'comic' && $ary[2] == 'search') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($this->directory_name);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				} else if ($ary[1] == 'tv' && $ary[2] == 'search') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($this->directory_name);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				} else if ($ary[1] == 'www' && $ary[2] == 'show_page') {
					$d = directory_table :: check('directory', 'url', $urls[$i]['url']);
					if (!$d) {
						$d = new directory_table();
						$d->set_url($urls[$i]['url']);
						$d->set_name($urls[$i]['anchor']);
						$d->set_source_id($this->source_id);
						$d->save();
					}
				}
			}
		}
	}
}
?>
