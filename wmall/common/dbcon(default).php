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

?>