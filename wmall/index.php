<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
/*
  Description : Main Page Index
*/
require_once("lib/lib_install.php");
if(install\installed('common/dbcon.php')==false){ // system not installed
	header("Location:install/");
}
else header("Location:customer/");
?>