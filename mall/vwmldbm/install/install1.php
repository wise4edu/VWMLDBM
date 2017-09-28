<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;
require_once("../lib/lib_install.php");
$cur_step=1;
if($_POST['from_step']==($cur_step-1) || $_POST['from_step']==($cur_step+1) ) ; // pass
else if($_POST['from_step']==$cur_step && $_POST['operation']=='db_check') ; // pass if DB Check
else die; // illegal access

install\install_html_header();
js();
echo "<input type='hidden' name='operation'>";
echo "<input type='hidden' name='db_check'>";
echo "<input type='hidden' name='from_step'>";

if(display_db_check()) install\display_goto_next_tag($cur_step+1);
echo "</form>";
install\install_html_footer();
?>

<?
function display_db_check(){
	require_once("../dbcon.php");
	echo "<br><b>Step 1. DB Setup:</b><br>";
	echo "<br><b> * Enter Database Account Information into which you want to install VWMLDBM.<br>";
	echo "<table>";
	echo "<tr>
			<td>Database Host</td>
			<td>localhost</td>
		  </tr>
		";

	if($_POST['operation']=='db_check'){
		error_reporting(~E_ALL); // No error message
		$conn=mysqli_connect("localhost",$_POST['DB_user'],$_POST['dbpasswd'],$_POST['DB']);
		if($conn) {
			$db_con_ok=true;
			if($db_con_ok && !install\tb_prefix_available($conn,$_POST['DB'],$_POST['TB_prefix']))
				$tb_pre_ok=true;
			else $tb_pre_ok=false;
		}
		else $db_con_ok=false;		
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); // enable error message again
	}
	
// DB name
	if($_POST['DB']) $val=$_POST['DB'];
	else if(substr($VWMLDBM['DB'],0,9)=="_INSTALL_") 
		$val="";
	else $val=$VWMLDBM['DB'];	
	echo "<tr>
			<td>Database Name</td>
			<td><input type=text name='DB' value='$val' $readonly onchange='document.form1.go_button.disabled=true;'></td>
		  </tr>
		";
		
// DB User
	if($_POST['DB_user']) $val=$_POST['DB_user'];
	else if(substr($VWMLDBM['DB_user'],0,9)=="_INSTALL_") 
		$val="";
	else $val=$VWMLDBM['DB_user'];	
	echo "<tr>
			<td>Database User</td>
			<td><input type=text name='DB_user' value='$val' $readonly onchange='document.form1.go_button.disabled=true;'></td>
		  </tr>
		";

// DB Password
	$val=$_POST['dbpasswd'];

	echo "<tr>
		<td>Database Password</td>
		<td><input type=text name='dbpasswd' value='$val' $readonly onchange='document.form1.go_button.disabled=true;'></td>
	  </tr>
	";

// Table prefix
	if($_POST['TB_prefix']) $val=$_POST['TB_prefix'];
	else if(substr($VWMLDBM['TB_prefix'],0,9)=="_INSTALL_") 
		$val="";
	else $val=$VWMLDBM['TB_prefix'];	
	echo "<tr>
			<td>Table Prefix</td>
			<td><input type=text name='TB_prefix' value='$val' $readonly onchange='document.form1.go_button.disabled=true;'></td>
		  </tr>
		";	

	echo "<tr>
			<td> </td>
			<td><input type='button' value='Check DB Settings' 
			onClick=\"check_db();\"></td>
		  </tr>
		";	
			
	echo "</table>";
	
	if($_POST['operation']=='db_check'){
		if($db_con_ok) {			
			if(install\check_mysql_version($conn)==false) die;
			install\display_ok_msg("DB connection is working!");
			if($tb_pre_ok) install\display_ok_msg("Table Prefix okay(exists)!");
			else install\display_error("Table Prefix is NOT okay(does not exist). Did you install the host applicaiton first? If not do it and come back.!");
		}
		else install\display_error("DB connection is NOT working!");		
			
	}
	if($mode!='init_install') return true;
	else if($db_con_ok && $tb_pre_ok) return true;
	else return false;
}

function js(){
	global $cur_step;
	$next_step=$cur_step+1;
	echo "<script>";
	echo "
		function checkForm(){
			var obj=document.form1;
			obj.from_step.value='$cur_step';
			obj.action='install$next_step.php';
			obj.operation.value='update_config';
			obj.submit();
		}
	";
	
	echo"	
		function check_db(){
			if(document.form1.DB.value==''){
				alert('Enter DB Name!');
				document.form1.DB.focus();
			}
			else if(document.form1.DB_user.value==''){
				alert('Enter DB User!');
				document.form1.DB_user.focus();
			}
			else if(document.form1.dbpasswd.value==''){
				alert('Enter DB Password!');
				document.form1.dbpasswd.focus();
			}
			else if(document.form1.TB_prefix.value==''){
				alert('Enter Table Prefix!');
				document.form1.TB_prefix.focus();
			}
			else {
				var pre=document.form1.TB_prefix.value;
				if(isLetter(pre.substr(0,1))==false) {
					alert('Table Prefix should start with an alphabet!');
					document.form1.TB_prefix.focus();
					return;
				}
				document.form1.operation.value='db_check';
				document.form1.from_step.value='$cur_step';
				document.form1.submit();
			}
		}
		function isLetter(s) {
		  if(s.match(/[a-z]/i)) return true;
		  else return false;
		}
	";
	
	echo "</script>";
}

?>