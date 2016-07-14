<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
error_reporting('E_ALL');
require_once('includes/stable/stable.php');
require_once('includes/stat1/Stat1.php');
require_once('includes/percentile/Stat.class.php');
require_once('includes/bayesianspamfilter/ngram.php');
require_once('includes/bayesianspamfilter/trainer.php');
require_once('includes/bayesianspamfilter/spam.php');
require_once('includes/bayesianspamfilter/config.php'); // keep stuck to seperate DB or use DTDatabase::
require_once('includes/combinatorics/Combinatorics.php');
class DTStats {
 	function __construct() {
		return true;
	}
 	
	public static function basicAveragesInPercent ($data,$options = 0){//the percentile class require ./percentile/Stat.class.php
		$stat = new Stat();
		//$data = array(12,34,56);
		$return["25th percentile"] = $stat->percentile($data,25);
		$return["median (50th percentile)"] = $stat->median($data);
		$return["95th percentile"] = $stat->percentile($data,95);
		$return["quartile(25th, 50th, 75th percentile)"] = implode(",	", $stat->quartiles($data));
		return $return;	
	}	
	
	public static function basicMedianTable ($data, $options = 0){// require ./stable/stable.php - the
		if ( !isset( $table) ) {$table = new StatisticTable();}
		$table->clear();
		foreach ( $data as $item ) {$table->add( $item);}
		$all = count( $table->getData() ); 
		foreach ( $table->getSortedData() as $value => $count ){
			$return[] = array ($value, $count, round( ( $count / $all ) * 100, 4 ));
		}
		return $return;
	}
	
	public static function fullMedianTable ($data, $options, $title){//require ./stat1/stat1.php
		 //while (list($key,$val) = each($data)) {$data0[] = $key; $data1[] = $val;}
		$h = new Stat1;
		$h->create($data, $data ,$title);//
		if ($options == "array") return $h->getStats();
		if ($options == "table") return $h->printStats(); // which will format outpot in a formatted table and return this in a text $var
		return false;
	} 
	
	public static function bayesianMatch ($data, $switch){//require ./bayesianspamfilter/ngram.php
		switch ($switch) {
			case 'eval':
				//require ./bayesianspamfilter/spam.php
				 function handler($ngrams,$type) { // required by new spam("handler"); | @param Array $ngrams N-grams | @param String $type Type of set to compare 
				    global $db;				    
				    $info = array_keys($ngrams);
				    $sql = "select ngram,percent from knowledge_base where belongs = '$type' && ngram in ('".implode("','",$info)."')";
				    $r = mysql_query($sql,$db);
				    while ( $row = mysql_fetch_array($r) ) {$t[ $row['ngram'] ]  = $row['percent'];}
				    return $t;
				}				
				$db = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
				mysql_select_db(MYSQL_DB,$db);
				$spam = new spam("handler"); 
				$texts = $data;// array("Phentermine", "Buy cheap xxx","Really nice post","Viagra","This a large text, it is not spam, but because the training set are small sentenses, it may be marked as spam. You can solve this problem with a largest sentences on the training set.");
				$return = "<h1>Spam test</h1>";
				foreach ($texts as $text)
				    $return .= "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'spam')."%</b> spam<hr>";
				$return .= "<h1>Ham test</h1>";
				foreach ($texts as $text)
				    $return .= "<em><strong>$text</strong></em> has an accuraccy of <b>". $spam->isItSpam_v2($text,'1')."%</b> ham<hr>";	
				return $return;
			break;
			case 'learn':
				//require ./bayesianspamfilter/trainer.php
				set_time_limit(0);
				ini_set('memory_limit','256M');
				$trainer = new trainer;
				$db = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
				mysql_select_db(MYSQL_DB,$db);
				/* loading previus learn */	//echo "<h1>Loading previous learn</h1>";flush();
				$query = mysql_query("select belongs,ngram,repite from knowledge_base",$db);
				$previouslearn = array();
				while ( $row = mysql_fetch_array($query) )$previouslearn[$row['belongs']][$row['ngram']] = $row['repite'];
				mysql_free_result($query);
				$trainer->setPreviousLearn($previouslearn);
				/* traine */ //echo "<h1>Training</h1>";flush();
				$query = mysql_query("select * from examples",$db);
				$sql=mysql_query("select comment_content as text,comment_approved as state from wp_comments",$db);
				//echo "<h2>Loading examples</h2>";flush();
				while ( $row = mysql_fetch_array($query) ){
				    $text = $row['text']; $text = strip_tags($text);
				    $trainer->add_example($text,$row['state']);
				}
				mysql_free_result($query);
				/* learn */	//echo "<h2>Learning</h2>";flush();
				$trainer->extractPatterns();
				/* save what is learned */ //echo "<h1>Saving learning</h1>";flush();
				foreach ($trainer->knowledge as $tipo => $v) {
				    foreach($v as $k => $y) {
				        $k = addslashes($k);
				        $sql = "replace knowledge_base values('$k','$tipo','".$y['cant']."','".$y['bayesian']."')";
				        mysql_query($sql,$db) or die(mysql_error($db).":".$sql);
				    }
				}
				//echo "<h1>Optimizing database</h1>";flush();	
				mysql_query("create temporary table opttable as 
				select ngram, count(*) total, min(percent) as nmin, max(percent) as nmax
				from knowledge_base group by ngram having count(ngram) > 1",$db);
				//mysql_query("delete from knowledge_base where ngram in (select ngram from opttable where (nmax-nmin) < 0.30)",$db); 				
				return true;
			break;	
		}
		return false;
	}
	
	public static function combinatorics ($data, $method, $take) {//require ./combinatorics/Combinatorics.php		
		$mycalc=new Combinatorics;
		/* using the class methods */
		switch ($method) {
			// possible Permutations of objects
			case 'numPerm': return $mycalc->numPerm($take);
			// returnlist if Permutations of objects
			case 'makePermutations': return $perms=$mycalc->makePermutations($data);
			// Number combinations of objects without repetition
			case 'numComb': return $ncombwr=$mycalc->numComb(count($data),$take);
			// return Combinations without repetition
			case 'makeCombination': return $combwr=$mycalc->makeCombination($data, $take);
			//Number of dispositions of objects
			case 'numDisp': return $ndisp=$mycalc->numDisp(count($data),$take );
			// return dispositions
			case 'makeDisposition': return $disp=$mycalc->makeDisposition($data, $take);
			// number of dipositions without repetition
			case 'numDispWoR': return $ndispwr=$mycalc->numDispWoR(count($data),$take );
			// return disposotions without repetions
			case 'makeDispositionWoR': return $dispwr=$mycalc->makeDispositionWoR($data, $take);
		}
		return false;	
	}
}
?>