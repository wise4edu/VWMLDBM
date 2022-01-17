<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/

namespace vwmldbm;
require_once("../config.php");
require_once("../lib/code.php");

// $instTxt from code.php;

if(!$_SESSION['vwmldbm_inst'] || $_SESSION['vwmldbm_utype']!='A') die;

// Permission Control TBM
$perm['R']='Y';
$perm['A']='Y';
$perm['M']='Y';
$perm['D']='Y';

define('SYSADMIN',123); // TBM
$_SESSION['c_adm_role']=SYSADMIN;  // TBM

$code_name=$_REQUEST['code_name']; // code name  TBM

$field_names=array(); // fields of the code
code\get_all_field_names($code_name,$field_names);

$c_lang_arr=array(); // for the synchronization of all language 
code\get_code_name_all($c_lang_arr,'vwmldbm_c_lang','code',null,'ALL_LANG','Y');
unset($c_lang_arr['10']); // remove English

if($code_name=='vwmldbm_c_lang') $codeLangName="code";
else $codeLangName="c_lang";

$wasMod=false;
if($_POST['operation']=='Modify' && $perm['M']=='Y') { // modify(udpate) a code record.	
	foreach($_POST as $key => $val) $_POST[$key]=mysqli_real_escape_string($conn,$val);
		
	foreach($field_names as $fdname){
		$t_field="h".$fdname;
		if($fdname=="code" || $fdname=="no" || $fdname=='inst' || $fdname==$codeLangName) continue;
		if($code_name != 'vwmldbm_c_lang' && $fdname=="use_yn" && $_POST[$codeLangName]!=10) continue; // only English
		
		if($code_name=="vwmldbm_c_lang" && $fdname==$codeLangName) continue;

		if($fdname=="use_yn") $_POST[$t_field]=strtoupper($_POST[$t_field]);
		
		if($code_name !='vwmldbm_c_lang') $lang_sql=" and c_lang='{$_POST[$codeLangName]}' ";
		$sql="update {$DTB_PRE}$code_name set $fdname='{$_POST[$t_field]}' where $instTxt and code='{$_POST['code']}' $lang_sql";		

		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) $wasMod=true;
	}	
	if(!$wasMod) echo "<script>alert('No data modfied.');</script>";
	else { //code info was successfully modified, so refresh the page
		
		// now modify other language use_yn	
		$sql="update $DTB_PRE"."_$code_name set use_yn='{$_POST['huse_yn']}' where $instTxt and code='{$_POST['code']}' and c_lang<>'10'";		
		mysqli_query($conn,$sql);
		// End of add other language use_yn		
		
		echo "<script>alert('[Success] {$_POST['code']} was modified. ');</script>";
		echo "<script>
			window.parent.document.form1.operation.value=window.parent.document.form1.operation.value;
			window.parent.document.form1.frame_op.value='Modify';
			window.parent.document.form1.code_name.value='{$_POST['code_name']}';
			window.parent.document.form1.submit();
			</script>"; 
	}
}
else if($_POST['operation']=='Delete' && $perm['D']=='Y') { // delete a code record
	if($code_name !='vwmldbm_c_lang' && $_POST[$codeLangName]!=10) $lang_sql=" and c_lang='{$_POST[$codeLangName]}' ";
	$sql="delete from {$DTB_PRE}$code_name where code='{$_POST['code']}' and $instTxt $lang_sql";

	mysqli_query($conn,$sql);
	if(mysqli_affected_rows($conn)>0) $wasMod=true;
	
	if(!$wasMod) echo "<script>alert('{$_POST['code']} was not deleted!');</script>";
	else { //code info was successfully deleted, so refresh the page
		echo "<script>alert('[Success] {$_POST['code']} was successfully deleted.');</script>";
		echo "<script>
			window.parent.document.form1.operation.value=window.parent.document.form1.operation.value;
			window.parent.document.form1.frame_op.value='Delete';
			window.parent.document.form1.code_name.value='{$_POST['code_name']}';
			window.parent.document.form1.submit();
			</script>"; 
	}
}
else if($_POST['operation']=='ADD' && $perm['A']=='Y') { // Add a code record
	
	$code_cset_problem=false;
	if($code_name=='code_cset') { // code set should be treated specially
		if(!code\check_code_set($_POST['n_code_name'])) $code_cset_problem=true;
	}
	
	if($code_name=='vwmldbm_c_lang'){
		$sql="insert into {$DTB_PRE}$code_name values('$inst','{$_POST['n_code']}'";
	}
	else if(!$code_cset_problem) {
		if($VWMLDBM['MULTI_INST'])
			$sql="insert into {$DTB_PRE}$code_name values('{$_POST['n_code']}','10'";
		else $sql="insert into {$DTB_PRE}$code_name values('{$_POST['n_code']}','10'";
	}
	
	foreach($field_names as $val){
		if($val=='inst' || $val==$codeLangName) continue;
		if($code_name=='vwmldbm_c_lang' && $val=='code') continue;
		if($val!="code") {
			$tname="n_".$val;
			if($val=="use_yn") $_POST[$tname]=strtoupper($_POST[$tname]);
			$sql.=",'".$_POST[$tname]."'";
		}
	}
	$sql.=")";

	mysqli_query($conn,$sql);
	if(mysqli_affected_rows($conn)>0) $wasMod=true;
	
	if(!$wasMod) echo "<script>alert('[Fail] {$_POST['n_code']} was not added!');</script>";
	else { //code info was successfully added, so refresh the page
		// now add other language records			
			if($code_name!='vwmldbm_c_lang' && $code_name!='code_cset') {
				foreach($c_lang_arr as $c) {
					if($c==10) continue; // Default Enlgish
					if($VWMLDBM['MULTI_INST'])
						$sql="insert into {$DTB_PRE}$code_name values('$inst','{$_POST['n_code']}','$c'";
					else $sql="insert into {$DTB_PRE}$code_name values('{$_POST['n_code']}','$c'";
					
					foreach($field_names as $val){
						if($val=='inst' || $val==$codeLangName) continue;
						if($val!="code") {
							$tname="n_".$val;
							if($val=="use_yn") $_POST[$tname]=strtoupper($_POST[$tname]);
							$sql.=",'".$_POST[$tname]."'";
						}
					}
					$sql.=")";
					mysqli_query($conn,$sql);
				}
			}
		// End of add other language records
		echo "<script>alert('[Success] {$_POST['n_code']} was added.');</script>";
		echo "<script>
			window.parent.document.form1.operation.value=window.parent.document.form1.operation.value;
			window.parent.document.form1.frame_op.value='Add';
			window.parent.document.form1.code_name.value='{$_POST['code_name']}';
			window.parent.document.form1.submit();
			</script>"; 
	}
	
}

