<?php
/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
/*
	common format and output helpers
	
	DEPRECATED: use includes/stringUtils.inc.php instead!!!
*/

class DTHelper {
// ul: output as list	
	public static function listarray($array) {
	    $content = "<ul>";
	    	foreach ($array as $property => $value){
			$content .= "<li>".$property.": ".$value."</li>";
 		 	foreach ($value as $tempkey => $value2) {
		 	    $content .= "<ul><li>".$tempkey.": ".$value2."</li></ul>";
			} 
		}
		$content .= "</ul>";
		return $content;
	}
	
	public static function listarray2($array) {
	    	$content = "<ul>";
		 	foreach ($array as $key => $val) {
					if (is_array($val)||is_object($val)){
					    $tmpid=uniqid(time());
					    $content .= "<a href=\"#\" onclick=\"Effect.toggle('$tmpid','appear'); return false;\">$key: $val</a>
						<div id=\"$tmpid\" style=\"display:none;\" ><li>";
			 	        $content .= "<ul>";
			 	        foreach ($val as $key2 => $val2) {
	                    	 if (is_array($val2)||is_object($val2)){
			 	            	$content .= "<li><ul>";
				 	        		foreach ($val2 as $key3 => $val3) {
				 	            		$content .= "<li>".$key3.": ".$val3."</li>";
						 			}
		 	            		$content .= "</ul></li>";
							} else {
								$content.="<li>".$key2.": ".$val2."</li>";
							}
			 	        }
						$content.= "</ul></div>";
		 			} else $content.="<li>".$key.": ".$val."</li>";
		 	}
		 	$content.="</ul>";
		return $content;
	}
	
	
	
	public static function listarrayrecurse($array) {
	    $content = "<ul>";
		foreach ($array as $key => $val) {
	    	if (is_array($val)||is_object($val)){
				$tmpid=uniqid(time());
			    $content .= "<li>";// .$key.": ".$val."::";
	    	    $content .= "<a href=\"#\" onclick=\"Effect.toggle('$tmpid','appear');
				return false;\">$key</a><div id=\"$tmpid\" style=\"display:none;\" >";
				$content .= DTHelper::listarrayrecurse($val);
	    		$content .= "</div></li>";
			} else {
				$content.="<li>".$key.": ".$val."</li>";
			}
		}
		$content.="</ul>";
		return $content;
	}

	public static function makeDestillerForm() {
	$form = "
			<script type='text/javascript' language='javascript'>
function verifyMe(){
var msg='';

if(document.getElementById('host').value==''){
	msg+='- URL\n\n';}

if(document.getElementById('action').value==''){
	msg+='- action\n\n';}

if(document.getElementById('save').value==''){
	msg+='- save\n\n';}

if(document.getElementById('captcha').value==''){
	msg+='- please fill in the captcha\n\n';}

if(msg!=''){
	alert('The following fields are empty or invalid:\n\n'+msg);
	return false
}else{
	return true }

}
</script>
<form name='MyForm' action='".$_SERVER['PHP_SELF']."' method='GET' enctype='application/x-www-form-urlencoded' onsubmit='return verifyMe();'>
<input type='hidden' name='destillersession' id='destillersession' value='".$_SESSION['userrankingdatabase']."'>
<input type='hidden' name='option' id='option' value='com_controller'>
<input type='hidden' name='Itemid' id='Itemid' value='62'>
<table class='table_form_1' id='table_form_1' cellspacing='0'>
	<tr>
		<td class='ftbl_row_2' ><LABEL for='action' ACCESSKEY='none' ><b style='color:red'>*</b>action
		</td>
		<td class='ftbl_row_2a' ><select name='action' id='action'>
			<option value='http'>http</option>
			<option value='quick'>quick</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class='ftbl_row_1' ><LABEL for='save' ACCESSKEY='none'><b style='color:red'>*</b>save
		</td>
		<td class='ftbl_row_1a' ><select name='save' id='save'>
			<option value='none'>nosave</option>
			<option value='cloud'>cloud</option>
			<option value='instantcloud'>instantcloud</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class='ftbl_row_1' ><b style='color:red'>*</b>WHOIS
		</td>
		<td class='ftbl_row_1a' >
			<LABEL ACCESSKEY='2'><input type='checkbox' name='whoisinformation' id='whoisinformation[0]' value='true'>true</LABEL>&nbsp;
		</td>
	</tr>
	<tr>
		<td class='ftbl_row_1' ><LABEL for='host' ACCESSKEY='1' ><b style='color:red'>*</b>URL
		</td>
		<td class='ftbl_row_1a' ><input type='text' name='host' id='host' size='45' maxlength='180'  value='http://'>
		</td>
	</tr>
	<tr>
		<td colspan='2' align='right'><img src='captcha.php?.png'>
									<input type='text' name='captcha' id='captcha' size='8' maxlength='8'  value='captcha'> 
				
		<input type='submit' name='submit' value='Submit'>&nbsp;<input type='reset' name='reset' value='Reset'>
		</td>
	</tr>
</table>
</form>					
			";
	return $form;
	}
	
