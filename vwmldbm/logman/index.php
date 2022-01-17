<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;
session_start();

require_once("../lib/auth.php");
require_once("../lib/doc.php");
require_once("../config.php");

if($_GET['OP']=='LOGIN') { // if there is login session, log out and log in again
	auth\logout();	
}
else if($_GET['OP']=='LOGOUT') {
	auth\logout();
	if($_GET['PAGE']=='MAIN') header("Location:../");
	die;	
}
else if($_POST['pass']){ // passcode was submitted
	if($_POST['pass']===$VWMLDBM['ADM_PWD']) { 
		auth\login();
		header("Location:../");
		die;
	}
	else {
		$msg="<div style='color:red;'>Wrong Passcode!</div>";
		sleep(2); // prevent brut force attack
	}
}
else die;

$doc = new Doc(["bootstrap4" => true,
				"jQueryUI"=>true,
				"fotawesome"=>true]);

echo $doc->head_tag();
echo "
	<style>
	html {
		margin: auto;
	}
	
	body {
		text-align:center;
		padding-top: 5px;
		height: auto;
	}
	</style>
";

echo $doc->title();

if($msg) echo "<div class='container' style='width:80%;background:yellow;padding:5px;'>$msg</div>";
?>

<p><div class='container'>
	<form class="form" method="POST" action='./'>
		<div class="form-group">
		  <div class="col-xs-6">
			  <input type="password" class="form-control" name="pass" required id="pass" placeholder="Enter Code">
			  <br>
			  <button class="btn btn-lg btn-success" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Enter</button>
					
		  </div>
		</div>
	</form>
</p></div>
<?PHP
	$doc->foot_tag();
?>
