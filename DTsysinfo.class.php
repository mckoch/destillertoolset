<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
// sysinfo plugin class for use with AJAX
// author: mckoch at mckoch de
// open use, by ref CC apply
// this class provides a wrapper for common U*IX cmds.


class DTsysinfo {
	
	private $avgload;
	private  $free;
	private $pid;
	//private 	
	 
	function _construct(){
		$this->pid = getmypid();
	}
	
	/* 
	function getAvgload
	function getFree
	function setPid
	function getMpu
	function readFromLog($logfile, $nolines)
	 */
	 

	function lockIsActive($lockfile) {
		//$lockfile = 'lockfiles/'.$_SESSION['userrankingdatabase'].'.LOCK';
		$lockfilehandle = fopen($lockfile, "r+");
		if (flock($lockfilehandle, LOCK_EX | LOCK_NB)) {fclose($lockfilehandle); return false;} else {return file_get_contents($lockfile);}
	}
	function countLines($filepath) 
	 {
	    /*** open the file for reading ***/
	    $handle = fopen( $filepath, "r" );
	    /*** set a counter ***/
	    $count = 0;
	    /*** loop over the file ***/
	    while( fgets($handle) ) 
	    {
	        /*** increment the counter ***/
	        $count++;
	    }
	    /*** close the file ***/
	    fclose($handle);
	    /*** show the total ***/
	    return $count;
	 }
}

?>