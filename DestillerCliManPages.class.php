<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
/* destiller    usage: 'destiller [url] [opt]' creates report on [url] with options [opt]
    top [n]    print current cloud's top [n] uri.
    find    usage: 'find [string]'
    initcloud    wipes all cloud data from database and runs a check.
    makecloud    as it says...
    batch [opt]    work with current batch using options [opt].
    quick [url]     create report from [url], add to and recompute cloud.     
*/
class DCliManPages {
	static function getManPage($app, $section = 0) {
			  //$myname = $_SERVER['SERVER_NAME'];
			  $logo = 
"___            _    _  _  _           
|   \  ___  ___| |_ (_)| || | ___  _ _ 
| |) |/ -_)(_-<|  _|| || || |/ -_)| '_|
|___/ \___|/__/ \__||_||_||_|\___||_|  
                                       
";
			  $help = "\n ************** \n".$app."\n ************** \n"."* help: This is more help on help. Type ? to get help, or man [app] for help on certain applications.\n For command help please refer to the website docs. \n";
			  $about = "\n ************** \n".$app."\n ************** \n"."* about: Text & Link Destiller V0.1 Command Line Text Operating System. All rights reserved.\n  This is a XI &Xi; system. Please refer to the website FAQ. \n For project details on Xi &Xi; visit http://bloogee.org/ or http://bloogee.net/. \n";
			  //$about .= DTS::makeWhoisInformation($myname);			  
			  $man = "\n ************** \n".$app."\n ************** \n"."* man: manual pages for applications (see '?') build from 'validcommands'. \n usage: 'man [app]' \n use 'man allmanpages' to show all manual entries in one screen.\n";
		 	  $batch = "\n ************** \n".$app."\n ************** \n"."* batch: batch manipulation. Add, edit, delete and run reports from the current batchlist. \n usage: 'batch [opt1] [(opt.) opt2]' \n options: \n add [url]: add URL to batch list, \n delete  [url]: delete URL from batch list, wildcards '%' in [string] supported), \n next [(opt.)number]: run next job from batch list,  or run next [number] jobs, \n list [(opt.)number]: list next [number] of jobs, \n find [string]: search for [string] in batch list. \n kill: stop cuurent background process if running. \n 'batch' with no options shows number of saved URLs on master batch. \n";
		 	  $last = "\n ************** \n".$app."\n ************** \n"."* last: show short foot print of  last report. \n usage: 'last [(opt.)int]' \n options: integer [int] to show the last [int] short prints.  \n";
			  $destiller = "\n ************** \n".$app."\n ************** \n"."* destiller: creates footprint on URL; must be preceeded by supported protocol; i.e. 'http://'. \n usage: 'destiller [url] [(opt.)options]' \n options: addDestillerObjectToRankingDb: will immediately add Destiller object to cloud database.  \n";
		 	  $quick = "\n ************** \n".$app."\n ************** \n"."* quick: creates footprint and adds report data to cloud; finally ranking table is re-computed. step-by-step addition of URL to current cloud. \n usage: 'quick [url]' \n If appendd to 'batch next' the cloud will be computed immediately after the successful batch job. \n";
		 	  $top = "\n ************** \n".$app."\n ************** \n"."* top: show top rankings from cloud. \n usage: 'top [(opt.)numberof]' \n [numberof]: if no number given basic cloudstats will be shown. \n If given a numerical [numberof] the corresponding number of top cloud ranking elements will be shown. \n";
		 	  $find = "\n ************** \n".$app."\n ************** \n"."* find: search a string in the cloud ranking database. Respects only elements in the computed cloud. \n usage: 'find [string]' \n wildcard usage: the percent sign '%' may be used to build wildcards. A default wildcard is added to beginning and ending of  string: %'[str]%[str2] [discarded after 1st space]'%. \n Results are ordered by cloud ranking descend. By default destiller will do a 'find' for every unregistered input string. \n";
		 	  $findreports = "\n ************** \n".$app."\n ************** \n"."* findreports: (alias: reports) search a string in the report repository.  \n usage: 'findreports [string]' \n wildcard usage: the percent sign '%' may be used to build wildcards. A default wildcard is added to beginning and ending of  string: %'[str]%[str2] [discarded after 1st space]'%. \n Results are ordered by CriD descend. \n";
		 	  $wildcard = "\n ************** \n".$app."\n ************** \n"."* %: '%' (percent sign) acts as repeatable wildcard. \n usage: 'key%some%' \n Notice: '% %' for spaces is invalid.";
			   $initcloud = "\n ************** \n".$app."\n ************** \n"."* initcloud: immediately wipes all cloud and report data fom user space. Does a final makecloud on empty database.\n usage: 'initcloud' \n";
		 	  $cloudinfo = "\n ************** \n".$app."\n ************** \n"."* cloudinfo: numerical info on current cloud size; no ranking (try 'top' for ranking stats).\n usage: 'cloudstatus' \n";
		 	  $status = "\n ************** \n".$app."\n ************** \n"."* status: info on user lockfile and remaining batch elements.\n usage: 'status' \n";
		 	  $makecloud = "\n ************** \n".$app."\n ************** \n"."* makecloud: re)computes the cloud. \n usage: 'makecloud' \n ";
		 	  $keyphrases = "\n ************** \n".$app."\n ************** \n"."* keyphrases: show top keyphrases with at least [int] number of words. \n usage: 'keyphrases [int]' \n ";
		 	  $bt = "\n ************** \n".$app."\n ************** \n"."* bt: bayesian talk, password generator and inspiring fun interface. \n usage: 'bt [opt: init, say, pass, talk]' \n options: \n init: generate pattern file from report data. \n, say [param: int n]: give a statement with [n] words,\n talk [param: save][param: ##followed by string]: , \n pass[opt. int length][opt. int number of]. \n talk [(opt.)SAVE] ## (string): give a comment on '##string'. ' SAVE ##some string' saves input to pattern file. \n UTILITY: 'btprep': prepare text base BEFORE running bt. \n";
			  $modules = "\n ************** \n".$app."\n ************** \n"."* modules: show a list of loaded system modules. \n usage: 'modules' \n";
		 	  $validcommands = "\n ************** \n".$app."\n ************** \n"."* validcommands: show a list of potentially valid functions / commands for CLI. \n usage: 'validcommands' \n";
		 	  $methods = "\n ************** \n".$app."\n ************** \n"."* methods: show all methods of a system module class. \n usage: 'methods [module]' \n"; 
		 	  $sysinfo = "***********************
