<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
if (stristr(htmlentities($_SERVER['PHP_SELF']), "DestillerToolSet.class.php")) {
    Header("Location: index.php");
    die();
}
//Destiller Tool Set DTS::
/* START REQUIREMENTS - DTS extensions and 3rd party code */
//require_once('includes/errorhandler/Error.class.php'); // doesnt work.
require_once('includes/accesscomp/access.class.php'); //the user component, still testing
require_once('includes/httpfetcher/Snoopy.patched.class.php'); // HTTP helper class
require_once("includes/spider/spiderClass.php"); // the spidering engine, NOT using Snoopy.class
require_once("includes/crawler.class.php");
require_once('includes/libtextcat/saddorlibtextcat.class.php'); // helper to statistically guess language
require_once('includes/domabstractor/simple_html_dom.php'); // DOM helper class
require_once('includes/autokeywords/class.autokeyword.php'); // keyword helper class
require_once('includes/textstatistics/TextStatistics.patched.class.php'); // modified textstatistics helper class
require_once('includes/charset/ConvertCharset.class.php'); // charset conversion utility
require_once("includes/pagerank/gRank.patched.class.php"); // the ranking class, from guaranix		
require_once("includes/API/googlekeywordposition/keywordPosition.class.php"); //guess what.....
require_once("includes/API/googlepagerankcheck/class.googlepr.php"); 
require_once("includes/API/class.twitter.php"); 
require_once("includes/url/url_to_absolute.php");
require_once('includes/nic/domain.class.php'); // WHOIS utility
require_once('DestillerDataBase.class.php'); //ADODB + MySql??!!!
require_once('DestillerToolsetHelper.class.php'); // formatter for output.
/* END REQUIREMENTS */
/* START FUNCTION LIB - GLOBAL destiller functions, no configuration needed! */
class DTS {
	public function __construct() {
		return true;
		}	
	/* START FUNCTION LIB - GLOBAL destiller functions, no configuration needed! 
	- used by class destillerSimpleSingleUrlReport */
	public static function httpGetter($url){
		global $useragent;
		$getscriptstart = microtime(true);
		$httpdoc = new Snoopy;
		$httpdoc->agent = $useragent;
		$httpdoc->fetch($url);
		$getscriptend = microtime(true);
		$document['responsetime'] = $getscriptend - $getscriptstart;
		$document['host']=$httpdoc->host;//string
		$document['agent']=$httpdoc->agent;//string
		$document['referer']=$httpdoc->referer;//string
		$document['rawheaders']=$httpdoc->rawheaders; //array
		$document['lastredirectaddr']=$httpdoc->lastredirectaddr;//string
		$document['response_code']=$httpdoc->response_code;//string
		$document['status']=$httpdoc->status; //string
		$document['error']=$httpdoc->error; //string
		$document['timed_out']=$httpdoc->timed_out; //string
		$document['results']=$httpdoc->results; //full HTTP GET/POST result, string
		$httpdoc->fetchlinks($url);
		$document['linklist']= array_unique($httpdoc->results);
		$document['headers']=$httpdoc->headers; //array
		unset($httpdoc); // saves memory....
		if ($_GET['getwhoisinformation']==TRUE) {$document['whoisinformation']= self::makeWhoisInformation($url);} //string (formatted text) 
		return $document;
	}
	public static function makeRawKrI($nolinks, $documentlength, $textlength){
		if ($nolinks<1) {$nolinks=1;}
		$kri=$textlength/$documentlength*$nolinks; // the true KrI.... to be sharpened.This is RAW_KrI
		return $kri;
	}
	public static function makeKeyWords($doc, $params, $encoding){
		$params['content'] = $doc; //page content
		$keyword = new autokeyword($params, $encoding); // replace by document charset, but lateron....
		$suggestedKeyWords = $keyword->get_keywords();
		//print_r(explode(',', $suggestedKeyWords));
		return explode(',', $suggestedKeyWords);	
	}
	public static function makeRelatives($linklist, $keywords, $headlines, $imagelist){
		//$keywords = explode(',',$keywords);
		$relatives = array_merge((array)$linklist, (array)$keywords);
		$relatives = array_merge((array)$relatives, (array)$headlines);
		$relatives = array_merge((array)$relatives, (array)$imagelist);
		//print_r($relatives); die;
		return $relatives;
	}
	public static function makeTextStatistics($hometext, $encoding){
		global $defaultcharset;
		$statistics = new TextStatistics($encoding, $hometext);
		$textstatistics = '';
		$textstatistics['flesch_kincaid_reading_ease'] =  $statistics->flesch_kincaid_reading_ease;
		$textstatistics['flesch_kincaid_grade_level'] = $statistics->flesch_kincaid_grade_level;
		$textstatistics['gunning_fog_score'] = $statistics->gunning_fog_score;
		$textstatistics['coleman_liau_index'] = $statistics->coleman_liau_index;
		$textstatistics['smog_index'] = $statistics->smog_index;
		$textstatistics['automated_readability_index'] = $statistics->automated_readability_index;
		$textstatistics['text_length'] = $statistics->text_length;
		$textstatistics['letter_count'] = $statistics->letter_count;
		$textstatistics['sentence_count'] = $statistics->sentence_count;
		$textstatistics['word_count'] = $statistics->word_count;
		$textstatistics['average_syllables_per_word'] =  $statistics->average_syllables_per_word;
		$textstatistics['words_with_three_syllables'] = $statistics->words_with_three_syllables;
		$textstatistics['percentage_words_with_three_syllables'] = $statistics->percentage_words_with_three_syllables;
		$textstatistics['get_unique_word_count'] = $statistics->get_unique_word_count;
		$textstatistics['wordlist'] = $statistics->get_word_list;
		unset($statistics);
		return $textstatistics;
	}
	
