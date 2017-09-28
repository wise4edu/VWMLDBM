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
$WMALL['DB']="_INSTALL_DB_name"; // to be updated by setup
$WMALL['DB_user']="_INSTALL_DB_user"; // to be updated by setup
$WMALL['TB_prefix']="_INSTALL_TB_prefix"; // to be updated by setup
$WMALL['DB_pwd']="_INSTALL_DB_pwd";
$WMALL['MALL_WWW_RT']="_INSTALL_MALL_WWW_RT";
$WMALL['MALL_RT']="_INSTALL_MALL_RT";

$DB=$WMALL['DB'];
$TB_PRE=$WMALL['TB_prefix']; 
$DTB_PRE=$DB.".".$TB_PRE; 

$conn=mysqli_connect("localhost",$WMALL['DB_user'],$WMALL['DB_pwd'],$WMALL['DB']);
if(!$conn && substr($WMALL['DB'],0,9)!="_INSTALL_") die("Cannot connect to Mysql."); // installation script will handle DB conn error
$WMALL['DB_pwd']=null; // for security

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); // enable error message again

// multi-language(JSON): menus, texts, javascript, buttons.
if(file_exists("$WMALL[MALL_RT]/mlang/10.json")==false) return;
if($_GET['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=$_GET['vwmldbm_lang'];
$lang=$_SESSION['vwmldbm_lang'];
if(file_exists("$WMALL[MALL_RT]/mlang/".$lang.".json"))
	$wmlang = json_decode(file_get_contents("$WMALL[MALL_RT]/mlang/".$lang.".json"), true);
else if(file_exists("$WMALL[MALL_RT]/mlang/10.json"))
	$wmlang = json_decode(file_get_contents("$WMALL[MALL_RT]/mlang/10.json"), true); // load the default language: English
else echo"<script> alert('Your default language json file (English) do not exsit!');</script>";

if(isset($wmlang) && count($wmlang)>0) { // escape the single qoutation marks
	foreach($wmlang as $key_arr => $arr) 
		foreach($arr as $key =>$val) $wmlang[$key_arr][$key]=addslashes($val);
}
?>