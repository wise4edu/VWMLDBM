<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
/*
  Description : DB Connection
*/
session_start();
error_reporting(~E_ALL); // No error message

/// DB and Table Prefix
$VWMLDBM['DB']="_INSTALL_DB_name"; // to be updated by setup
$VWMLDBM['DB_user']="_INSTALL_DB_user"; // to be updated by setup
$VWMLDBM['TB_prefix']="_INSTALL_TB_prefix"; // to be updated by setup
$VWMLDBM['DB_pwd']="_INSTALL_DB_pwd";
$VWMLDBM['VWMLDBM_RT']="_INSTALL_VWMLDBM_RT";

$DB=$VWMLDBM['DB'];
$TB_PRE=$VWMLDBM['TB_prefix']; 
$DTB_PRE=$DB.".".$TB_PRE; 
	
$conn=mysqli_connect("localhost",$VWMLDBM['DB_user'],$VWMLDBM['DB_pwd'],$VWMLDBM['DB']);
if(!$conn && substr($DB,0,9)!="_INSTALL_") die("Cannot connect to Mysql."); // installation script will handle DB conn error
//else if(!$conn && isset($_POST['from_step'])==false) header("Location:install/");
$VWMLDBM['DB_pwd']=null; // for security

// multi-language(JSON): menus, texts, javascript, buttons.
if(file_exists("$VWMLDBM[VWMLDBM_RT]/mlang/10.json")==false) return; // no json files
if($_GET['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=$_GET['vwmldbm_lang'];
$lang=$_SESSION['vwmldbm_lang'];
if(file_exists("$VWMLDBM[VWMLDBM_RT]/mlang/".$lang.".json"))
	$wmlang = json_decode(file_get_contents("$VWMLDBM[VWMLDBM_RT]/mlang/".$lang.".json"), true);
else if(file_exists("$VWMLDBM[VWMLDBM_RT]/mlang/10.json"))
	$wmlang = json_decode(file_get_contents("$VWMLDBM[VWMLDBM_RT]/mlang/10.json"), true); // load the default language: English
else echo"<script> alert('Your default language json file (English) do not exsit!');</script>";

if(isset($wmlang) && count($wmlang)>0) { // escape the single qoutation marks
	foreach($wmlang as $key_arr => $arr) 
		foreach($arr as $key =>$val) $wmlang[$key_arr][$key]=addslashes($val);
}

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); // enable error message again
?>