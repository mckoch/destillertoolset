<?php 
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
if (stristr(htmlentities($_SERVER['PHP_SELF']), "destillerSimpleSingleUrlReport.class.php")) {
    Header("index.php");
    die();
}
//main class 
class destillerSimpleSingleUrlReport {
	public $uri, $kri, $version;
	private $content, $contenttype, $encoding, $crawldata;
	protected $domstatistics, $textstatistics, $footprint, $keywords, $relatives;
	public function __construct() {return true;}
	public function constructDestiller($url){
		/* set execution time for Destiller */
		global $destillerscripttime;
		set_time_limit($destillerscripttime);  
		/* basic object (URL GET/POST response) */
		//$_SESSION['running'] = microtime();
		global $params, $defaultcharset, $fallbackcharset, $version; // for makeTextStatistics(object, params)
		$starttime = microtime(true);
		$this->version = $version;
		$this->uri = $url;
		$this->crawldata = '';
		$this->content = DTS::httpGetter($this->uri); //array; create and store results from a Snoopy object
		if (strlen($this->content['results']) > 64){
		try {	
			//  encoding  routines to be moved to DTS::toolset 	
			$this->makeEncodingfromHttpResponse($this->content['headers']);
			$tmpcharset=$this->encoding;
			if ($tmpcharset!=""){
								//print " re-encoding first run...";
				if (strtolower($tmpcharset)!=strtolower($defaultcharset)){
					// convert routines to be moved to DTS::toolset 
					$convert = new ConvertCharset ($tmpcharset, $defaultcharset); 
					$this->content['results'] = $convert->Convert($this->content['results']);
					unset($convert);
					//print "coverting from ".$tmpcharset."to ".$defaultcharset.".";
				}
			}
			//print " running domstats to find out....";
			$this->domstatistics = DTS::makeDomStatistics($this->content['results']); //array
			$this->makeEncodingFromHtml($this->domstatistics['meta']);
			if ($this->encoding=="") {
				//print  "no correct 'Content-Type:' header, fallback to $fallbackcharset";	
				$this->encoding=$fallbackcharset;	
			}
			if ($this->contenttype=="") {
				//print " unknown media type. assuming text/html."; //die;
				// noop. too risky. but to kep things going
				//$this->contenttype="text/html";	
				// catch it!! 
				throw new Exception('no correct content type, giving up.');
				return;
			}
			/*@notice: does NOT reconvert in case encoding in html is different from primary http response encoding AND default charset!!!! 
			means: will fail in some rare cases, resulting in (some) unreadable chars.
			*/ 
			if (strtolower($this->encoding) != strtolower($tmpcharset)){
								//print " 2nd run...".$this->encoding;	
				if (strtolower($this->encoding)!=strtolower($defaultcharset)){ // 
					$convert = new ConvertCharset ($this->encoding, $defaultcharset); 
					$this->content['results'] = $convert->Convert($this->content['results']);
					//print "converted from ".$this->encoding." to ".$defaultcharset.". running domstats again........";
					$this->domstatistics = DTS::makeDomStatistics($this->content['results']); //array
					//print "<hr><span class='ui-state-error'> domstatistics re-run. possible backwards conversion? </span><hr>";	
				}	
			}
			 $this->textstatistics = DTS::makeTextStatistics($this->domstatistics['plaintext'], $defaultcharset); //array
			 $this->textstatistics['guessed_language'] = DTS::makeGuessedLanguage($this->domstatistics['plaintext']); //array
			 //integrate results from DOMstatististics: add image descriptions, add link descriptons, add page title(!)
			 $this->keywords = DTS::makeKeyWords($this->domstatistics['plaintext'], $params, $defaultcharset); //array
			 $this->footprint = DTS::makeFootPrint($this->textstatistics['wordlist']); //string
			$this->relatives = DTS::makeRelatives($this->domstatistics['linklist'], $this->keywords, $this->domstatistics['headlines'], $this->domstatistics['imagelist']); //array
			$this->kri = DTS::makeRawKrI(count($this->domstatistics['linklist']), strlen($this->content['results'] ), strlen($this->domstatistics['plaintext'])); //$this->kri = DTS::makeRawKrI($nolinks, $documentlength, $textlength);
			 unset ($this->content['results']);
			 $endtime=microtime(true);
			 $this->content['objectduration'] = $endtime - $starttime;
		} catch (Exception $e) {
			//fwrite($handle, "#".$e->getMessage()."#ERROR\n");
		 	//print 'involving exception handler for: '.  $e->getMessage();
			simple_exception_handler($e);
		} 
		} else { //print "there seems to be a serious problem ith the response from this host. giving up.<hr>"; 
			throw new Exception('empty response from host'); return;
		} //die;
	}
	public function getFootprint(){return $this -> footprint;}	
	public function getKeywords(){return $this ->keywords;}	
	public function getRelatives() {return $this -> relatives;}	
	public function getTextStatistics(){return $this -> textstatistics;}
	public function getDomStatistics(){return $this -> domstatistics;}			
	public function getSessionData(){return $this->content;} //['headers'];}
	public function getTextStatisticsValue($val){return $this -> textstatistics[$val];}
	public function getDomStatisticsValue($val){return $this -> domstatistics[$val];}			
	public function getSessionDataValue($val){return $this->content[$val];}
	public function getCharSet(){return $this->encoding;}
	public function getDocumentType(){return $this->contenttype;}
	public function addCrawlData($crawldata){$this -> crawldata = $crawldata;}
	public function getCrawlData(){return $this -> crawldata;}
	
	/* move to DTS::toolset */
	private function makeEncodingFromHtml($encoding){
		foreach ($encoding as $i=>$val) {
			//print_r($i);
			if (strtolower(trim($i))=="http-equiv") {
				foreach ($val as $id=>$tags) {
					if (strtolower(trim($id))=="content-type:") {
						//print "Content-Type in HTML.";
						$tags=explode(';', $tags);
						if (strtolower(trim($tags[0]))=="text/html"){
							//print $tags[0];//print $tags[1];
							$this->contenttype = $tags[0];
							$tags=explode('=', $tags[1]);
							if (strtolower(trim($tags[0]))=="charset"){
								$this->encoding = strtolower(trim($tags[1]));
								//print "http-equiv charset found:".$this->encoding;
								break;
							}
						}
					}
				}
			}
		}
	}
	private function makeEncodingFromHttpResponse($encoding){
		foreach ($encoding as $i) {	
			$i=explode(':', $i);
			foreach($i as $key=>$val){
				if (strtolower(trim($val))=="content-type") {//print_r($val);
					//print "<hr>Content-Type in HTTP $i[1] raw.";
					$raw = explode(';', $i[1]);
					foreach ($raw as $param=>$val){
						if (strtolower(trim($val))=="text/html") $this->contenttype=$val;
						$parameter=explode('=',$val);
						foreach ($parameter as $p=>$v) {
							if (strtolower(trim($v))=="charset") {
								//print $parameter[1]."<hr>";
								$this->encoding=strtolower(trim($parameter[1]));
								break;	
							}
						}
					}
					
				}
			}
			unset($key);
		}
		unset($i);
	}
}
?>