// Create the codes whose language was added or enabled
if($code_name!='vwmldbm_c_lang') code\check_n_create_lang_code($code_name,$c_lang_arr);

?>
  
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/common.css" rel="stylesheet" type="text/css" />
</head>
  
<body style="text-align: center;">
<center><h3><?=$codes[$code_name]['name']." ($code_name)"?></h3></center>
<?PHP
echo "<h3 style='color:blue;'>If there is no specific value for \"code\", enter like 10,20,30</h3>";
	

if($code_name=='code_cset') {
	echo "<h3 style='color:red;'>If you don't know what this code does, do NOT touch except 'user_yn'!</h3>";
	echo "<h3 style='color:magenta;'>If you set 'user_yn' 'No', it will not be displayed in Book list</h3>";
	
}

?>
<form name="form1" method="POST"  onSubmit="return checkForm(this);">
  <input type='hidden' name="operation">
  <input type='hidden' name="frame_op">
  <input type='hidden' name="code_name">
  <input type='hidden' name="code" value="<?=$_REQUEST['code']?>">
  <input type='hidden' name="c_lang" value="<?=$_REQUEST[$codeLangName]?>">
<?php
	foreach($field_names as $val) if($val!="code" && $val!='inst' && $val!=$codeLangName) echo "<input type='hidden' name='h$val'>"; // print a hidden field for each field of the code except code itself
