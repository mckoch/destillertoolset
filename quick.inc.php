<style type="text/css"/>#col1_content img {width: 33px !important; height: 33px !important; float: left !important; position: relative !important;}</style>
<div id="main">
        <div id="col1">
          <div id="col1_content" class="clearfix">
				<script language='javascript'>
				function verifyMe(){
				var msg='';
								
				if(document.getElementById('host').value==''){
					msg+='- \n\n';}
				
				if(msg!=''){
					alert('The following fields are empty or invalid:\n\n'+msg);
					return false
				}else{
					return true }
				
				}
				</script>
				<form name='Quick Destiller' action='/dev/DestillerV05/' method='GET' enctype='application/x-www-form-urlencoded' onsubmit='return verifyMe();'>
				<input type='hidden' name='action' id='action' value='quick'>
				<input type='hidden' name='destillersession' id='destillersession' value='"<?php $_SESSION['userrankingdatabase'] ?>"'>
				<table class='table_form_1' id='table_form_1' cellspacing='0'>
					<tr>
						<td class='ftbl_row_1' ><b style='color:red'>*</b>WHOIS
						</td>
						<td class='ftbl_row_1a' >
							<LABEL ACCESSKEY='2'><input type='checkbox' name='whoisinformation' id='whoisinformation[0]' value='true'>true</LABEL>&nbsp;
						</td>
					</tr>
					<tr>
						<td class='ftbl_row_2' ><LABEL for='host' ACCESSKEY='0' ><b style='color:red'>*</b>URL
						</td>
						<td class='ftbl_row_2a' ><input type='text' name='host' id='host' size='45' maxlength='100'  value=''>
						</td>
					</tr>
					<tr>
						<td colspan='2' align='right'><input type='submit' name='submit' value='Submit'>&nbsp;<input type='reset' name='reset' value='Reset'>
						</td>
					</tr>
				</table>
				</form>
<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/ 
if (stristr(htmlentities($_SERVER['PHP_SELF']), "quick.inc.php")) {
	    Header("index.php");
	    die();
}
/* not for production */
//error_reporting(E_ERROR);

/* Requirements */
/* require_once('includes/simplehtmldom/simple_html_dom.php');
require_once('includes/Snoopy-1.2.4/Snoopy.class.php');
require_once('includes/autokeywords/class.autokeyword.php');
require_once('includes/libtextcat/saddorlibtextcat.php');
require_once("includes/domain.class.php"); */

/*  Helper  to get DomainName from URL*/
function stripDomainFromUrl($url) {$url=parse_url($url,PHP_URL_HOST);$url=explode('.', $url);
	$url=array_reverse($url); $url=$url[1].'.'.$url[0]; return $url;
}

/* URL to examin */
$url = $_GET['host'];
if ($_GET['whoisinformation']){
	$domaininfo = new domain(stripDomainFromUrl($url));
	$domaininfo = $domaininfo->info();
} 

/* HTTP GET function */
$snoopy = new Snoopy;
$snoopy->fetch($url);
$thisurl = $snoopy->results;
$lastredirect = $snoopy->lastredirectaddr;
print $lastredirect;
/* creating DOM object */	
$html = str_get_html($thisurl);

/* dummy for bgHTTP */
echo "<hr>bgHTTP support: NO. showing HTTP header information:<br>";

/* TODO: convert $html to lowercase! */

/* HTTP Response header */
while(list($key,$val) = each($snoopy->headers)) echo $key.": ".$val."<br>\n";

echo "<hr>META http-equiv: ";
/* http-equiv */
foreach($html->find('head meta[http-equiv]') as $el){//echo htmlentities($el->outertext) . ': '; 
	echo $el->content . '<br>';
}
/*+ content type */
// KAPPES preg_match('/http-equiv=Content-Type/', $html->find('[http-equiv=Content-Type]', 0));
// CASE SENSITIVE!!!:-((
//if ($html->find('head meta[http-equiv=Content-Type]', 0)) echo "<hr>".($html->find('[http-equiv=Content-Type]', 0)->content);

/* Meta-Tags */
echo "<hr>META name: ";
foreach($html->find('head meta[name]') as $el){echo $el->name . ': '; echo $el->content . '<br>';}

