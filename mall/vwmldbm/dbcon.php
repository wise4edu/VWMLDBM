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

$DB=$VWMLDBM['DB'];
$TB_PRE=$VWMLDBM['TB_prefix']; 
$DTB_PRE=$DB.".".$TB_PRE; 
	
$conn=mysqli_connect("localhost",$VWMLDBM['DB_user'],$VWMLDBM['DB_pwd'],$VWMLDBM['DB']);
if(!$conn && substr($DB,0,9)!="_INSTALL_") die("Cannot connect to Mysql."); // installation script will handle DB conn error
//else if(!$conn && isset($_POST['from_step'])==false) header("Location:install/");
$VWMLDBM['DB_pwd']=null; // for security

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); // enable error message again
?>