		public static function normalizeUtf8String( $s)
	{
		$original_string = $s;
	    // Normalizer-class missing!
	     /*if (! class_exists("Normalizer", $autoload = false))
	      //print "shit!"; die;
	        return $original_string;
	   */
	   
	    // maps German (umlauts) and other European characters onto two characters before just removing diacritics
	    $s    = preg_replace( '@\x{00c4}@u'    , "AE",    $s );    // umlaut Ä => AE
	    $s    = preg_replace( '@\x{00d6}@u'    , "OE",    $s );    // umlaut Ö => OE
	    $s    = preg_replace( '@\x{00dc}@u'    , "UE",    $s );    // umlaut Ü => UE
	    $s    = preg_replace( '@\x{00e4}@u'    , "ae",    $s );    // umlaut ä => ae
	    $s    = preg_replace( '@\x{00f6}@u'    , "oe",    $s );    // umlaut ö => oe
	    $s    = preg_replace( '@\x{00fc}@u'    , "ue",    $s );    // umlaut ü => ue
	    $s    = preg_replace( '@\x{00f1}@u'    , "ny",    $s );    // ñ => ny
	    $s    = preg_replace( '@\x{00ff}@u'    , "yu",    $s );    // ÿ => yu
	   
	   
	    // maps special characters (characters with diacritics) on their base-character followed by the diacritical mark
	        // exmaple:  Ú => U´,  á => a`
	    //$s    = Normalizer::normalize( $s, Normalizer::FORM_D );
	   
	   
	    $s    = preg_replace( '@\pM@u'        , "",    $s );    // removes diacritics
	   
	   
	    $s    = preg_replace( '@\x{00df}@u'    , "ss",    $s );    // maps German ß onto ss
	    $s    = preg_replace( '@\x{00c6}@u'    , "AE",    $s );    // Æ => AE
	    $s    = preg_replace( '@\x{00e6}@u'    , "ae",    $s );    // æ => ae
	    $s    = preg_replace( '@\x{0132}@u'    , "IJ",    $s );    // ? => IJ
	    $s    = preg_replace( '@\x{0133}@u'    , "ij",    $s );    // ? => ij
	    $s    = preg_replace( '@\x{0152}@u'    , "OE",    $s );    // Œ => OE
	    $s    = preg_replace( '@\x{0153}@u'    , "oe",    $s );    // œ => oe
	   
	    $s    = preg_replace( '@\x{00d0}@u'    , "D",    $s );    // Ð => D
	    $s    = preg_replace( '@\x{0110}@u'    , "D",    $s );    // Ð => D
	    $s    = preg_replace( '@\x{00f0}@u'    , "d",    $s );    // ð => d
	    $s    = preg_replace( '@\x{0111}@u'    , "d",    $s );    // d => d
	    $s    = preg_replace( '@\x{0126}@u'    , "H",    $s );    // H => H
	    $s    = preg_replace( '@\x{0127}@u'    , "h",    $s );    // h => h
	    $s    = preg_replace( '@\x{0131}@u'    , "i",    $s );    // i => i
	    $s    = preg_replace( '@\x{0138}@u'    , "k",    $s );    // ? => k
	    $s    = preg_replace( '@\x{013f}@u'    , "L",    $s );    // ? => L
	    $s    = preg_replace( '@\x{0141}@u'    , "L",    $s );    // L => L
	    $s    = preg_replace( '@\x{0140}@u'    , "l",    $s );    // ? => l
	    $s    = preg_replace( '@\x{0142}@u'    , "l",    $s );    // l => l
	    $s    = preg_replace( '@\x{014a}@u'    , "N",    $s );    // ? => N
	    $s    = preg_replace( '@\x{0149}@u'    , "n",    $s );    // ? => n
	    $s    = preg_replace( '@\x{014b}@u'    , "n",    $s );    // ? => n
	    $s    = preg_replace( '@\x{00d8}@u'    , "O",    $s );    // Ø => O
	    $s    = preg_replace( '@\x{00f8}@u'    , "o",    $s );    // ø => o
	    $s    = preg_replace( '@\x{017f}@u'    , "s",    $s );    // ? => s
	    $s    = preg_replace( '@\x{00de}@u'    , "T",    $s );    // Þ => T
	    $s    = preg_replace( '@\x{0166}@u'    , "T",    $s );    // T => T
	    $s    = preg_replace( '@\x{00fe}@u'    , "t",    $s );    // þ => t
	    $s    = preg_replace( '@\x{0167}@u'    , "t",    $s );    // t => t
	   
	    // remove all non-ASCii characters
	    $s    = preg_replace( '@[^\0-\x80]@u'    , "",    $s );
	   
	   
	    // possible errors in UTF8-regular-expressions
	    if (empty($s))
	        return $original_string;
	    else
	        return $s;
	}

	
	public static function makeDomStatistics($doc){
		global $url;
		error_reporting('E_ALL');
		$newdoc = html_entity_decode($doc, ENT_NOQUOTES, 'UTF-8');
		//$newdoc= self::makeAbsoluteUrl($newdoc, $url);
		//print $newdoc; die;
		//$newdoc = self::normalizeUtf8String($newdoc);
		//print $doc; //die;
		$html = str_get_html($newdoc);
		//print_r($html); //die;
		$domstatistics = array('imagelist'=>array(), 'linklist'=>array(), 'headlines'=>array(), 'plaintext', 'tagged' => array(), 
			'forms' => array(), 'meta' => array(), 'imagelistfullurl' => array(), 'linklistabsolute'=>array()); 
		$domstatistics['plaintext'] =  $html->plaintext;
		/* 
		this DOM block shoud be moved to a function.
		params array for DOM elements to INI.
		*/
		foreach($html->find('img') as $e){
	    	if (strlen($e->alt) > 5) {
				array_push($domstatistics['imagelist'], array(self::makeAbsoluteUrl($e->src, $url) => $e->alt)); 
			} elseif (strlen($e->description) > 5) {
				array_push($domstatistics['imagelist'], array(self::makeAbsoluteUrl($e->src, $url) => $e->description)); 
			} else {
				array_push($domstatistics['imagelist'], array(self::makeAbsoluteUrl($e->src, $url) => 0));//$e->src);	
			}
		}
		//print jDThelper::listArray($domstatistics['imagelist']); print_r($domstatistics);die;
		/* add a href to link array, but: description to destiller->imagelist !! */
		foreach($html->find('a') as $e){
			// first add to LINKLIST !!!
			array_push($domstatistics['linklist'], self::makeAbsoluteUrl($e->href, $url));
			// this to get the description from a (href?) tags into keywords/database; array linklist actually set to vals from snoopy!
			/* should be taken to opt() in INI */
			if (strlen($e->description) > 5) { // tomin_length from $GLOBAL['params']
				// alt description ->makes the array destiller->imagelist a meta container!!!!!
				array_push($domstatistics['imagelist'], array(self::makeAbsoluteUrl($e->href, $url) => $e->description)); // treats description as headline!!!
				// TODO: add description to keywords 		
			} else {}//nothing.
		
		}
		foreach($html->find('h1, h2, h3, h4') as $e){
			$t=$e->plaintext;
	        array_push($domstatistics['headlines'],$t);
		}
		/* foreach($html->find('p, ul, ol') as $e){
			$t=$e-> plaintext;
	        array_push($domstatistics['tagged'],$t);
		} */
		foreach($html->find('form') as $e){
			$t=$e->plaintext;
	        array_push($domstatistics['forms'],$t);
		}
		foreach($html->find('meta') as $e){
			$name=$e->name; $content=$e->content;  
			$push[$name]=$content;
	        array_push($domstatistics['meta'],$push);
	        unset($push);
		}
		foreach($html->find('[http-equiv]') as $e){
			$push['http-equiv']=$e->content;
			array_push($domstatistics['meta'],$push);
	        unset($push);
		}
		$html->clear(); 
		unset($html);
		return $domstatistics;
	}
	public static function makeFootPrint($doc){
		$doc= implode(' ', $doc);
		return $doc;
	}
	/*  Helper  to get DomainName from URL*/
	public static function stripDomainFromUrl($url) {$url=parse_url($url,PHP_URL_HOST);$url=explode('.', $url);
			$url=array_reverse($url); $url=$url[1].'.'.$url[0]; return $url;
	}
	public static function makeWhoisInformation($url){
		$domaininfo = new domain(self::stripDomainFromUrl($url));
		$domaininfo = $domaininfo->info();
		return $domaininfo;
	} 
	public static function makeGuessedLanguage($doc) {
		$libtext = new SaddorLibTextCat();
		$libtext->WhatLang($doc);
		return $libtext->ranking;
	}
	public static function makeAbsoluteUrl($rel, $base) //original by http://nashruddin.com -> convert relative to absolte URL
	{
	    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
	    if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;
	    $rel = extract(parse_url($base));
	    $path = preg_replace('#/[^/]*$#', '', $path);
	    if ($rel[0] == '/') $path = '';
	    $abs = "$host$path/$rel";
	    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
	    for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
	    $scheme = "http";
	    return $scheme.'://'.$abs; 
	}
	public static function saveDestillerObjectToSession($destiller) {
		global $maxurlhistory;
		if (!isset($_SESSION['urlhistory'])) {$_SESSION['urlhistory'] = array('init'=>'init');}
		if(count($_SESSION['urlhistory']) >= $maxurlhistory) {
			$garbage=array_shift($_SESSION['urlhistory']);
		}
		if (!isset($_SESSION['keywords'])) {$_SESSION['keywords'] = 'init ';}
		else {$newkeywords=$destiller->getDomStatisticsValue('plaintext'); 
			$_SESSION['keywords']=$_SESSION['keywords'].$newkeywords;
		}
		
		$uri=$destiller->uri;
		$destiller =serialize($destiller); $destiller=substr($destiller, 0, 10);
		$thisurldata = array($uri=>$destiller);
		array_push($_SESSION['urlhistory'], $thisurldata);
		return true;
	}
	public static function quickAddTextToSession($text) {
		if (!isset($_SESSION['keywords'])) {$_SESSION['keywords'] = 'init ';}
		else { // to be fixed, same issues  as above! -> to arrays
			$_SESSION['keywords']=$_SESSION['keywords'].$text;
		}
		}
	public static function getSessionHistory() {
		return $_SESSION['urlhistory'];	
	}
	
