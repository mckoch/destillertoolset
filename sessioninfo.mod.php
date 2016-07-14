<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/ 
if (stristr(htmlentities($_SERVER['PHP_SELF']), "seesioninfo.php")) {
	    Header("index.php");
	    die();
}
$_SESSION['fullscriptend'] = microtime(true); $jobtime=($_SESSION['fullscriptend']-$_SESSION['fullscriptstart']); 
$_SESSION['sessiontime']=($_SESSION['sessiontime']+$_SESSION['destillertime']+$_SESSION['rankingtime']); 
print $jobtime; ?> sec.<br>destiller time : <?php print $_SESSION['destillertime']; ?><br>ranking time : <?php print $_SESSION['rankingtime']; ?>
<br>used time: 
<?php print $_SESSION['sessiontime']+$_SESSION['rankingtime']; ?> sec.
<br>remaining time:
<?php $usertime= $usertime - $_SESSION['sessiontime']; print $usertime ?> sec.
<?php if ($usertime < 0) print "<br><strong>user time exceeded. please purchase more destiller time to continue.</strong>"; ?>
<?php print "<br>".(memory_get_peak_usage(true)/1024/1024)." megabytes used. "; print "; ". $version." running on ".php_uname().". ";
	if (!$_SESSION['running']) {print "job terminated successfully.";} else {print $_SESSION['running']." seems to be locked up....";}
?>