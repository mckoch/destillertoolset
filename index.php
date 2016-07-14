<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
	/* init script for class test */
	//require_once('includes/IDS.include.php');
	// include the session manager class: NOTE: Session DB is and MUST be accessed seperately !!!
	require_once('sessionmanager.php');
	require_once('DESTILLER.INI.php');
	require_once($destillertoolset);
	//require_once('DestillerToolsetHelper.class.php');
	require_once($languagefile);
	require_once('includes/errorhandler/Error.class.php');
	$ERROR= new Error;
	$url = $_GET['host'];
	$request = $_GET['action'];
	$accesses = $_GET['save'];
		switch ($request) {
			//case 'mytest': print DTHelper::makeForm(); //die;
		    case 'http':
		    	require_once('header.php');
		    	require_once('includes/destillerSimpleSingleUrlReport.class.php'); // 
		    	$destiller = new destillerSimpleSingleUrlReport();
		    	$destiller->constructDestiller($url);
		    	switch ($accesses) {
		    		case 'cloud':
		    			DTS::addDestillerObjectToRankingDb($destiller); // cloud must be generated seperately!!!
		    			print DTHelper::listarrayrecurse(DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords())) ; // for class crawler.class
		    			print DTHelper::listarrayrecurse($_SESSION['crawldata']);
			    	break;
			    	case 'instantcloud':
			    		$ddb= new DDB; //$ddb->initializeDataBase(); unset($ddb); // : clear the ranking database!
			    		$ddb->getRankingTable(20,1);
			    		DTS::addDestillerObjectToRankingDb($destiller);
			    		print DTHelper::listarrayrecurse(DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords()) );
			    		print DTHelper::listarrayrecurse($_SESSION['crawldata']);
			    		DTS::makeCloudUriRanking(); // single document internal ranking
			    		print $ddb->getRankingTable(20,1);
			    	break;
			    	case '_crawler':
			    		error_reporting(E_WARNING);
			    		$crawlerreport = DTS::crawlFromUrlWithKeyWords($url,$destiller->getKeywords()) ; // for class crawler.class
			    		print "<hr>";
						print DTHelper::listarrayrecurse($crawlerreport);
						$_SESSION['crawlreports'][$url][] = $crawlerreport; 
			    		print "<hr>";
			    		print DTHelper::listarrayrecurse($_SESSION['crawlreports']);
						//die;
			    	break;
			    	case '_spider':
				    	/* spiderFromUrlWithKeyWords opt ($strStartURL, $intCrawlDepth, $maxurl,	$strKeyword, $level02Keyword, 
						$level03Keyword, $level04Keyword, $level05Keyword, $spiderscripttime) */
			    		DTS::spiderFromUrlWithKeyWords($destiller->uri,4,200,$destiller->getKeywords(),0,0,0,0,300); // for class Spider w configurable depth
			    	break;
		    	} 
		    	require_once('destillerSingleUrlReportTemplate.php');
		    	require_once('footer.php');
		    	DTS::saveDestillerObjectToSession($destiller); //$destiller=serialize($destiller);
		    	break;
		    case 'cloud':
		    	require_once('header.php');
		    	$t=new DDB();
		    	switch ($accesses) { // all these vars to INI!!!
		    		case 'generate':
		    			DTS::makeCloudUriRanking();
		    			print DTHelper::makeFindForm();
		    		break;
		    		case 'full':
		    			print "TOP 50<hr>";
				    	print DTHelper::listarray($t->getRankingTable(100));
						print "</pre>";
						print $t->getRankingTable(1500,2);
						print "<hr><h1>keywords only: </h1>";
						print $t->getRankingTable(1500,1);
						print "<hr>";
						print DTHelper::makeFindForm();
		    		break;
		    		default:
		    			global $nokwrecommendations, $nolinkrecommendations;
		    			//print "<a href=http://192.168.103.234/DestillerV05/?action=cloud&save=generate>please click here to (re-)generate your session cloud's ranking!</a>";
		    			print DTHelper::makeGenerateForm();
			    		print"<h1>TOP $nokwrecommendations KEYWORDS TO USE</h1><div id='pagerank'><hr>";
			    		print DTHelper::listarrayrecurse($t->getRankingTable($nokwrecommendations,1));
					//print_r($t->getRankingTable($nokwrecommendations,1));
			    		print "<hr><h1>TOP $nolinkrecommendations LINKS TO LINK TO</h1> (OR BETTER GET A LINK FROM...;)"; 
			    		print DTHelper::listarrayrecurse($t->getRankingTable($nolinkrecommendations,2));
			    		print "</div>";
			    		print DTHelper::listarrayrecurse($_SESSION['crawlreports']);
			    		print DTHelper::makeFindForm();
		    	}
		    	require_once('footer.php');
		    	break;
		    case 'config':
		    	require_once('header.php');
		    	print "<h3>user servicable runtime config</h3>";
		    	global $params;
		    	print DTHelper::listarrayrecurse($params);
		    	require_once('includes/destillerSimpleSingleUrlReport.class.php');
		    	$objects = array('DTS','DTHelper','DDB','destillerSimpleSingleUrlReport','TextStatistics','Snoopy','simple_html_dom','gRank','keywordCrawler','autokeyword','spiderScraper','dbsession');//,'Error');
		    	foreach ($objects as $obj) {
		    		$i++; 
		    	//	try { $obj = new $obj;}
			    //  	catch (Exception $e) {
				//    echo 'Caught exception: ',  $e->getMessage(), "\n";
				//	} throw ('what da hel...');
			    	if ($obj = new $obj){
			    		//CAVE: this will evtly. list PASSWORD for database!!!!
						 print "<hr>".$i.get_class($obj).DTHelper::listarrayrecurse(get_class_methods($obj));
				    	 print "<hr>".$i.DTHelper::listarrayrecurse($obj); $n++;
	    			} else print DTHelper::listarrayrecurse(debug_print_backtrace());
		    	}
				print DTHelper::listarrayrecurse(get_included_files());
				print DTHelper::listarrayrecurse($_SESSION);
				//print DTHelper::listarrayrecurse(getrusage());
				//print DTHelpprint DTHelper::listarrayrecurse(mysql_list_dbs ());
				//print DTHelper::listarrayrecurse(debug_print_backtrace());
				print DTHelper::listarrayrecurse(error_get_last());
				 print "<hr><li> $i of $n: system seems healthy. Good.</li>";
		    	require_once('footer.php');
		    	break;
		    case 'quick':
		    	require_once('header.php');
		    	require_once('includes/quick.inc.php');
		    	require_once('footer.php');
		    	break;
		    case 'about':
		    	require_once('header.php');
		    	require_once('includes/about.inc.php');
		    	require_once('footer.php');
		    	break;
		    case 'spider':
		    //error_reporting(E_ALL);
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
		    	require_once('header.php');
		    	DTS::spiderFromUrlWithKeyWords($strStartURL, $intCrawlDepth, $maxurl, 
					$strKeyword, $level02Keyword, $level03Keyword, $level04Keyword, 
						$level05Keyword, $spiderscripttime);
		    	require_once('footer.php');
		    	break;
		    case 'find': // raw search page.
		    	require_once('header.php');
		    	if ($accesses != ''){
					$ddb= new DDB;
					print "<pre>";
					print $ddb->findKeyWordInDb($accesses); //TODO: SECURITY!!!!!!!!
					print "</pre>";
		    	}
		    	print DTHelper::makeFindForm();
		    	print DTHelper::makeDestillerForm();
				
				print DTHelper::makeResetForm(); 
		    	require_once('footer.php');
		    	break;
		    default:
		    	require_once('header.php');
		    	if ($accesses=='initialize'){
		    		global $ERROR;
			    	$ERROR->flushErrors();
					unset($_SESSION['keywords']); // = 'init ';
					unset($_SESSION['urlhistory']);	
			    	$ddb= new DDB;
			    	$ddb->initializeDataBase($_SESSION['userrankingdatabase']); // : clear the ranking database!
					//unset($ddb);
					echo "<h3>SESSION DATABASE RESET SUCCESSFULLY.</h3>To delete all your data simply get rid of Destiller's cookies....";
					print DTHelper::makeDestillerForm();
		    	} else {
		    		print DTHelper::makeResetForm();
		    		echo "i equals 0. not initialized. superbowl succeeded... oooops: some <strong>harsh grims</strong> detected, try again?<br>";
		    		echo "default of ";
		    		print DTHelper::makeDestillerForm(); 
		    		print DTHelper::makeGenerateForm();
		    		print DTHelper::makeFindForm();
		    		
		    	}
		    	require_once('footer.php');
		    	break;
		}
	die;
?>