echo "<hr>Images: ";

foreach($html->find('img') as $element)
	{	if ($lastredirect) {$url = $lastredirect;} 
		$tmp = $element->src;
       echo  "<img src=\"".DTS::makeAbsoluteUrl($tmp, $url)."\">";
	}

/* language from header */ 
//echo $html->find('[lang]', 0)->content;

/* convert HTML to plain text */
//unset $snoopy;
$phtml = $html -> plaintext;

/* check / estimate language from plain text */
$libtext = new SaddorLibTextCat();
$libtext->WhatLang($phtml);
echo "<hr>Estimated language(s): <nopre>";
print_r ($libtext->ranking);
echo "</nopre>";

/* filter Stopwords */
// require_once('stopWords');
echo "<hr>NO STOP FILTER. libs: [de,en].";
// require spamfunction from isitspam.class
echo "<hr>NO SPAM FILTER. libs: [auto,untrained].";


/* suggested keywords, params für class autokeyword */
$params['content'] = $phtml; //page content
$params['min_word_length'] = 6;  //minimum length of single words
$params['min_word_occur'] = 3;  //minimum occur of single words
$params['min_2words_length'] = 5;  //minimum length of words for 2 word phrases
$params['min_2words_phrase_length'] = 12; //minimum length of 2 word phrases
$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase
$params['min_3words_length'] = 4;  //minimum length of words for 3 word phrases
$params['min_3words_phrase_length'] = 15; //minimum length of 3 word phrases
$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase


/* instantiate the KeyWords Object */
$keyword = new autokeyword($params, $defaultcharset);
/*
echo "<H1>Output - keywords</H1>";echo "<H2>words</H2>";echo $keyword->parse_words();echo "<H2>2 words phrase</H2>";echo $keyword->parse_2words();echo "<H2>3 words phrase</H2>";echo $keyword->parse_3words();echo "<H2>All together</H2>";
*/
$suggestedKeyWords = $keyword->get_keywords();
echo "<hr><span class='genkeywords'>Estimated keywords and -phrases: ".$suggestedKeyWords."</span>"; 

// Printing out whois data 
print "<hr>WHOIS information (<em>this application does not save or cache any WHOIS-Information</em>): ".$domaininfo;

//adding to session cloud: text or keywords?
DTS::quickAddTextToSession($suggestedKeyWords);// keywords only. mixes session cloud with plaintext from SimpleSingeleUrlReport
//DTS::quickAddTextToSession($phtml); //full text

//double destilled for twitter...
$params['content'] = $suggestedKeyWords; 
$metatwitterkeywords=new autokeyword($params, $defaultcharset);
$metakeywords=$metatwitterkeywords->get_keywords();
$twittertext = $url.": ".$suggestedKeyWords;
if (strlen($twittertext)> 50){DTS::twitterThis($twittertext);}
?>
</div></div>

<div id="col2">
          <div id="col2_content" class="clearfix">
          <script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 12,
  interval: 6000,
  width: 'auto',
  height: 300,
  theme: {
    shell: {
      background: 'silver',
      color: '#ffffff'
    },
    tweets: {
      background: 'silver',
      color: '#ffffff',
      links: 'white'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    hashtags: false,
    timestamp: true,
    avatars: false,
    behavior: 'all'
  }
}).render().setUser('contentanalyzer').start();
</script>

          
          </div>
        </div>
<div id="col3">
          <div id="col3_content" class="clearfix">
          <hr>current cloud<no_pre>
          <?php
			//print_r((DTS::getSessionHistory()));
			//$a=array_reverse(DTS::makeSessionKeywords());
			$a=DTS::makeSessionKeywords();
			while (list ($key, $val) = each ($a)) {
			echo "[$key] $val "; }
			//print_r(array_rand(DTS::makeSessionKeywords(), 10));
		  ?>
		  </no_pre>
          <hr>Top 10 keywords/keyphrases from current cloud:<ul>
          		<li class="notice">ERROR. NOT LOGGED IN.</li>
          	</ul>
          </div>
          <!-- IE Column Clearing -->
          <div id="ie_clearing"> &#160; </div>
        </div>
</div>