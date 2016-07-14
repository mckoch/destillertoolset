<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
if (stristr(htmlentities($_SERVER['PHP_SELF']), "destillerSimpleSingleUrlReportToDb.class.php")) {
    Header("index.php");
    die();
}
/* NOT IN USE - use DDB::statics() for breaking text into URIs. 
	This class only stores and retrives full Destiller objects!!! */


class destillerSimpleSingleUrlReportToDb extends destillerSimpleSingleUrlReport {
	public function __construct() {
		$db = NewADOConnection('mysql');
		$db->Connect($dbhost, $dbuname, $dbpass, $dbname);
	}	
	public function saveDestillerObject() { // save serialized values to database........
		$serialized = serialize(parent::$this); //serialize parent::
		$sql="INSERT INTO destillerSimpleSingleUrlReport (url, abstract) VALUES ($url, $serialized)";
		$res=$db->Execute($sql);
		if ($res != false) return true else return false;
	}
	public function loadDestillerObject($url) {
		$sql="SELECT * FROM destillerSimpleSingleUrlReport WHERE url LIKE $url";
		$res=$db->Execute($sql);
		if ($res != false) { 
			parent::$this = unserialize($res); //unserialize to parent	
		} else return false;
	}
	public function newSave($url) { // shorthand for batch creation, update etc.
		$this->deleteDestillerObject($url);
		parent::$this->constructDestiller($url);
		if ($this->saveDestillerObject($url)) return true else return false;
		}
	public function deleteDestillerObject($url) {
		$sql = "DELETE FROM destillerSimpleSingleUrlReport WHERE url LIKE $url";
		$res=$db->Execute($sql);
		if ($res != false) return true else return false;
	}
	public function saveDestillerValues(array $saveconfiguration) {
		// store persistant abstract in table/fields for cloud generation
		// read fields to save and construct dat record for URL. 
		//see array global $saveconfiguration
	}
	function __destruct() {	
		$db->Close;
	}
}	
/* 	
 while (!$result->EOF) {
	for ($i=0, $max=$result->FieldCount(); $i < $max; $i++)
		   print $result->fields[$i].' ';
	$result->MoveNext();
	print "<br>\n";
 }  */


?>