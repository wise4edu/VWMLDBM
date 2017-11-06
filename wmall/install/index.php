<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../lib/lib_install.php");
$cur_step=0;
$next_step=$cur_step+1;
/// PHP version Check
	$PHP_MIN_VERSION=install\get_version('php_min'); 
	if(phpversion()<$PHP_MIN_VERSION) {
		$msg= "<font color=red> [Problem] You should have PHP version $PHP_MIN_VERSION or higher.</font><br>";	
		$msg.=" Your PHP version: ".phpversion()."<br>";
		$msg.=" Please upgrade your PHP first.";
		install\display_error($msg);
		exit;
	}
	else {
		$msg="Your PHP version: ".phpversion();
	}
/// End of PHP version Check

install\install_html_header("Welcome to WISE Shopping Mall(Beta) Setup");
js();
install\display_ok_msg($msg);

echo "<input type='hidden' name='from_step' value='$next_step'>";
echo "<input type='hidden' name='operation'>";

$go_button_opt='disabled';
install\display_license(); // licence
install\display_goto_next_tag($next_step,$go_button_opt);
install\install_html_footer();
?>

<?PHP
function js(){
	global $mode,$cur_step;
	$next_step=$cur_step+1;
	echo "<script>";
	echo "
		function checkForm(){
			var obj=document.form1;
			obj.from_step.value='$cur_step';
			obj.action='install$next_step.php';
			obj.submit();
		}
	";

	echo"
	function toggle_continue(obj){
		if(obj.checked) document.form1.go_button.disabled=false;
		else document.form1.go_button.disabled=true;
	}
	function change_mode(){
		document.form1.mode.value=document.form1.maint_mode.value;
		document.form1.go_button.disabled=false;
	}
	";
	
	echo "</script>";
}

?>