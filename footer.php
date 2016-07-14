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
?>
      <!-- begin: #footer -->
      <div id="footer">
        <div class="subcolumns">
          <div class="c25l">Cloudstats: 
          <span class="notice">There are 0 URLs and 0 keywords in user database.</span>
          <span class="notice">There are 0 URLs and 0 keywords in system database.
		  <?php print " Current user count: ".$session->get_users_online().". ";  ?>
		  </span>
          </div>
          <div class="c50l"><?php print ANONYMOUS_INFO_SHORT_MESSAGE; ?></span>
          </div>
          <div class="c25r">
            <span class="footmsg">
				<img src="http://content-analyzer.de/themes/Traditional/images/logo.png" align="left">Ein Projekt der <a href="http://raderthalmedien.de/news/mitteilungen/dienstleister-fuer-die-branche.html">RMM Raderthal MedienManufaktur GmbH, K&ouml;ln</a>.<br>
				<a href="http://raderthalmedien.de/impressum.html"><img src="http://raderthalmedien.de/images/logo.jpg" border="0" width="120"></a>
				<br>page generation: <?php require_once('sessioninfo.mod.php'); ?>
				?>

			</span>
          </div>
        </div>
      </div>
    </div>
    <div id="border-bottom">
      <div id="edge-bl"></div>
      <div id="edge-br"></div>
    </div>
  </div><?php print ini_get("session.gc_maxlifetime"); ?>
</body>
</html>
