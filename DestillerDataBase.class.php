<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
if (stristr(htmlentities($_SERVER['PHP_SELF']), "DestillerDataBase.class.php")) {
    Header("Location: index.php");
    die();
}
require_once('includes/ADODB/adodb.inc.php');
require_once('DESTILLER.INI.php');
/* Destiller Database Toolset  handle single URI mappings for/and pagerank module */
class DDB {
	private $conn;
	function __construct(){$this->conn=$this->_uriMapDbConn();}
	
	public function toRankingDb($id,$val) { // this function takes full URIs
		$id = $this->_writeUriToMapDb($id);//mapping first: URI pair to numerical IDs 
		$val = $this->_writeUriToMapDb($val);
		$this->_insertIntoRankingDb($id,$val); //insert into ranking db/table
		return TRUE;
	}
	
	public function getRankingTable($limit, $keywordsonly=0){
		$conn=$this->conn;
		if ($keywordsonly==0){
			$sql = "SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED  
			INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			AND PR_FINISHED.id!=2  AND uri.uri NOT LIKE 'javascript:%' 
			ORDER BY posicion ASC LIMIT ".$limit;
		}else {
			if ($keywordsonly=='1'){
			$sql="SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED
			  INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			  AND PR_FINISHED.id!=2 AND uri.uri NOT LIKE 'http://%' 
			  AND uri.uri NOT LIKE 'https://%' AND uri.uri NOT LIKE 'javascript:%' ORDER BY posicion ASC LIMIT ".$limit;
			}
			if ($keywordsonly=='2'){
			$sql="SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED  
			INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			AND PR_FINISHED.id!=2 AND ( uri.uri  LIKE 'http://%' OR uri.uri  LIKE 'https://%') 
			AND uri.uri NOT LIKE 'javascript:%' ORDER BY posicion ASC LIMIT ".$limit;					
			}
		}		
		return $conn->Execute($sql);
	}
	public function getRankingTableStatsArray($limit, $keywordsonly=0) {
	$conn=$this->conn;
			if ($keywordsonly==0){
			$sql = "SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED  
			INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			AND PR_FINISHED.id!=2  AND uri.uri NOT LIKE 'javascript:%' 
			ORDER BY posicion ASC LIMIT ".$limit;
		}else {
			if ($keywordsonly=='1'){
			$sql="SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED
			  INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			  AND PR_FINISHED.id!=2 AND uri.uri NOT LIKE 'http://%' 
			  AND uri.uri NOT LIKE 'https://%' AND uri.uri NOT LIKE 'javascript:%' ORDER BY posicion ASC LIMIT ".$limit;
			}
			if ($keywordsonly=='2'){
			$sql="SELECT PR_FINISHED.pagerank,  uri.uri FROM PR_FINISHED  
			INNER JOIN uri ON PR_FINISHED.id=uri.id WHERE PR_FINISHED.id!=1 
			AND PR_FINISHED.id!=2 AND ( uri.uri  LIKE 'http://%' OR uri.uri  LIKE 'https://%') 
			AND uri.uri NOT LIKE 'javascript:%' ORDER BY posicion ASC LIMIT ".$limit;					
			}
		}		
			return $conn->GetCol($sql);
	}
		
	
/* 	/// CAVE: RECORDSET!
	private function getRankingById($id) {
		$conn=$this->conn;
		$sql = "SELECT * FROM PR_FINISHED WHERE id= ".$id;
		return $conn->Execute($sql);
	}
	/// CAVE: RECORDSET! again....
	public function getRankingByUri($uri) {
		$id=$this->getUriIdFromMapDb($uri);
		print $id; die;
		return $this->getRankingById($id);	
	}
	 */
	 
	/* Mapping for single URI: generate ID for each single URI */
	private function _writeUriToMapDb($uri) {
		global $urimappings;
		$uri= mysql_real_escape_string(html_entity_decode(trim($uri)));
		$conn=$this->conn;
		$uriid=$this->getUriIdFromMapDb($uri);
		if ($uriid==FALSE){
			$sql="INSERT INTO $urimappings VALUES (0,'$uri')";
			$conn->Execute($sql);
			return $this->getUriIdFromMapDb($uri);
		} else {
			return $uriid;
		}
	}
	
	public function getUriIdFromMapDb($uri){
		global $urimappings;
		$uri= mysql_real_escape_string(html_entity_decode(trim($uri)));
		$conn = $this->conn;
		$sql="SELECT id FROM $urimappings WHERE uri LIKE '$uri' LIMIT 1";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {return $rs->fields[0];}
		else return FALSE;
	}
			
