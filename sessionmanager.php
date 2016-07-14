<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
	if (stristr(htmlentities($_SERVER['PHP_SELF']), "footer.php")) {
		    Header("index.php");
		    die();
	}
/* This part taken from PHPnuke... security: basically check for GET and POST vars.
*/
	//prevent UNION SQL Injections
	unset($matches);
	unset($loc);
	if(isset($_SERVER['QUERY_STRING'])) {
		if (preg_match("/([OdWo5NIbpuU4V2iJT0n]{5}) /", rawurldecode($loc=$_SERVER['QUERY_STRING']), $matches)) {
	    	die('Illegal Operation');
	  	}
	}
	// This block of code makes sure $admin and $user are COOKIES
	if((isset($admin) && $admin != $_COOKIE['admin']) OR (isset($user) && $user != $_COOKIE['user'])) {
		die("Illegal Operation");
	}
	function stripos_clone($haystack, $needle, $offset=0) {
		$return = stripos($haystack, $needle, $offset=0);
		if ($return === false) {
			return false;
		} else {
			return true;
		}
	}
	// Additional security (Union, CLike, XSS)
	if(isset($_SERVER['QUERY_STRING']) && (!stripos_clone($_SERVER['QUERY_STRING'], "ad_click"))) {
		$queryString = $_SERVER['QUERY_STRING'];
	    if (stripos_clone($queryString,'%20union%20') OR stripos_clone($queryString,'/*') OR stripos_clone($queryString,'*/union/*') OR stripos_clone($queryString,'c2nyaxb0') OR stripos_clone($queryString,'+union+') OR (stripos_clone($queryString,'cmd=') AND !stripos_clone($queryString,'&cmd')) OR (stripos_clone($queryString,'exec') AND !stripos_clone($queryString,'execu')) OR stripos_clone($queryString,'concat')) {
	    	die('Illegal Operation');
	    }
	}
	$postString = "";
	foreach ($_POST as $postkey => $postvalue) {
	    if ($postString > "") {
	     $postString .= "&".$postkey."=".$postvalue;
	    } else {
	     $postString .= $postkey."=".$postvalue;
	    }
	}
	str_replace("%09", "%20", $postString);
	$postString_64 = base64_decode($postString);
	
	if (stripos_clone($postString,'%20union%20') OR stripos_clone($postString,'*/union/*') OR stripos_clone($postString,' union ') OR stripos_clone($postString_64,'%20union%20') OR stripos_clone($postString_64,'*/union/*') OR stripos_clone($postString_64,' union ') OR stripos_clone($postString_64,'+union+')) {
		header("Location: index.php");
		die();
	}
	
/* now for the true job - sesion handling 
 		session management in seperate Database; before config loads!*/
	$mySQLHost = "localhost";    
	$mySQLUsername = "destillerweb";    
	$mySQLPassword = "luauucia";    
	$mySQLDatabase = "destillermain";
	$link = mysql_connect($mySQLHost, $mySQLUsername, $mySQLPassword);    
	if (!$link) {die ("Could not connect to database!");    }
	/*
		initialize user destiller session db
	*/
	$db = mysql_select_db($mySQLDatabase, $link);    
	if (!$db) {die ("Could not select database!");}
	
	ini_set('session.gc_maxlifetime', 14400); 
	ini_set('session.gc_probability', 1); 
	ini_set('session.gc_divisor', 1); //garbage collector for sessions
	require_once "includes/sessions/class.dbsession.php";// instantiate a new session object     // note that you don't need to call the session_start() function    // as it is called automatically when the object is instantiated
	$session = new dbsession();    // from now on, use sessions as you would normally    // the only difference is that session data is no longer saved on the server    // but in your database, making the data in it more secure
	print session_id();
	if (!isset($_SESSION['sessiontime'])) {$_SESSION['sessiontime'] = 0;}
	$_SESSION['fullscriptstart'] = microtime(true); 
	//$_SESSION['userrankingdatabase'] = preg_replace('/\=/', '999', base64_encode(session_id()));  // Remeber: this has to be done in sessions db class too, see garbge collection!
	$_SESSION['userrankingdatabase'] = session_id();
	/* 
		init the UserDB for Destiller data //TODO: full init for user db. array sql[] each.... with tables
	*/ 
	mysql_query("CREATE DATABASE IF NOT EXISTS `".$_SESSION['userrankingdatabase'])."`"; // each session has its own ranking db! 
	print mysql_error($link);
?>