	public static function makeFindForm(){
	
	$form = "
		<script language='javascript'>
		function verifyMe(){
		var msg='';
		
		if(document.getElementById('destillersession').value==''){
			msg+='- \n\n';}
		
		if(document.getElementById('action').value==''){
			msg+='- \n\n';}
		
		if(document.getElementById('save').value==''){
			msg+='- Find\n\n';}
		
		if(msg!=''){
			alert('The following fields are empty or invalid:\n\n'+msg);
			return false
		}else{
			return true }
		
		}
		</script>
		<form name='Find in ranking DB' action='".$_SERVER['PHP_SELF']."' method='GET' enctype='application/x-www-form-urlencoded' onsubmit='return verifyMe();'>
		<input type='hidden' name='destillersession' id='destillersession' value='".$_SESSION['userrankingdatabase']."'>
		<input type='hidden' name='action' id='action' value='find'>
		<input type='hidden' name='option' id='option' value='com_controller'>
		<input type='hidden' name='Itemid' id='Itemid' value='62'>
		<table class='table_form_1' id='table_form_1' cellspacing='0'>
			<tr>
				<td class='ftbl_row_1' ><LABEL for='save' ACCESSKEY='1' ><b style='color:red'>*</b>Find
				</td>
				<td class='ftbl_row_1a' ><input type='text' name='save' id='save' size='45' maxlength='100'  value=''>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='right'><input type='submit' name='submit' value='Submit'>&nbsp;<input type='reset' name='reset' value='Reset'>
				</td>
			</tr>
		</table>
		</form>
		";	
		return $form;
	}
	public static function makeGenerateForm() {
		$form = "
		<form name='Generate Cloud' action='".$_SERVER['PHP_SELF']."' method='GET' enctype='application/x-www-form-urlencoded' onsubmit='return verifyMe();'>
		<input type='hidden' name='destillersessionid' id='destillersessionid' value='".$_SESSION['userrankingdatabase']."'>
		<input type='hidden' name='action' id='action' value='cloud'>
		<input type='hidden' name='option' id='option' value='com_controller'>
		<input type='hidden' name='Itemid' id='Itemid' value='62'>
		<table class='table_form_1' id='table_form_1' cellspacing='0'>
			<tr>
				<td class='ftbl_row_1' ><b style='color:red'>*</b>generate cloud?
				</td>
				<td class='ftbl_row_1a' >
					<LABEL ACCESSKEY=''><input type='checkbox' name='save' id='save' value='generate'></LABEL>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='right'><input type='submit' name='submit' value='Submit'>
				</td>
			</tr>
		</table>
		</form>";
		return $form;
	}
	
	public static function makeResetForm() {
	$form = "
	<form name='(re)initialitze your database' action='".$_SERVER['PHP_SELF']."' method='GET' enctype='application/x-www-form-urlencoded' onsubmit='return verifyMe();'>
	<input type='hidden' name='action' id='action' value='start'>
	<input type='hidden' name='option' id='option' value='com_controller'>
	<input type='hidden' name='Itemid' id='Itemid' value='62'>
	<table class='table_form_1' id='table_form_1' cellspacing='0'>
		<tr>
			<td class='ftbl_row_1' ><LABEL for='save' ACCESSKEY='none' ><b style='color:red'>*</b>initialize / reset? <em>do this once BEFORE using Destiller.</em>
			</td>
			<td class='ftbl_row_1a' ><input type='checkbox' name='save' id='save' value='initialize'>
			</td>
		</tr>
		<tr>
			<td colspan='2' align='right'><input type='submit' name='submit' value='Submit'>
			</td>
		</tr>
	</table>
	</form>";
	return $form;
	}
	public static function makeCaptcha (){
		require('includes/captcha/captcha.class.php');
		$captcha = new captcha;
		$_SESSION['CAPTCHAString'] = $captcha->getCaptchaString();
	}
}


?>