	public static function makeSessionKeywords() {
		global $params, $defaultcharset;
		
		$tmp  =$_SESSION['keywords'];
		$sessioncloud = self::makeKeyWords($tmp, $params, $defaultcharset);
		return $sessioncloud;
		//print "<li><h1>HERE: ".$sessioncloud."</h1>";
		
	}
	public static function twitterThis($twittertext){
		global $twitteruname, $twitterpass;
		$object = new twitter($twitteruname, $twitterpass);
			//$object->post_tweet(printf("[%10.120s]\n",$twittertext));
			return substr($twittertext, 0, 130); //debug
	}
	/* save Destiller oblect to ranking database: links, keywords, special routines for images, headlines and 2-3word keyphrases */	
	public static function addDestillerObjectToRankingDb($destiller){
		global $params;
		$minlen = $params['min_word_length'] - 1;
		$db = new DDB;
		$url = $destiller->uri;	
		$db->toRankingDb('INIT',$url); // initial record for container URL to ensure closed cloud!!
		$linklist = $destiller->getRelatives(); // all keywords and links, special DOM from document
		while ($link=each($linklist)) { // not correct; value for  
			$db->toRankingDb($url, trim($link[1])); // all relatives written to DB
			$s='/\s/'; // space 
			// TODO: TRIMMING and minSTRLENGTH! ->helper DTS:: function?!
			if (preg_match($s,$link[1])){ //check keyphrases and headlines: additional break into single word  uri 
				$inlinelinks=explode(' ',trim($link[1])); // only true if link = language uri!!
				while ($inlinelink=each($inlinelinks)){
					if (strlen(trim($inlinelink[1])) > $minlen)  $db->toRankingDb(trim($link[1]),trim($inlinelink[1])); // pointer from headline to keyword
					//if (strlen(trim($inlinelink[1])) > $minlen) $db->toRankingDb(trim($inlinelink[1]),$url); // opt. should be..., pushes docs with keywords in headlines, same to images
				}
			}
		}
		unset($linklist);
		// now for the images //REMEMBER: this $imagelist array contains descriptions from a href links!!!!
		$imagelist = $destiller->getDomStatistics('imagelist');
		foreach ($imagelist as $image=>$value){
			foreach ($value as $src=>$dscr){ // each image in document:
				$db->toRankingDb($url, trim($src)); // link to image from document
				if (!preg_match('/^javascript:/', $dscr)){
					if (strlen(trim($dscr)) > $minlen){ //the image or link  description, as single keywords:
						$s='/\s/'; // space
						if (preg_match($s,$dscr)){
							$inlinelinks=explode(' ',$dscr);
							while ($inlinelink=each($inlinelinks)){ //implement minlength, see headlines too!!
								if (trim($inlinelink[1]) > $minlen) $db->toRankingDb(trim($src), trim($inlinelink[1])); //emphasis on keyword
								/* opt 3 */ //generally push URLs with description-- testing.
								//if (strlen(trim($ininelink[1])) > $minlen) $db->toRankingDb(trim($inlinelink[1]),$url);							
							}
						} 
					}
				}
			}
		}
		unset($imagelist); unset($db);
	}
	
