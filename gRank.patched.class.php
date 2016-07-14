<?php
/****************************************************************************************
*    guaranix Rank                                                                      *
*                                                                                       *
*    This is a class that implements the Google Rank algorithm called PageRank.         *
*    The algorithm is too much effective  for get what Page (or what are you trying     *
*    to rank) is more important than the others.                                        *
*                                                                                       *
*    The whole algorithm with explanation could be found at:                            *
*    http://dbpubs.stanford.edu:8090/pub/1999-66                                        *
*                                                                                       *
*    Becouse the algorithm is not mine (I didn't do the research about this ranking)    *
*    this source is free                                                                *
*                                                                                       *
*                                                                                       *
****************************************************************************************/
class gRank
{
	var $mysql;
	var $table;
	
	var $dump; 
	var $sql_limit;  #SQL limit 
	
	function gRank()
	{
		$this->dump = 0.85;
		$this->sql_limit = 10000;
	}
	function calculate()
	{
		$rankingstarttime = microtime();
		print " \n Preparing... ";
		//ob_flush();flush();
		$this->Prepare();
		print "Prepared\n \n ";
		
		print " \n Calculating the Pagerank with out Danglins\n \n ";
		//ob_flush();flush();
		$this->CalculatePR_WitoutDanglings();
		
		print " \n Calculating the Pagerank\n \n ";
		//ob_flush();flush();
		$this->CalculatePR();
		//ob_flush();flush();
		$this->FinishPR();
		print " \n FINISHED \n  \n ";
		//ob_flush();flush();
		$rankingendtime = microtime();
		$rankingtime = $rankingendtime - $rankingstarttime;
		$_SESSION['rankingtime'] = $_SESSION['rankingtime'] + $rankingtime;
		
	}
	
	function FinishPR()
	{
		global $sql_finish;
		
		foreach($sql_finish as $Sql)
			mysql_query($Sql,$this->mysql) or die(mysql_error($this->mysql));
	}
	
	function Prepare() 
	{
		global $sql;
		/* cleanup..... */
		mysql_query("DROP TABLE IF EXISTS tmp_nodanglins");
		//mysql_query("DROP TABLE IF EXISTS tmp_pr");
		mysql_query("DROP TABLE IF EXISTS tmp_unilist");
		mysql_query("DROP TABLE IF EXISTS pr_finished");
		/* .... go */		
		foreach($sql as $Sql) {
			mysql_query($Sql,$this->mysql) or die(mysql_error($this->mysql));
			//print $Sql;
		}
		#Now we will calculate formulates it of each page for its PageRank 
		for($i=0; ;$i++)
		{
			$main = mysql_query("select id from tmp_pr where formula LIKE '' limit ".$this->sql_limit,$this->mysql) or die(mysql_error($this->mysql));
			if (mysql_num_rows($main) == 0) break;
			//print "<h3>$i</h3>";
			//ob_flush();flush();
			$cnt =0;
			while ($row = mysql_fetch_array($main)) 
			{	
				$cnt++;
				//print " \n prep: ".$cnt;
				$Sql = "select pagerank.master, tmp_pr.nroout from pagerank inner 
					join tmp_pr on (tmp_pr.id = pagerank.master) where pagerank.slave = ".$row['id'];
				
				$xSql = mysql_query($Sql, $this->mysql) or die(mysql_error( $this->mysql));
				$formula = "";
				$counter= 0;
				while ($row1 = mysql_fetch_array($xSql))
				{	
					//print"<h4>".$row1['master']."::".$row1['nroout']." out danglins</h4>";
					$formula .= "PR(".$row1['master'].")/".$row1['nroout']." + ";
					$counter++;
					//print $counter.":".$formula.":<br><s__cript language=javascript>window.scrollTo(0,9e9);</script>";
					//ob_flush();flush();
				}
				$formula = substr($formula,0,strlen($formula) - 2); 
				$formula = mysql_escape_string($formula);
				mysql_free_result($xSql);
				$Sql = "update tmp_pr set formula = '".$formula."' where id = ".$row['id'];
				
				mysql_query($Sql,$this->mysql) or die(mysql_error($this->mysql));
				//print "done writing formula.<br><s__cript language=javascript>window.scrollTo(0,9e9);</script>";
				//ob_flush();flush();
			}
		}
	}
	
