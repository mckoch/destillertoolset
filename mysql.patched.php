<?
/*
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
$sql[] = "CREATE TABLE tmp_nodanglins AS SELECT count( * ) as total, master FROM pagerank GROUP BY MASTER";
$sql[] = "ALTER TABLE `tmp_nodanglins` ADD PRIMARY KEY ( `master` ) ";

/*
*	
*/
$sql[] = "CREATE TABLE tmp_unilist as (select distinct master as id from pagerank) union (select distinct slave as id from pagerank)";
$sql[] = "ALTER TABLE `tmp_unilist` ADD PRIMARY KEY ( `id` ) ;";

/*
*	
*/
$sql[] = "Insert into tmp_pr(id,nroout) select tmp_unilist.id, tmp_nodanglins.total  from tmp_unilist left join tmp_nodanglins  on (tmp_nodanglins.master = tmp_unilist.id)";


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

$sql_finish[] = "DROP TABLE tmp_unilist;";
$sql_finish[] = "DROP TABLE tmp_pr;";
$sql_finish[] = "DROP TABLE tmp_nodanglins;";
?>