	public static function makeCloudUriRanking(){
		global $ddbhost,$ddbuname,$ddbpass, $rankingscripttime;
		set_time_limit($rankingscripttime);
		$pr = new gRank;
		ini_set("memory_limit","64M");
		$pr->mysql = mysql_connect('localhost','destillerweb','luauucia') or die(mysql_error());
		mysql_select_db($_SESSION['userrankingdatabase']) or die(mysql_error($pr->mysql));
		$pr->calculate();
		return true;
	}
	
	public static function spiderFromUrlWithKeyWords ($strStartURL, $intCrawlDepth, $maxurl, 
		$strKeyword, $level02Keyword, $level03Keyword, $level04Keyword, 
			$level05Keyword, $spiderscripttime){
		set_time_limit($spiderscripttime);
		if (is_array($strKeyword)){ // set options to url, max,..., 0,0,0,0
			if (isset($tmp)){unset($tmp);}
			foreach($strKeyword as $i) {$i = '/'.$i.'/'; $tmp[] = $i;} // regex "conversion"
			$arrLinksRegex = array(1 => $tmp, 2 => $tmp, 3 => $tmp, 4 => $tmp, 5 =>$tmp); // for each iteration: 1=>kw,kw2 2=kw2, kw3 ..... ALLREGEXs
		} elseif ($strKeyword){ // manual, removed after esting..
			$strKeyword = "/$strKeyword/i"; // ading delimters and case insensitivity
			print "regex $strKeyword is keyword.<hr>"; // this is provisoric
			$arrLinksRegex = array(1 => array($strKeyword), 2 => array($level02Keyword), 
			 3 => array($level03Keyword), 4 => array($level04Keyword), 5 =>array($level05Keyword)); // for each iteration: 1=>kw,kw2 2=kw2, kw3 ..... ALLREGEXs
		} else return print "no regex given. ERROR.";
		$objKeyWordSpider = new spiderScraper;
		$objKeyWordSpider -> spiderStart($strStartURL);
		$objKeyWordSpider -> arrLinksRegex = $arrLinksRegex;
		$objKeyWordSpider -> intCrawlDepth = $intCrawlDepth;
		for ($i = 1; $i <= $maxurl; $i++) { //
			$timePrev = $objKeyWordSpider->timeLapsed;
			$arrFetchedPage = $objKeyWordSpider -> spiderNextPage();
		//	if(!$arrFetchedPage["error"]>0){
				echo "<hr>".$i.": Depth: ".$objKeyWordSpider->intCurrentDepth." -Seq: 
					".$objKeyWordSpider->intCurrentSequence." ".($objKeyWordSpider->timeLapsed - 
						$timePrev)."secs - ";
				echo " URL: ".$arrFetchedPage[0]."<br>";
				echo "<br>";
		//	} 
			/* end the loop.... */
			//if empty($arrFetchedPage[0]) 
		print "<h3>page crawled @ ".$objKeyWordSpider->timeLapsed." jobtime.</h3><hr><script language=javascript>window.scrollTo(0,9e9);</script>";
		}
		print "<hr><h1>".$objKeywordSpider->intPagesCrawled." pages crawled in ".$objKeyWordSpider->timeLapsed." jobtime</h1>";
	}  
	public static function crawlFromUrl($url){
		// move params to array 
		$crawler = new keywordCrawler();
		$crawler->setURL($url);
		$crawler->addReceiveContentType("/text\/html/ i");
		$crawler->addNonFollowMatch("/.(jpg|jpeg|gif|png|bmp|pdf|fla)$/ i");
		$crawler->addNonFollowMatch("/.(css|js)$/ i");
		$crawler->addNonFollowMatch("/^https:\/\// i");
		$crawler->setCookieHandling(true);
		$crawler->setAggressiveLinkExtraction(false);
		$crawler->setTrafficLimit(4096 * 1024);
		$crawler->setPageLimit(250);
		$crawler->setContentSizeLimit(250 * 1024);
		$crawler->setStreamTimeout(3);
		global $useragent;
		$crawler->setUserAgentString($useragent);
		/* see doc in bext function;
		 */
		$crawler->setFollowMode(0); 
		$crawler->go();	
		return $crawler->getReport();
		
	}
	
	
	public static function crawlFromUrlWithKeyWords($url, $arrKeywords, $logfile=0){
		foreach($arrKeywords as $keyword) {
			$strRegEx = '/'.trim($keyword).'/i'; 
			$arrRegEx[$strRegEx] = $keyword;
		} // regex "conversion", does not convert spaces yet!!!!
		$crawler = new keywordCrawler();
		$crawler->setKeywords($arrRegEx);		
		print_r($crawler->arrKeywords);
		//die;
		//print $url;
		//error_reporting(E_ALL);
		$crawler->setURL($url);
		$crawler->getLogHandle($logfile);
		$crawler->addReceiveContentType("/text\/html/ i");
		$crawler->addNonFollowMatch("/.(jpg|jpeg|gif|png|bmp|pdf|fla)$/ i");
		$crawler->addNonFollowMatch("/.(css|js)$/ i");
		$crawler->addNonFollowMatch("/^https:\/\// i");
		$crawler->setCookieHandling(true);
		$crawler->setAggressiveLinkExtraction(false);
		$crawler->setTrafficLimit(2047 * 1024);
		$crawler->setPageLimit(50);
		$crawler->setContentSizeLimit(250 * 1024);
		$crawler->setStreamTimeout(3);
		//$crawler->addLinkPriority("/.(strg|strg)$/ i", 10);
		global $useragent;
		$crawler->setUserAgentString($useragent);
		/* 
		bool setFollowMode (int mode)
			This method sets the general follow-mode of the crawler.
			The following table lists and explains the supported follow-modes.
			mode 	explanation
			0 	The crawler will follow EVERY link, 
			1 	The crawler will follow links that lead to the same host AND to hosts with the same domain like the one in the root-url.
			2 	The crawler will only follow links that lead to the same host like the one in the root-url.
			3 	The crawler only follows links to pages or files that are in or under the same path like the one of the root-url.
		 */
		$crawler->setFollowMode(1); 
		$crawler->go();	
		/*
		links_followed  	int  	The number of links/URLs the crawler found and followed.
		files_received 	int 	The number of pages/files the crawler received.
		bytes_received 	int 	The number of bytes the crawler received alltogether.
		process_runtime 	float 	The time the crawling-process was running in seconds.
		(since veriosn 0.7)
		data_throughput 	int 	The average data-throughput in bytes per second.
		(since veriosn 0.7)
		traffic_limit_reached 	bool 	Will be TRUE if the crawling-process stopped becaus the traffic-limit was reached.
		(See method setTrafficLimit())
		file_limit_reached 	bool 	Will be TRUE if the page/file-limit was reached.
		(See method setPageLimit())
		user_abort 	bool 	Will be TRUE if the crawling-process stopped because the overridable function handlePageData() returned a negative value.
		(since veriosn 0.7)
		*/
		/* 
		$report = $crawler->getReport();
		print_r($report);
		print_r($_SESSION['crawldata']);
		 */
		return $crawler->getReport();
		//die;
	}
	public static function getGooglePageRank($url) { //lookup google PR for page
		$gpr = new GooglePR();
		$gpr->userAgent = $_SERVER["HTTP_USER_AGENT"];
		$gpr->useCache = false; // all option to DESTILLER.INI.php !!!
		$gpr->debug = false;
		return $gpr->GetPR($url);
	}
	public static function getGooglePosition($url, $keyword, $maxposition) { // position in google search results for kw/url
		$position=new KeywordPosition($url, $keyword, $maxposition);
		return $position->GetPosition();
	}
	
	public static function makeReportGraphics($graphtype, $graphdata, $outputfilename) {
		 // Standard inclusions     
		 // $graphtype UNUSED should e array => (type, site, colorscheme, labels., background graphics...).....
		 global $destiller;//
		  //error_reporting(E_ALL);
		  require_once("includes/pChart/pData.class");  
		  require_once("includes/pChart/pChart.class");  
		  // Dataset definition   
		  $DataSet = new pData; //print_rprint_r($graphdata); 
		   $DataSet = new pData;  
		  $DataSet->AddPoint($graphdata['data'],$graphdata['label']);  
		  $DataSet->AddAllSeries();  
		  $DataSet->SetAbsciseLabelSerie();  
		  $DataSet->SetSerieName($graphdata['label'],$graphdata['label']);  
		  // Initialise the graph  
		  $font = '/srv/www/vhosts/content-analyzer.com/httpdocs/dev/DestillerV05/includes/fonts/tahoma.ttf';
		  $Test = new pChart(700,230);  
		  //$Test->loadColorPalette('/srv/www/vhosts/content-analyzer.com/httpdocs/dev/DestillerV05/includes/pChart/tones-3.txt',',');  
		  $Test->setFontProperties($font,8);  
		  $Test->setGraphArea(50,30,680,200);  
		  $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);  
		  $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);  
		  $Test->drawGraphArea(255,255,255,TRUE);  
		  //$Test->drawGraphAreaGradient(255,255,255,50,TARGET_BACKGROUND);  
		  $Test->drawFromPNG("/srv/www/vhosts/content-analyzer.com/httpdocs/images/logo.png",565,10,14);  
		  //$Test->drawFromPNG("/srv/www/vhosts/content-analyzer.com/httpdocs/images/rmmlogo.png",84,35); 
		
		  $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALL,150,150,150,TRUE,0,0,TRUE,1,TRUE);     
		  $Test->drawGrid(4,TRUE,230,230,230,22);  
		  // Draw the 0 line  
		  $Test->setFontProperties($font,6);  
		  $Test->drawTreshold(0,143,55,72,TRUE,TRUE);  
		   $Test->setShadowProperties(1,1,100,120,130,15,2);  
		  // Draw the bar graph  
		  $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,50);  
		  $Test->clearShadow();  
		  //$Test->writeValues($DataSet->GetData(),$DataSet->GetDataDescription(),$graphdata['label']);
		  // Finish the graph  
		  $crid = $graphdata['crid'];
		  $Test->setFontProperties($font,8);  
		  $Test->drawLegend(396,50,$DataSet->GetDataDescription(),255,255,255);  
		  $Test->drawTextBox(0,210,700,230,'copyright 2010',0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30); 
		  $Test->setFontProperties($font,10);  
		  $Test->drawTextBox(0,0,55,230,'HTTP://CONTENT-ANALYZER.COM ',270,0,0,0,ALIGN_TOP_LEFT,FALSE,0,0,0,7);  
		  $Test->setFontProperties($font,10);  
		  $Test->drawTitle(550,22,'SEMANTIC FOOTPRINT | '.$crid,50,50,50,275); 
		  $crid = $graphdata['crid'];
		  $reportid = $graphdata['reportid'];
		  //$Test->setFontProperties($font,24);  
		 $Test->drawTextBox(25,190,680,230,$reportid,0,255,255,255,ALIGN_LEFT,TRUE,0,0,0,7);  
		    
		   $Test->addBorder(0,200,200,100); //has to be last since size changes!!!
		  // output to
		  $Test->Render($outputfilename);
	}
	public static function checkIfUrlIsValid($url, $check=0) {
		$url = @parse_url($url);
		if ( ! $url) {return false;}
		$url = array_map('trim', $url);
		$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
		$path = (isset($url['path'])) ? $url['path'] : '';
		if ($path == ''){$path = '/';}
		$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';
		if ( isset ( $url['host'] ) AND $url['host'] != gethostbyname ( $url['host'] ) ) {
			if ($check == 1){
			 	$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path"); //PHP5 ONLY!!!
				$headers = ( is_array ( $headers ) ) ? implode ( "\n", $headers ) : $headers;
				return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers ); 
			}
		return true;
		}
		return false;
	}
	public static function nslookup($host) {
	//Forward DNS Lookup:
		$dnsinfo = dns_get_record($host);
		$return = array();
		foreach($dnsinfo as $dns) {
		    if($dns['type'] == 'A')
			 $return[] = $dns['ip'] . "\n";
		}
		return $return;
	}
//	Reverse DNS Lookup:
	public static function reversenslookup($ip){
		return  gethostbyaddr($ip);
	}
//	Mail Record Lookup:
	public static function mxlookup($domain){
		$dnsinfo = dns_get_record($domain);
		$return = array();
		foreach($dnsinfo as $dns) {
		    if($dns['type'] == 'MX')
			$return[] =  $dns['target'] . "\n";
		}
		return $return;
	}
	public static function cloudInfo(){
		$ddb = new DDB;
		$info['danglings'] = $ddb->countDanglingsInDb();
		$info['rankeduri'] = $ddb->countRankedDanglingsInDb();
		$info['uri'] = $ddb->countUriInDb();
		$info['keywords'] = $ddb->countKeywordsInDb();
		//$info['keyphrases min. 2'] = count($ddb->findKeyWordInDb(' '));
		//$info['keyphrases min. 3'] = count($ddb->findKeyWordInDb(' % '));
		$info['links'] = $ddb->countLinksInDb();
		$info['relativedensity'] = $info['danglings'] / $info['uri'];
		$info['keyworddensity'] = $info['uri'] / $info['keywords'];
		$info['linkdensity'] = $info['uri']  / $info['links'];
		return $info;
			
	}
}
?>