?>

<table border="1" align="center" cellpadding="5" cellspacing="0" bgcolor="#DFDFDF" style="border:0px #333333 solid;border-top-width:1px;">

<?
// add a new code record
// field names
if($perm['A']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) {
	echo "<tr>";
	echo "<th>".\vwmldbm\code\get_field_name("$code_name",'code')."</th>";
	echo "<th>".\vwmldbm\code\get_field_name("$code_name",$codeLangName)."</th>";
	echo "<th>".\vwmldbm\code\get_field_name("$code_name",'use_yn')."</th>";
	
	foreach($field_names as $val) {
		if($val!='inst' && $val!='use_yn' && $val!='code' && $val!=$codeLangName) {
			echo "<th>".\vwmldbm\code\get_field_name("$code_name",$val)."</th>";
		}
	}
	
	echo "<th></th></tr>";
	echo "<tr>";
	
	echo "<td><input type='text' name='n_code' required size=8></td>";
	
	echo "<td>";
	
	\vwmldbm\code\print_lang(10,'n_c_lang','Y',null,'ONE');
	echo "<td>".\vwmldbm\code\print_c_yn($code_name,'Y','n_use_yn')."</td>";
	
	foreach($field_names as $val) {
		if($val=='name') $ln=20;
		else $ln=8; // default
		if(substr($val,-3,3)=='_yn' and $val!='use_yn'){ // Y/N code
			echo "<td>";
			echo \vwmldbm\code\print_c_yn($val,null,'n_'.$val);
			echo "</td>";
			continue;
		}
			
		if($val!='inst' && $val!='use_yn' && $val!='code' && $val!=$codeLangName)
			echo "<td><input type=text name='n_".$val."' size='$ln'></td>";	
	}

	echo "<td>";
	echo "<img src='../img/add.png' class='img_button' onClick='add_code()'> ";
	
	echo "
	<script>
		function add_code() {
			var f=document.form1;
			if(f.n_code.value=='') {
				f.n_code.style.background='pink';
				f.n_code.focus();
				alert('Enter code!');
			}
			else {
				f.operation.value='ADD'; 
				f.code_name.value='$code_name'; 
				f.submit();
			}
		}
	</script>	
	";
	
	echo "</td></tr>";
}
?>
</table>
</form>
<br>
<form name="form2" method="POST">
<table border="1" align="center" cellpadding="5" cellspacing="0" bgcolor="#DFDFDF" style="border:0px #333333 solid;border-top-width:1px;">
<?PHP
// retrieve code info
// field names

echo "<tr><th></th>";
echo "<th>".\vwmldbm\code\get_field_name($code_name,'use_yn')."</th>";
foreach($field_names as $val) if($val!='inst' && $val!='use_yn') {
	echo "<th>".code\get_field_name($code_name,$val)."</th>";
}
echo "</tr>";

// data
if($code_name=='vwmldbm_c_lang')
	$sql="select * from {$DTB_PRE}{$code_name} where $instTxt order by code asc";
else $sql="select * from {$DTB_PRE}{$code_name} where $instTxt order by c_lang, code asc";

$res=mysqli_query($conn,$sql);
$cnt = 0;

