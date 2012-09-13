<?php

include_once 'CrawlerReader.php';

/**
 * This class can be used to search for links with text anchors on the referenced page.
 * The result may be returned as an associative array or as styled HTML (siteMap)
 * 
 * Furthermore class has the following features: 
 * - Adjustable depth of the search
 * - The ability to specify whether the search may include external links
 * - The ability to use your own reader class, instead of embedded
 * - Links exclusion mechanism. Filtering by url and/or anchor by regular expr 
 * - Ability to run user callback functions on different stages of implementation (When the link is parsed and when the page i read). Perform custom operation on raw data.
 * 
 * Copyright (c) 2011 Jacek Lukasiewicz (jlukasie@gmail.com)
 * All rights reserved.
 *
 * @category      Library
 * @package       PageCrawler
 * @copyright     Copyright (c) 2011 Jacek Lukasiewicz (jlukasie@gmail.com)
 * @version       2.1
 * @license       New BSD License
 */
class Crawler {
	 
	/**
	 * represents reder object
	 * @var IReader
	 */
	private $_reader;
	
	/**
	 * should Crawler follow external links
	 * @var boolean
	 */
	private $_followExternalLinks = false;
	
	/**
	 * scan depth
	 * @var int
	 */
	private $_scanDepth = 1;
	
	/**
	 * Contains processed page(s) links 
	 * @var array
	 */
	private $_links = array();
	
	/**
	 * Contains page(s) words 
	 * @var array
	 */
	private $_words = array();
	
	/**
	 * Site base url
	 * @var string
	 */
	private $_siteBaseUrl = '';
	
	/**
	 * contains current url
	 * @var unknown_type
	 */
	private $_currentUrl;
	
	/**
	 * All links table
	 * @var array
	 */
	private $_allLinks = array();

	/**
	 * url exclude patterns (regular expressions)
	 * @var array
	 */
	private $_excludedLinks = array();

	/**
	 * array of excluded anchors  (regular expressions)
	 * @var array
	 */
	private $_excludedAnchors = array();
	
	/**
	 * collect only unique links
	 * @var bool
	 */
	private $_ignoreDuplicates = true;
	
	
	/**
	 * Contains onLinkParse calbacks  
	 * @var array
	 */
	private $_onLinkParse = array();
	
	/**
	 * Conatins processPage callbacks
	 * @var array
	 */
	private $_onProcessPage = array();
	
	
	public function __construct()
	{
		if(func_num_args() == 0)
		{
			$this->_reader = new CrawlerReader();
		}
		elseif(func_num_args() == 1)
		{
			$this->_reader = func_get_arg(0);
		}
 
	}
	
	
	/**
	 * setter for $this->_followExternalLinks
	 * @param boolean $value
	 */
	public function setFollowExternalLinks($value)
	{
		$this->_followExternalLinks = $value;
		return $this;
	}
	
	/**
	 * setter for $this->_scanDepth
	 * @param integer $value
	 */
	public function setScanDepth($value)
	{
		$this->_scanDepth = $value;
		return $this;
	}
	
	/**
	 * $this->_excludedLinks setter
	 * regular expression pattern
	 * @param string $pattern
	 */
	public function addExcludedLink($pattern)
	{
		$this->_excludedLinks[] = $pattern;
		return $this;
	}
	
	/**
	 * $this->_excludedAnchors setter
	 * regular expression pattern
	 * @param string $pattern
	 */
	public function addExcludedAnchor($pattern)
	{
		$this->_excludedAnchors[] = $pattern;
		return $this;	
	}
	
	
	public function addOnLinkParseCallback($callbackObject)
	{
		$this->_onLinkParse[] = $callbackObject;
		return $this;
	}
	
	public function addOnProcessPage($callbackObject)
	{
		$this->_onProcessPage[] = $callbackObject;
		return $this;
	}
	
	/**
	 * Setup and run scan
	 * @param string $url
	 */
	public function process($url)
	{
		$parsedUrl = parse_url($url);
		$this->_siteBaseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

		$this->_links = $this->_scan($url, $this->_scanDepth);
		return $this;
	}
	
	/**
	 * Scan pages
	 * @param string $url
	 */
	private function _scan($url, $depth)
	{

		//$this->_scanDepth--;
		$depth--;
		$urls = array();
		
		if(!is_array($url))
		{
			$urls[] = array( 'url' => $url, 'anchor' => 'START');
		}		
		else
		{
			$urls = $url;
		}
		
		foreach($urls as $k => $u)
		{
			//check for external links
			if(!$this->_followExternalLinks) 
			{
				if(preg_match('~^' . $this->_siteBaseUrl . '~', $u['url']) == 0)
				{
					continue;
				}
			}
		
			$urls[$k]['children'] = $this->_processPage($u['url']);
			//if($this->_scanDepth >= 1 && !empty($urls[$k]['children']))
			if($depth >= 1 && !empty($urls[$k]['children']))
			{
				$urls[$k]['children'] = $this->_scan($urls[$k]['children'], $depth);
				
			}
		}
		
		return $urls;
	}
	
