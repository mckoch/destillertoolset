<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
if (stristr(htmlentities($_SERVER['PHP_SELF']), "destillerSimpleUrlReportTemplate.php")) {
    Header("index.php");
    die();
}
?>
      
      <div id="teaser"><style type="text/css">#teaser .footprint img{width: 33px !important; height: 33px !important; float: left !important; position: relative !important;}</style><hr>
	  <span class="footprint">Footprint: 
      <?php
      //print_r($_SESSION); print_r($destiller);
      	print $destiller->getFootprint()."<hr>Images: ";
      	//print_r(array_values($destiller->getDomStatisticsValue('imagelist')));
      	foreach (array_unique($destiller->getDomStatisticsValue('imagelist')) as $i[$key->$val]) print "<img src=\"".$i[$val]."\">";
      ?>
	  </span><br><hr>
        <div class="subcolumns">
          <div class="c25l">
          <a target="_blank" href="
          	<?php print $destiller->uri;?>">
      		<img src="http://open.thumbshots.org/image.aspx?url=
          	<?php print $destiller->uri;?>" border=\"1\" align=\"left\"/></a>
			 <?php
			  print "; ".DTHelper::listarrayrecurse($destiller->getSessionDataValue('headers'));//
			  ?>
			  <hr>Document Headers:
			  <?php
			  print DTHelper::listarrayrecurse($destiller->getDomStatisticsValue('meta'));
			  ?><hr>Crawled Pages: 
			  
          </div>
          <div class="c25l">General info on 
          <?php 
		  print $destiller->uri;
		  ?>
		  <?php 
			  print DTHelper::listarrayrecurse($_SESSION['crawldata']);
			?>
          </div>
          <div class="c25l">Text statistics: 
          <?php 
          $textstatistics01 = $destiller->getTextStatistics();
		  print DTHelper::listarrayrecurse($textstatistics01);
          //print "</pre><pre>";
		  print $destiller->getDocumentType(); print "<br>".($destiller->getCharSet());
		  //print "</pre>"; 
		  ?>
          </div>
          <div class="c25r">Session: 
    		<?php 
			print DTHelper::listarrayrecurse($destiller->getSessionData()); 
			?>
          </div>
        </div>
      </div>
      
      <div id="main">
        <div id="col1">
          <div id="col1_content" class="clearfix">
 
          <hr>Keywords: 
            <?php 
			$keywords=$destiller->getKeyWords();
			#77$keywords=
			print DTHelper::listarrayrecurse($keywords);
			?>
            <hr>Forms (pre-formatted):
			<?php print DTHelper::listarrayrecurse($destiller->getDomStatisticsValue('forms')); ?>
          <hr>Links (pre-formatted): <span class="fullinklist"><style type="text/css">.fullinklist img {width: 10px !important; height: 10px !important;}</style>
            <?php print DTHelper::listarrayrecurse($destiller->getDomStatisticsValue('linklist')); ?>
            </span>
          <?php print $destiller->getSessionDataValue('whoisinformation'); ?>
          </div>
        </div>
        <div id="col2">
          <div id="col2_content" class="clearfix">
          <style type="text/css">#col2_content img{width: 33px; height: 33px;}</style>
            DOM relatives: 

            <hr>Headlines (pre-formatted):
			<?php print DTHelper::listarrayrecurse($destiller->getDomStatisticsValue('headlines')); ?>
          </div>
        </div>
        <div id="col3">
          <div id="col3_content" class="clearfix">
          <?php 
		  //print_r($destiller->getDomStatisticsValue('imagelistfullurl')); 
		  ?>
          </pre>
          <?php
		   //print $destiller->getDomStatisticsValue('plaintext'); 
		   ?>
          <hr>Last destilled in session:
          <?php
			print DTHelper::listarrayrecurse((DTS::getSessionHistory()));
			print DTHelper::listarrayrecurse(DTS::makeSessionKeywords());
		  ?>
		  </pre>
          <hr>Top 10 keywords/keyphrases from current cloud:<ul>
          		<li class="notice">ERROR. NOT LOGGED IN.</li>
          	</ul>
          </div>
          <!-- IE Column Clearing -->
          <div id="ie_clearing"> &#160; </div>
        </div>
      </div>
      <?php 
	  /* print_r($_SESSION); 
	  print_r($destiller); */ 
	  ?>
