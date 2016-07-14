<?php 
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
	if (stristr(htmlentities($_SERVER['PHP_SELF']), "header.php")) {
	    Header("index.php");
	    die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?php print $version." on ".$url; ?></title>
	<link href="css/my_layout.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 7]>
	<link href="css/patches/patch_my_layout.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	  <script type="text/javascript" src="oldlib/prototype.js" type="text/javascript"></script>
  <script type="text/javascript"  src="oldlib/scriptaculous.js" type="text/javascript"></script>
</head>
<body>
  <div class="page_margins">
    <div id="border-top">
      <div id="edge-tl"></div>
      <div id="edge-tr"></div>
    </div>
    <div class="page">
    
      <div id="header">
        <div id="topnav">
			<img src="http://content-analyzer.de/themes/Traditional/images/logo.png" align="left"><a href="#">Login</a> | <a href="#">Kontakt </a> | <a href="#">Impressum</a><br><a href="http://raderthalmedien.de/impressum.html"><img src="http://raderthalmedien.de/images/logo.jpg" border="0" width="120"></a>
        </div>
				
	  </div>
		<?php
		function activeLink($target, $text) {
			if (!$_GET['action']){$_GET['action']='start';}
			$activelinkstart = "<li class='active'><strong>";
			$activelinkend = "</strong></li>";        
			$passivelinkstart ="<li><a href='?action=";
			$passivelinkclose1 = "'>";
			$passivelinkclose2 = "</a>";
			if ($target == $_GET['action']) {return $activelinkstart.$text.$activelinkend;}
			else {return $passivelinkstart.$target.$passivelinkclose1.$text.$passivelinkclose2;}
		}
		?>
      <div id="nav">
        <!-- skiplink anchor: navigation -->
        <a id="navigation" name="navigation"></a>
        <div class="hlist">
          <!-- main navigation: horizontal list -->
          <ul>
          <?php
		  	print activeLink('start', 'Start/Reset');
			print activeLink('http', 'Destiller Single URL Report');
			print activeLink('cloud', 'Cloud Destiller');
			print activeLink('quick', 'Quick Destiller');
			print activeLink('about', 'About'); 
			print activeLink('config', 'Config');
          ?>
          </ul>
        </div>
      </div>
