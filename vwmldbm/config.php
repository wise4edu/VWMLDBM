<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
/**********************************
System environmental variables

Important! Make sure error reporting in php.ini should not allowed more than this:
	E_ALL & ~E_STRICT & ~E_NOTICE"
**********************************/

namespace vwmldbm;
error_reporting(~E_ALL); // No error message
if(!isset($_SESSION)) session_start(); 

/*** Display Language ***/
	if(!isset($_SESSION['vwmldbm_lang'])) $_SESSION['vwmldbm_lang']=10; // default English

/****** Settings of DB and Paths *****/
	$VWMLDBM['DB']="_INSTALL_DB_name"; // to be updated by setup
	$VWMLDBM['DB_user']="_INSTALL_DB_user"; // to be updated by setup
	$VWMLDBM['TB_prefix']="_INSTALL_TB_prefix"; // to be updated by setup
	$VWMLDBM['DB_pwd']="_INSTALL_DB_pwd"; // to be updated by setup
	$VWMLDBM['VWMLDBM_RT']="_INSTALL_VWMLDBM_RT"; // to be updated by setup
	$VWMLDBM['VWMLDBM_WWW_RT']="_INSTALL_VWMLDBM_WWW_RT"; // to be updated by setup
	$VWMLDBM['MULTI_INST']=false; // true: multi-institution mode, false: single institution mode
	$VWMLDBM['ADM_PWD']="vwmldbm"; // default password should be changed

	$GLOBALS['VWMLDBM']=$VWMLDBM;
	$GLOBALS['DB']=$VWMLDBM['DB'];
	$GLOBALS['TB_PRE']=$VWMLDBM['TB_prefix']; 
	
	$GLOBALS['DTB_PRE']=$VWMLDBM['DB'].".".$GLOBALS['TB_PRE'];
	if($GLOBALS['TB_PRE']) $GLOBALS['DTB_PRE'].="_";

	$GLOBALS['conn']=mysqli_connect("localhost",$VWMLDBM['DB_user'],$VWMLDBM['DB_pwd'],$VWMLDBM['DB']);
	$VWMLDBM['DB_pwd']=null; // for security
/*** End of Settings of DB and Paths ***/

/*** multi-language(JSON): menus, texts, javascript, buttons. ***/
	if(file_exists($VWMLDBM['VWMLDBM_RT']."/mlang/10.json")==false) return; // no json files
	if($_GET['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=$_GET['vwmldbm_lang'];
	else if(!$_SESSION['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=10; // default is English
	$lang=$_SESSION['vwmldbm_lang'];

	if(file_exists($VWMLDBM['VWMLDBM_RT']."/mlang/$lang.json"))
		$GLOBALS['wmlang'] = json_decode(file_get_contents($VWMLDBM['VWMLDBM_RT']."/mlang/$lang.json"), true);
	else if(file_exists($VWMLDBM['VWMLDBM_RT']."/mlang/10.json"))
		$GLOBALS['wmlang'] = json_decode(file_get_contents($VWMLDBM['VWMLDBM_RT']."/mlang/10.json"), true); // load the default language: English
	else echo"<script> alert('Your default language json file (English) do not exsit!');</script>";

	if(isset($GLOBALS['wmlang']) && count($GLOBALS['wmlang'])>0) { // escape the single qoutation marks
		foreach($GLOBALS['wmlang'] as $key_arr => $arr) 
			foreach($arr as $key =>$val) $GLOBALS['wmlang'][$key_arr][$key]=addslashes($val);
	}
/*** End of multi-language(JSON): menus, texts, javascript, buttons. ***/

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); // enable error messages again
?>