	function CalculatePR_WitoutDanglings()
	{
		for ($i=0; $i < 52; $i++)
		{
			//print "\tIteration No. $i\n<br><s__cript language=javascript>window.scrollTo(0,9e9);</script>";
			//ob_flush();flush();
			for ($e = 0; ; $e++)
			{
				if ($this->InternalPRcalculator($e,false) == false)
					break;
			}
		}
	}
	
	function CalculatePR()
	{
		for ($e = 0; ; $e++)
		{
			if ($this->InternalPRcalculator($e) == false)
				break;
		}
	}
	
	function InternalPRcalculator($Start, $dangling = true)
	{
		global $buff;
		$start = $Start * $this->sql_limit;
		$where = "";
		if (!$dangling)
		{
			//$where = "where nroout != 0"; 
			$where = "where nroout > 0";
			//print "danglings < 1 not counted."; 
			//ob_flush();flush();
		}
		
		$sql = mysql_query("select id, formula from tmp_pr $where limit $start, ".$this->sql_limit,$this->mysql) or die(mysql_error($this->mysql));

		if (mysql_num_rows($sql) == 0)
			return false;
		while ($row = mysql_fetch_array($sql))
		{	
			//print "for ".$row['formula'].": ".$row['formula']."<s__cript language=javascript>window.scrollTo(0,9e9);</script>";
			//ob_flush();flush();
			eval('$nro = (float)'.$row['formula'].';');
			$nro = (float)(1 - $this->dump) + $this->dump * ($nro);
			mysql_query("update tmp_pr set pr = '$nro' where id = ".$row['id']);
			$buff[$row['id']] = $nro; 
		}
		return true; 
	}
}

/*
*	This function will calcula the PR of a given page.
*/
function PR($i)
{
	global $buff; #This is the memory buffer
		
	if (!isset($buff[$i]))
	{
		$sql1 = mysql_query("select pr from tmp_pr where id = $i");
		$row = mysql_fetch_array($sql1);
		mysql_free_result($sql1);
		$buff[$i] = $row[0];
	}
	return $buff[$i];
}

/*  the SQL statements
*	Prepare
*/

$sql[] = "DROP TABLE IF EXISTS tmp_pr";
$sql[] = "
	CREATE TABLE tmp_pr (
	  id bigint(20) NOT NULL default '0',
	  formula longtext NOT NULL,
	  nroout int(11) NOT NULL default '0',
	  pr float NOT NULL default '0',
	  PRIMARY KEY  (id),
	  KEY nroout (nroout)
	)";

/*
*	
*/
$sql[] = "DROP TABLE IF EXISTS tmp_nodanglins";
$sql[] = "CREATE TABLE tmp_nodanglins AS SELECT count( * ) as total, master FROM pagerank GROUP BY MASTER";
$sql[] = "ALTER TABLE `tmp_nodanglins` ADD PRIMARY KEY ( `master` ) ";

/*
*	
*/
$sql[] = "DROP TABLE IF EXISTS tmp_unilist";
$sql[] = "CREATE TABLE tmp_unilist as (select distinct master as id from pagerank) union (select distinct slave as id from pagerank)";
$sql[] = "ALTER TABLE `tmp_unilist` ADD PRIMARY KEY ( `id` ) ;";

/*
*	
*/
$sql[] = "Insert into tmp_pr(id,nroout) select tmp_unilist.id, tmp_nodanglins.total  from tmp_unilist left join tmp_nodanglins  on (tmp_nodanglins.master = tmp_unilist.id)";

$GLOBALS['sql'] = $sql;
/*
*	This SQL command are executed when the PR calculation finish.
*/
$sql_finish[] = "DROP TABLE IF EXISTS PR_FINISHED";
$sql_finish[] = "CREATE TABLE `PR_FINISHED` (
  `posicion` int(11) NOT NULL auto_increment,
  `id` int(11) NOT NULL default '0',
  `pagerank` float NOT NULL default '0',
  PRIMARY KEY  (`posicion`),
  UNIQUE KEY `id` (`id`),
  KEY `pagerank` (`pagerank`)
)";

$sql_finish[] = "INSERT INTO PR_FINISHED(id,pagerank) SELECT tmp_pr.id,tmp_pr.pr FROM tmp_pr ORDER BY tmp_pr.pr DESC";
// evtly. keep these tables for further stats?!
//$sql_finish[] = "DROP TABLE tmp_unilist;";
//$sql_finish[] = "DROP TABLE tmp_pr;";
//$sql_finish[] = "DROP TABLE tmp_nodanglins;";
$GLOBALS['sql_finish'] = $sql_finish;
?>