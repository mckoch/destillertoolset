<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
/* 
the RPC component - mostly wrapper for DTS:: and DTB::
*/ 
	require_once('sessionmanager.php');
	require_once('DESTILLER.INI.php');
	require_once($destillertoolset);
	require_once($languagefile);
	
	require_once('wrapper.inc.php');
	
	$url = $_GET['host'];
	$request = $_GET['action'];
	$accesses = $_GET['save'];
	switch ($request) {
	    case 'http':
	    	require_once('includes/destillerSimpleSingleUrlReport.class.php'); // 
	    	$destiller = new destillerSimpleSingleUrlReport();
	    	$destiller->constructDestiller($url);
	    	switch ($accesses) {
	    		case 'cloud':
	    			DTS::addDestillerObjectToRankingDb($destiller); // cloud must be generated seperately!!!
	    			$crawlerreport = DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords()) ; // for class crawler.class
	    			return DTHelper::listarray($crawlerreport);
		    	break;
		    	case '_instantcloud':
		    		$ddb= new DDB; //$ddb->initializeDataBase(); 
		    		DTS::addDestillerObjectToRankingDb($destiller);
		    		DTHelper->listarray(DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords()) );
		    		DTS::makeCloudUriRanking(); // single document internal ranking
		    		return  DTHelper::listarray($ddb->getRankingTable(20,1));
		    	break;
		    	case '_crawler':
		    		//error_reporting(E_WARNING);
		    		$crawlerreport = DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords()) ; // for class crawler.class
					return  DTHelper::listarray($crawlerreport);
					//die;
		    	break;
		    	case '_spider':
			    	/* spiderFromUrlWithKeyWords opt ($strStartURL, $intCrawlDepth, $maxurl,	$strKeyword, $level02Keyword, 
					$level03Keyword, $level04Keyword, $level05Keyword, $spiderscripttime) */
		    		DTS::spiderFromUrlWithKeyWords($destiller->uri,4,200,$destiller->getKeywords(),0,0,0,0,300); // for class Spider w configurable depth
		    		return true; //for now...
		    	break;
	    	} 
	    	break;
	    case 'cloud':
	    	require_once('header.php');
	    	$t=new DDB();
	    	switch ($accesses) { // all these vars to INI!!!
	    		case 'generate':
	    			DTS::makeCloudUriRanking();
	    		break;
	    		case 'full':
	    			print "TOP 50<pre>";
			    	print $t->getRankingTable(100);
					print "</pre>";
					print $t->getRankingTable(1500,2);
					print "<hr><h1>keywords only: </h1>";
					print $t->getRankingTable(1500,1);
					print "</pre>";
	    		break;
	    		default:
	    			global $nokwrecommendations, $nolinkrecommendations;
		    		print"<h1>TOP $nokwrecommendations KEYWORDS TO USE</h1><div id='pagerank'><pre>";
		    		print $t->getRankingTable($nokwrecommendations,1);
		    		print "</pre><h1>TOP $nolinkrecommendations LINKS TO LINK TO</h1> (OR BETTER GET A LINK FROM...;)<pre>"; 
		    		print $t->getRankingTable($nolinkrecommendations,2);
		    		print "</pre></div>";
	    	}
	    	break;
	    case 'config':
	    	print "<h3>NO PUBLIC INTERFACE AVAILABLE, SORRY.</h3>";
	    	print "<pre>";
	    	// if ($accesses=='view') print_r($GLOBALS);
	    	print "</pre>";
	    	break;
	    case 'quick':
	    	require_once('includes/quick.inc.php');
	    	break;
	    case 'about':
	    	require_once('includes/about.inc.php');
	    	break;
	    case 'spider':
	    	//http://../?action=spider&host=http://slashcam.de/&level=4&&max=150&maxtime=120&level01=sony&level02=/test/&level03=/sony/&level04=/test/
	    	$strStartURL = $url; //"http://slashcam.de/";
			$strKeyword = $_GET['level01']; //// level01Keyword - NOTICE: ALL regex!!!!!!! , at least .  
			$level02Keyword = $_GET['level02']; //"/kamera|hdv|neu|pop/";
			$level03Keyword = $_GET['level03']; //   /./ at least!
			$level04Keyword = $_GET['level04']; //
			$level05Keyword = $_GET['level05']; //
			$intCrawlDepth= $_GET['level']; //
			$maxurl=$_GET['max'];
			$spiderscripttime = $_GET['maxtime']; //90; // time limit for spider in seconds, to GLOBALS
	    	DTS::spiderFromUrlWithKeyWords($strStartURL, $intCrawlDepth, $maxurl, 
				$strKeyword, $level02Keyword, $level03Keyword, $level04Keyword, 
					$level05Keyword, $spiderscripttime);
	    	break;
	    case 'find': // raw search page.
	    	if ($accesses != ''){
				$ddb= new DDB;
				print "<pre>";
				print $ddb->findKeyWordInDb($accesses); //TODO: mmore SECURITY! partially fixed in sessionmanager.php
				print "</pre>";
	    	}
	    	break;
	    default:
	    	if ($accesses=='_reset'){
		    	session_destroy();
				echo "<h3>SESSION RESET SUCCESSFULLY.</h3>";
	    	} else {
	    		echo "ERROR";
	    	}
	    	break;
	}
die;


?>