<?php
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;

class Doc{
	function __construct($conf=null){
		$this->title=$conf['title'];
		$this->bootstrap4=$conf['bootstrap4'];
		$this->jQueryUI=$conf['jQueryUI'];
		$this->fotawesome=$conf['fotawesome'];
	}
	
	public function head_tag(){
		$rval="
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset='UTF-8'>
				<meta name='viewport' content='width=device-width, initial-scale=1.0'>
				<title>{$this->title}</title>
			
		";
		
		if($this->bootstrap4) {
			$rval.="
				<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css'>
				<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'>
				<script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js' integrity='sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo' crossorigin='anonymous'></script>
				<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js' integrity='sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6' crossorigin='anonymous'></script>
			";
		}
		
		if($this->fotawesome) {
			$rval.="
				<script src='https://kit.fontawesome.com/a076d05399.js'></script>
			";
		}
		
		if($this->jQueryUI) {
			$rval.="
				<link rel='stylesheet' href='//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
  				<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
			";
		}
		
		$rval.="
			</head>
			<body>
		";
		
		return $rval;
	}
	
	public function foot_tag(){
		$rval="
			</body>
			</html>
		";
		
		return $rval;
	}
	
	public function title($t=null){
		$t=($t?$t:$this->title);
		return "<h2 class='container text-center'>$t</h2>";
	}
}