if($res) while($rs=mysqli_fetch_assoc($res)) {
	if($codes[$code_name]['en_only_yn']=='Y' && ($code_name!='vwmldbm_c_lang' && $rs[$codeLangName]!=10)) continue; // if the code is English ony code, don't show other lang code
	echo "<tr>";
	$cnt++;
  // update a code record	
	echo "<td align='center'>";
	if($perm['M']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) {
		echo "<img src='../img/ok.png' class='img_button' 
	      onClick=\" document.form1.operation.value='Modify'; 
		  document.form1.code_name.value='$code_name'; document.form1.code.value='{$rs['code']}'; ";
		if($code_name!='vwmldbm_c_lang') echo "document.form1.c_lang.value='{$rs[$codeLangName]}';";
		foreach($field_names as $val_fd) {
			if($val_fd!='code' && $val_fd!='inst' && $val_fd!=$codeLangName) {
				if($val_fd=='use_yn' && $rs[$codeLangName]!=10 && $code_name!='vwmldbm_c_lang') continue; // non-English codes should be passed
				
				if($en_only_cols[$code_name][$val_fd]) echo "document.form1.h$val_fd.value=document.form2.".$val_fd."_".$rs['code']."_10.value;";
				else echo "document.form1.h$val_fd.value=document.form2.".$val_fd."_".$rs['code']."_{$rs[$codeLangName]}.value;";
			}
		}
		echo "document.form1.submit();\">";
	}
  // delete the code record	
  
	if(($code_name == 'vwmldbm_c_lang' || $rs[$codeLangName]=='10' || ($code_name!='vwmldbm_c_lang' && array_search($rs[$codeLangName],$c_lang_arr)===false)) && $perm['D']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) { // 10: Enlgish
		echo "<img src='../img/delete.png' class='img_button' ";
		echo "onClick=\" document.form1.operation.value='Delete'; 
		  document.form1.code_name.value='$code_name'; document.form1.code.value='{$rs['code']}';  document.form1.c_lang.value='{$rs[$codeLangName]}'; del_confirm(); \"";
	}
	echo "</td>";
	
 // display the code record
	if($code_name == 'vwmldbm_c_lang') { // modification is possible in vwmldbm_c_lang
		echo "<td>";
		echo \vwmldbm\code\print_c_yn("use_yn_{$rs['code']}_{$rs[$codeLangName]}",$rs['use_yn']);
		echo "</td>";
	}
	else if($rs[$codeLangName]=='10') { // modification is only possible for English
		echo "<td>";
		echo \vwmldbm\code\print_c_yn("use_yn_{$rs['code']}_10",$rs['use_yn']);
		echo "</td>";
	}
	else echo "<td></td>";
	
	foreach($rs as $key=>$val) {
		if($key=='use_yn') continue;
		$readonly="";
		$rs[$key]=htmlspecialchars($val);
		if(strlen($val)>0 && strlen($val)>3)$tlen=strlen($val)-2; // adjust the input box size
		else $tlen=4;
		if($key=='code' || $key==$codeLangName) $readonly=" readonly";
		if($key!='inst') {
			if($rs[$codeLangName]!=10 && $en_only_cols[$code_name][$key]) echo "<td></td>";
			else {
				if(substr($key,-3,3)=='_yn'){ // Y/N code
					echo "<td>";
					echo \vwmldbm\code\print_c_yn($key,$val,"{$key}_{$rs['code']}_{$rs[$codeLangName]}");
					echo "</td>";
				}
				else if($key ==$codeLangName && $code_name !='vwmldbm_c_lang') {
					echo "<td>";
					\vwmldbm\code\print_lang($rs[$codeLangName],"{$key}_{$rs['code']}_{$rs[$codeLangName]}",null,null,'ONE');
					echo "</td>";
				}
				else echo "<td><input type='text' name='{$key}_{$rs['code']}_{$rs[$codeLangName]}' size='$tlen' value='$val' $readonly></input></td>";
			}
		}
	}
	echo "</tr>";
}

?>
</table>
<div><b>Total : <?=$cnt?></b></div>
<!-- end of main table -->
</form>
<br>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
<tr> 
  <td align="center"><font color="#FF0000">*</font>Mandatory Field</td>
</tr>
</table>

<script>
function del_confirm(){
	if(confirm('Do you want to delete this code?')) document.form1.submit()
}

function checkForm(theForm) {
	theForm.operation.value="Modify";
	return true;
}
</script> 
</body>
</html>
