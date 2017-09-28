<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../lib/lib_install.php");
$cur_step=2;
if($_POST['from_step']==($cur_step-1) || $_POST['from_step']==($cur_step+1) ) ; // pass
else if($_POST['from_step']==$cur_step || $_POST['operation']=='update_config'); // pass
else die; // illegal access

install\install_html_header($mode);
js();

echo "<input type='hidden' name='operation'>";
echo "<input type='hidden' name='from_step'>";
echo "<input type='hidden' name='mode' value='$mode'>";
echo "<input type='hidden' name='data_path' value='$data_path'>";
echo "<input type='hidden' name='DB' value='".$_POST['DB']."'>";
echo "<input type='hidden' name='DB_user' value='".$_POST['DB_user']."'>";
echo "<input type='hidden' name='dbpasswd' value='".$_POST['dbpasswd']."'>";
echo "<input type='hidden' name='TB_prefix' value='".$_POST['TB_prefix']."'>";

if($_POST['operation']=='update_config'){
	$update_ok=update_config_install(); /// Update config.php and generate configx.php 
	if($update_ok) {
		install\display_goto_next_tag($cur_step+1,$go_button_opt);
	}
}
else {
	install\display_goto_next_tag($cur_step+1,$go_button_opt);
}
echo "</form>";
install\install_html_footer();
?>

<?
function js(){
	global $cur_step;
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
		function check_update_form(obj){
			if(obj.value=='' || !validateEmail(obj.value)){
				alert('Enter a valid email address!');
				obj.focus();
			}
			else {
				document.form1.operation.value='update_config';
				document.form1.submit();			
			}
		}
	";
	echo "</script>";
}
function update_config_install(){
	$update_ok=true;

	/// update dbcon.php	
	$config_name="../common/dbcon.php";
	$config_contents = file_get_contents($config_name);
	$cnt=0; $tot_cnt=0; 
	$total_config_var_to_update=4;
	$config_contents=str_replace("_INSTALL_DB_name",$_POST['DB'],$config_contents,$cnt); $tot_cnt+=$cnt;
	$config_contents=str_replace("_INSTALL_DB_user",$_POST['DB_user'],$config_contents,$cnt); $tot_cnt+=$cnt;
	$config_contents=str_replace("_INSTALL_TB_prefix",$_POST['TB_prefix'],$config_contents,$cnt); $tot_cnt+=$cnt;
	$config_contents=str_replace("_INSTALL_DB_pwd",$_POST['dbpasswd'],$config_contents,$cnt); $tot_cnt+=$cnt;
	
	$config_contents=str_replace("_INSTALL_MALL_WWW_RT",dirname(dirname($_SERVER['PHP_SELF'])),$config_contents,$cnt); $tot_cnt+=$cnt;
	$config_contents=str_replace("_INSTALL_MALL_RT",addslashes(dirname(__DIR__)),$config_contents,$cnt); $tot_cnt+=$cnt;
	
	if($tot_cnt<$total_config_var_to_update) {
		install\display_warn("Something wrong! ".($total_config_var_to_update-$tot_cnt)." config variables were not updated(or same as before).");
	}
	if($tot_cnt>1){
		if (!$handle = fopen($config_name, 'w')) {
			echo "Cannot open file ($config_name)<br>";
			$update_ok=false;
		}
		if (fwrite($handle, $config_contents) === FALSE) {
			echo "<font color=red>Cannot write to file ($config_name)</font><br>";
			$update_ok=false;
		}
		else install\display_ok_msg("$config_name was updated.");
	}
	return $update_ok;
}

?>