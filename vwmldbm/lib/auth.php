<?php
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm\auth;

function auth(){
	if(isset($_SESSION['vwmldbm_inst'])&& $_SESSION['vwmldbm_inst']===1); // okay
	else {
		logout();
		return false;
	}
	if(isset($_SESSION['vwmldbm_utype'])&& $_SESSION['vwmldbm_utype']==='A'); // okay
	else {
		logout();
		return false;
	}
	
	return true; // logged in
}

function logout() {
	$_SESSION['vwmldbm_inst']=null; // Make sure clear the session
	$_SESSION['vwmldbm_utype']=null; // Make sure clear the session	
}

function login() {
	$_SESSION['vwmldbm_inst']=1; 
	$_SESSION['vwmldbm_utype']='A'; // Admin
}