	/**
	 * Process/Scan single Page for children
	 * @param string $url
	 */
	private function _processPage($url, $anchor='') 
	{
		$this->_currentUrl = $url;
		
		if(!$htmlContent = $this->_reader->read($url))
		{
			return array();	
		}
		$body = $this->_findDocumentBody($htmlContent);
		$head = $this->_findDocumentHead($htmlContent);

		//run registered callbacks
		if(!empty($this->_onProcessPage))
		{
			foreach($this->_onProcessPage as $cObj)
			{
				$cObj->fire($head, $body, $this->_currentUrl);
			}
		}
		
		
		$children = $this->_grabLinks($body);

		return $children;
	}
	
	/**
	 * Getter for this._links
	 */
	public function getLinks()
	{
		return $this->_links;
	}
	

	/**
	 * 
	 * find document <body> part
	 * @param string $content
	 */
	private function _findDocumentBody($content) {
		$matches = array();
		preg_match('/(<body\s?[^>]*>)(.*)(<\/body>)/is', $content, $matches);
		if (!$matches[2]) {
			preg_match('/(<body\s?[^>]*>)(.*)(<\/body>)/isU', $content, $matches);
		}
		return $matches[2];
	}	

	
	/**
	 * find document <head> part
	 */
	private function _findDocumentHead($htmlContent) {
		$matches = array();
		preg_match('/(<head>)(.*)(<\/head>)/si', $htmlContent, $matches);
		if (!$matches[2]) {
			preg_match('/(<head>)(.*)(<\/head>)/isU', $htmlContent, $matches);
		}
		return $matches[2];
	}
		
	/**
	 * collect links from the body
	 * @param string $content
	 */
	private function _grabLinks($content) {
		
		$links = array();
		$matches = array();
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>"; 
		preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER);
		if(!empty($matches)) {
			foreach ($matches as $link) {
				$tmpArray =  $this->_parseLinkData($link);
				
				
				if(empty($tmpArray) 
					|| (in_array($tmpArray['url'], $this->_allLinks) && $this->_ignoreDuplicates) 
					|| empty($tmpArray['anchor']))
				{
					continue;
				}
				
				$this->_allLinks[] = $tmpArray['url'];
				$links[] = $tmpArray;
				
			}
		}
		return 	$links;	
	}	
	
	/**
	 * Process any link
	 * @param array $linkData
	 */
	private function _parseLinkData(array $linkData) {
		$fullUrl = $linkData[0];
		$anchor = strip_tags($linkData[3]);	
		
		if(preg_match('/^http/', $linkData[2])) 
		{
			$url = $linkData[2];
		}
		else
		{
			$url = $this->_siteBaseUrl . $linkData[2];
		}
		
		//check for url excludes
		if(!empty($this->_excludedLinks))
		{
			foreach ($this->_excludedLinks as $el)
			{
				if(preg_match('~'. $el .'~', $url))
				{
					return false;
				}
			}
		}
		
		//anchor excludes
		if(!empty($this->_excludedAnchors))
		{
			foreach ($this->_excludedAnchors as $ea)
			{
				if(preg_match('~'. $ea .'~', $anchor))
				{
					return false;
				}
			}
		}
		
		//run registered callbacks
		if(!empty($this->_onLinkParse))
		{
			foreach($this->_onLinkParse as $cObj)
			{
				$cObj->fire($linkData, $this->_currentUrl);
			}
		}
		
		return array('url' => $url, 'anchor' => $anchor);
	}


	public function getHtmlSiteMap()
	{
		if(empty($this->_links))
		{
			return;
		}
		$output = $this->getHtmlMapNode($this->_links);
		
		return $output;
	}
	
	private function getHtmlMapNode($node)
	{
		$output = "<ul>";
		foreach ($node as $link) 
		{
			if(!empty($link['children']))
			{
				$children = $this->getHtmlMapNode($link['children']);
				$cssClass = "node";
			}
			else
			{
				$children = '';
				$cssClass = "leaf";
			}
			
			$output .= "<li class=\"" . $cssClass ."\">";
			$output .= "<a href=\"" . $link['url'] . "\">" . $link['anchor'] ."</a>";
			$output .= "</li>";
			$output .= $children;

		}
		$output .= "</ul>";
		
		return $output;
	}
	
	
}