Connected to Text & Link Destiller version '".$GLOBALS['version']." @ ".$_SERVER['SERVER_NAME']."' as user '".$_SESSION['userrankingdatabase']."'.
This tool is designed for full cloud reporting in command line style while producing a sequential output like a standard desktop calculator.  

* Available local applications & utils:
	destiller	*usage: 'destiller [url] [opt]' creates report on [url] with options [opt]
	top [n]	*print current cloud's top [n] uri.
	find	*usage: 'find [string]' in computed cloud. 
	findreports 	*usage: 'findreports [string]' in report database.
	initcloud	*wipes all cloud data from database and runs a check.
	makecloud	*compute the cloud from report data.
	keyphrases [n] 	*find keyphrases with >= [n] words
	batch [opt]	*work with current batch using options [opt].
	quick [url] 	*create report from [url], add to and recompute cloud.
	man [app]	*general help on application [app].
	
* Available utils for general and system information:
	help, ?	*print this help message
	validcommands	*list valid Destiller commands from modules.
	modules	*list accessable Destiller modules.
	methods [module]	*list methods for [module]
	DTS::, DDB::	*list methods from basic class modules
	status 		*user batch locking info.
	cloudinfo	*useful infomation about cloud.
	last 	* show last (int) report short prints.
	
[%all%other%input%]:	*every input not recognized as a command will be treated as search string (first 3 words). Wildcard '%' within string will be respected. \n *********************** \n";

		 	  
		 	  
		 	  
			  $allmanpages = "\n ***************** \n SYSTEM OVERVIEW \n ***************** \n".$sysinfo.$help.$man
			  ." \n * available documentation:  \n".$about." \n  \n".$batch." \n  \n".$status." \n  \n".$destiller." \n  \n".$quick." \n  \n".$history
			  ." \n  \n".$top." \n  \n".$find." \n  \n".$findreports." \n  \n".$initcloud." \n  \n".$makecloud." \n  \n".$cloudinfo." \n  \n".$keyphrases." \n  \n".$bt." \n  \n".$modules." \n  \n"
			  .$validfunctions." \n  \n".$methods." \n  \n".$wildcard;
			 		
		if ($$app == '') {return " no manual entry for ".$app;} else {
			return $$app;	
		}
	}
}
?>