	public function getUriFromMapDb($id){
		global $conn, $urimappings;
		$sql="SELECT uri FROM $urimappings WHERE id == $id";
		$conn->Execute($sql);
		if (!$rs->EOF) {return $rs->fields[0];}
		return $uri;
	}
	
	/* read and write  URI/URI pairings to ranking DB */
	private function _insertIntoRankingDb($source,$target){
		global $rankingdb;
		$conn = $this->conn;
		$sql="INSERT INTO $rankingdb VALUES ($source,$target)";
		$conn->Execute($sql);	
	}

	/* general initiate DB connection */
	private function _uriMapDbConn() {
		global $ddbhost,$ddbuname,$ddbpass;
		$ddbname = $_SESSION['userrankingdatabase'];
		$conn = ADONewConnection('mysql');// # eg. 'mysql' or 'oci8' 
		$conn->debug = false;
		$conn->Connect('localhost', 'destillerweb' , 'luauucia', $ddbname);
		return $conn;	
	}
	public function initializeDataBase($database){ // CAVE: this clears ALL user tables without notice....
		global $urimappings, $rankingdb;
		$conn = $this->conn;
		$conn->debug = false;
		
				$tbl_ranking[] = "CREATE DATABASE IF NOT EXISTS `".$database."`";	
				$db = $_SESSION['userrankingdatabase'];		
				// make sure that tables exist	
				$tbl_ranking[] = "
					CREATE TABLE IF NOT EXISTS  pagerank (
					  `master` int(11) NOT NULL DEFAULT '0',
					  `slave` int(11) NOT NULL DEFAULT '0',
					  KEY m (`master`),
					  KEY s (`slave`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
					$tbl_ranking[] = "TRUNCATE TABLE  pagerank;";
				//$tbl_ranking[] = "	INSERT INTO  pagerank (master, slave) VALUES ('1', '2'), ('2', '1');";
				$tbl_ranking[] = "
					CREATE TABLE IF NOT EXISTS  uri (
					  id int(11) unsigned NOT NULL AUTO_INCREMENT,
					  uri varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
					  PRIMARY KEY (id)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
				//$tbl_ranking[] = "INSERT INTO  uri (id, uri) VALUES ('1', 'INIT'), ('2', 'EXIT');";
				$tbl_ranking[] = "
					CREATE TABLE IF NOT EXISTS `reports` (
				  `queryid` varchar(200) collate utf8_unicode_ci NOT NULL,
				  `url` text collate utf8_unicode_ci NOT NULL,
				  `kri` float NOT NULL default '0',
				  `destillerobject` longtext collate utf8_unicode_ci NOT NULL,
				  `keywords` tinytext collate utf8_unicode_ci NOT NULL,
				  `fulltext` longtext collate utf8_unicode_ci NOT NULL,
				  `footprint` tinytext collate utf8_unicode_ci NOT NULL,
				  `datetime` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`queryid`),
				  KEY `kri` (`kri`),
				  FULLTEXT KEY `keywords` (`keywords`),
				  FULLTEXT KEY `fulltext` (`fulltext`),
				  FULLTEXT KEY `url` (`url`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
				";
				$tbl_ranking[] = "TRUNCATE TABLE  reports;";
				// in case all exists nothing does any more ....
				$tbl_ranking[] = " TRUNCATE TABLE  pagerank";
				$tbl_ranking[] = "TRUNCATE TABLE  uri";
				// the following two key/values ensure closing of keyword cloud. IMPORTANT.
				$tbl_ranking[]="INSERT INTO  pagerank (master, slave) VALUES ('1', '2'), ('2', '1');";
				$tbl_ranking[]="INSERT INTO  uri (id, uri) VALUES ('1', 'INIT'), ('2', 'EXIT');";
				$tbl_ranking[] = "
				CREATE TABLE IF NOT EXISTS `batches` (
				  `id` int(11) NOT NULL auto_increment,
				  `url` varchar(255) collate utf8_unicode_ci NOT NULL,
				  `batch` varchar(255) collate utf8_unicode_ci default NULL,
				  `lastupdate` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
				  PRIMARY KEY  (`id`),
				  UNIQUE KEY `url_3` (`url`),
				  FULLTEXT KEY `url_2` (`url`)
				) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
				NE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
				"; 				
				foreach($tbl_ranking as $q) {
					$conn->Execute($q);
				}		
				return TRUE; 
			}
	
	public function findKeyWordInDb($uri, $limit = 100){
		$conn = $this->conn;
		$conn->debug = false;
		$uri=mysql_real_escape_string($uri);
		//$uri = explode(' ',$uri); //TODO: explode if keyphrase
		$sql = "SELECT PR_FINISHED.pagerank AS pagerank,  uri.uri FROM uri INNER JOIN PR_FINISHED ON PR_FINISHED.id = uri.id 
		WHERE uri LIKE '%$uri%' ORDER BY PR_FINISHED.pagerank DESC LIMIT ".$limit; //single words only!
		return $conn->Execute($sql);	
	}
	private function _writeSessionValues(){ // NOT WORKING SINCE ADO recordsets returned!!!!
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( DISTINCT SLAVE ) FROM pagerank WHERE MASTER =1";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$_SESSION['numberofiniturls']=$rs->fields[0];}
		//$tmp->fields; // oh dear.....
		$sql="SELECT COUNT( DISTINCT uri ) FROM uri";
		$_SESSION['numberofuris']=$conn->Execute($sql); // ...and again
	}
	
	public function listBatch($limit=100000) {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT `url` FROM `batches` WHERE 1 ORDER BY `id` LIMIT ".$limit;
		$rs=$conn->GetArray($sql);
		return $rs;
	}
		public function getNextBatchElement() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT `url` FROM `batches` WHERE 1 ORDER BY `id` LIMIT 1";
		$rs=$conn->GetArray($sql);
		return $rs;
	}


	public function addToBatch($url) {
		error_reporting(E_ALL);
		$conn = $this->conn;
		$conn->debug = false; 
		$sql = "INSERT INTO `batches` ( `id` , `url` , `batch` , `lastupdate` ) VALUES ( NULL , '".$url."', NULL , NULL)";
		//print $sql; //die;
		if ($conn->Execute($sql)) {return true;} else {return false;}
	}
	public function findInBatch($url) {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT url  FROM batches WHERE url like '%".$url."%'";
		$rs=$conn->GetArray($sql);
		return $rs;	
	}
	public function removeFromBatch($url) {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="DELETE  FROM batches WHERE url like '".$url."'";
		if ($conn->Execute($sql)) {return true;} else {return false;}	
	}
	public function clearBatch() {
		$conn = $this->conn;
		$conn->debug = true;
		$sql="truncate table batches";
		if ($conn->Execute($sql)) {return true;} else {return false;}
	}
	public function importBatchfromCloud() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql =" INSERT IGNORE INTO batches(url) SELECT `uri` as  `url` FROM uri WHERE `uri` LIKE 'http://%' OR `uri` LIKE 'https://%'  ";
		if ($conn->Execute($sql)) {return true;} else {return false;}
	}
	public function importBatchFromReportList() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql =" INSERT IGNORE INTO batches(url) SELECT DSITINCT `url` as  `url` FROM reports WHERE `url` LIKE 'http%' OR `url` LIKE 'https://%' ";
		if ($conn->Execute($sql)) {return true;} else {return false;}
	}
	public function editBatch() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql=" ";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
	}
	public function countBatch() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( url ) FROM batches";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
	}
	
	public function countReportsInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( kri ) FROM reports WHERE 1";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
		}

	public function countLinksInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( uri ) FROM uri WHERE uri  LIKE 'http://%' 
			  OR  uri.uri LIKE 'https://%' AND uri.uri NOT LIKE 'javascript:%' ";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
		}
	public function countKeywordsInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( uri ) FROM uri WHERE uri NOT LIKE 'http://%' 
			  AND uri.uri NOT LIKE 'https://%' AND uri.uri NOT LIKE 'javascript:%' ";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
		}
	
	public function countUriInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( uri ) FROM uri";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
		}
	public function countRankedDanglingsInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( pagerank ) FROM PR_FINISHED";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		else return false;
		}	
	public function countDanglingsInDb() {
		$conn = $this->conn;
		$conn->debug = false;
		$sql="SELECT COUNT( SLAVE ) FROM pagerank";
		$rs=$conn->Execute($sql);
		if (!$rs->EOF) {$returnvalue = $rs->fields[0]; return $returnvalue;}		
		}
	
	function __destruct(){$conn = $this->conn; //$this->_writeSessionValues();
		$conn->Close();
	}
}
?>