<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
require_once('includes/crawler/phpcrawler.class.php');
class keywordCrawler extends PHPCrawler 
{	
	public $arrKeywords;
	private $filehandle;
	private $db;
	public function __construct(){
	$this->db = new DDB;
	}
	public function setKeywords($arrRegEx){
		if ($this->arrKeywords = $arrRegEx)
		return true;	
	}	
	public function getLogHandle($filehandle) {
		$this->filehandle = $filehandle;
	}
	public   function handlePageData(&$page_data) 
  {    
    /* in $pagedata[]: 
    link_raw - contains the raw link as it was found.
	url_rebuild - contains the full qualified URL the link leads to
	linkcode - the html-codepart that contained the link.
	linktext - the linktext the link was layed over (may be empty). 
	*/ 
	//$destillerdb = new DDB; 
	//error_reporting(E_ALL);
	//global $destiller;
	print "<hr>fetched new:".$page_data['url'];
	//print_r( $page_data); print "</hr>";
	fwrite($this->filehandle, $page_data['url']."\n");
	$crawldata='';
    foreach($page_data['links_found'] as $link) {
	$db = $this->db;
	$db->toRankingDb($link["referer_url"],$link['url_rebuild']); // basic: link to ranking DB
	
    	//$rawlink = $link['link_raw']; // switch between oth methods!!!!!
    	$rawlink = $link['linktext'];

		foreach ($this->arrKeywords as $regEx=>$keyword){ 
		 if (preg_match($regEx,$rawlink)) {
		 	if (@!$_GET['silent']){
			 	//print "<li>MATCH: ".$regEx." on ".$keyword."<br>".$link['url_rebuild']."</li><hr><script language=javascript>window.scrollTo(0,9e9);</script>";
			 	print "<li>[MATCH: ".$regEx." on ".$rawlink."]</li>";//<script language=javascript>window.scrollTo(0,9e9);</script>";
				//ob_flush();flush();
			} 
			//$_SESSION['crawldata'][$keyword][] = $link['url_rebuild'];
	//		$db = $this->db;
			$db->toRankingDb($link['url_rebuild'],$keyword); 
			//$link = $link['url_rebuild'];
			//$destiller->addCrawlData(array($keyword=>$link));
			$crawldata[$keyword][] = $link['url_rebuild'];
			}
		}	
    }

    //die;
    if (@!$_GET['silent']){
	    print "<script language=javascript>window.scrollTo(0,9e9);</script>";
	    //echo str_pad(" ", 5000); // "Force flush", workaround
	    //ob_flush();flush();
    }
    return $crawldata;
  }
}
?>