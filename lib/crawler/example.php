<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Crawler Example usage</title>
	<style type="text/css" media="all">
		.node {
			font-weight: bold;		
		}
		.leaf {
			
		}
		
		body, a {
			color: #6E6E6E;
			background-color: #fefefe;
		}	
	</style>
</head>
<body>       
<?php
set_time_limit('0');
include_once 'Crawler.php';

/**
 * Basic usage
 */

#########################################################################
/* Example 1
$c = new Crawler();
$c->process('http://phpclasses.org/');
echo $c->getHtmlSiteMap();
//*/


/**
 * More advanced usage
 */

#########################################################################
///* Example 2

$c = new Crawler();
//set options
$c->setScanDepth(1)
	->setFollowExternalLinks(true)
	->addExcludedLink('advertising$') //exclude all the links, which in the address (at the end) have  "advertising"
	->addExcludedAnchor('^1$') //exclude all the links, which in the text anchor (is equal) have 1 (page bar) 
	->addExcludedAnchor('^2$')
	->addExcludedAnchor('^3$')
	->addExcludedAnchor('^4$')
	->addExcludedAnchor('^5$')
	->addExcludedAnchor('^6$');

//run page scan and get links array	
$rawLinks = $c->process('http://hz.youku.com/red/click.php?tp=1&cp=4000281&cpp=1000191&url=http://v.youku.com/v_show/id_XNDQ3NTk2OTA0.html')->getLinks();
//display themed pageMap
echo $c->getHtmlSiteMap();
// */


#########################################################################
/* Example 3 - using Callbacks

$c = new Crawler();

class LinkParseCallback {

	public $links = array();

	public function fire($data, $url)
	{
		$this->links[$url][] = $data; 
	}
}
//$tc->fire() is called after exclusions and before node item is build
$tc = new LinkParseCallback();

$c->addOnLinkParseCallback($tc);

class ProcessPageCallback
{
	private $data = array();
	
	public function fire($head, $body, $url)
	{
		//$this->data[$url] = array('head' => $head, 'body' => $body);
		$this->data[$url] = $head;
	}
}

$ppc = new ProcessPageCallback();
$c->addOnProcessPage($ppc);


$c->setScanDepth(3)
	->setFollowExternalLinks(false)
	->addExcludedLink('JPG$')
	->addExcludedAnchor('^Free CSS Templates');

$c->process('http://falsztyn.boo.pl');

//var_dump($ppc);
//var_dump($tc);

//display themed pageMap
echo $c->getHtmlSiteMap();
// */
?>
</body>
</html>