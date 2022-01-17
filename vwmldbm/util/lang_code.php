<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
?>
<? 
namespace vwmldbm;
require_once("../config.php");
require_once("../dbcon.php");
require_once("../lib/code.php");

$inst_no=$_SESSION['vwmldbm_inst']; 

// Permission Control TBM
$perm['R']='Y';
$perm['A']='Y';
$perm['M']='Y';
$perm['D']='Y';

define('SYSADMIN',123); // TBM
$_SESSION['c_adm_role']=SYSADMIN;  // TBM

// $code_name=$_REQUEST[code_name]; // code name
$code_name="vwmldbm_c_lang"; // code name  TBM

$field_names=array(); // fields of the code
$sql="show columns from {$DTB_PRE}$code_name";

$res=mysqli_query($conn,$sql);
if($res) while($rs=mysqli_fetch_array($res)) array_push($field_names,$rs['Field']);
$wasMod=false;	

if($_POST['operation']=='Modify' && $perm['M']=='Y') { // modify(udpate) a code record.
	foreach($_POST as $key => $val) $_POST[$key]=mysqli_real_escape_string($conn,$val);		
	foreach($field_names as $fdname){
		$t_field="h".$fdname;
		if($fdname=="code" || $fdname=="no" || $fdname=='inst') continue;
		else if($fdname=="use_yn") $_POST[$t_field]=strtoupper($_POST[$t_field]);
		
		$sql="update {$DTB_PRE}$code_name set $fdname='".$_POST[$t_field]."' where code='{$_POST['code']}'";		
		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) $wasMod=true;
	}	
	if(!$wasMod) echo "<script>alert('No datum was modfied.');</script>";
	else { //code info was successfully modified, so refresh the page
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
	$sql="delete from {$DTB_PRE}$code_name where code='{$_POST['code']}'";
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
else if($_POST['operation']=='Add' && $perm['A']=='Y') { // Add a code record
	$sql="insert into {$DTB_PRE}$code_name values('{$_POST['n_code']}'";
	foreach($field_names as $val){
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
		echo "<script>alert('[Success] {$_POST['n_code']} was added.');</script>";
		echo "<script>
			window.parent.document.form1.operation.value=window.parent.document.form1.operation.value;
			window.parent.document.form1.frame_op.value='Add';
			window.parent.document.form1.code_name.value='{$_POST['code_name']}';
			window.parent.document.form1.submit();
			</script>"; 
	}
}
?>
  
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/common.css" rel="stylesheet" type="text/css" />
</head>
  
<body style="text-align: center;">
<center><h3><?=$code_name?></h3></center>
<form name="form1" method="POST"  onSubmit="return checkForm(this);">
  <input type='hidden' name="operation">
  <input type='hidden' name="frame_op">
  <input type='hidden' name="code_name">
  <input type='hidden' name="code" value="<?=$_REQUEST['code']?>">
<?
	foreach($field_names as $val) if($val!="code" && $val!='inst') echo "<input type='hidden' name='h$val'>"; // print a hidden field for each field of the code except code itself
?>
<table border="1" align="center" cellpadding="5" cellspacing="0" bgcolor="#DFDFDF" style="border:0px #333333 solid;border-top-width:1px;">

<?
// add a new code record
// field names
if($perm['A']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) {
	echo "<tr>";
	foreach($field_names as $val) if($val!='inst') echo "<th>".\vwmldbm\code\get_field_name("academic_$code_name",$val)."</th>";
	echo "<th></th></tr>";
	echo "<tr>";
	foreach($field_names as $val) {
		if($val!='inst')
			echo "<td><input type=text name='n_".$val."' size=8></input></td>";	
	}

	echo "<td>";
	echo "<img src='img/add.png' class='img_button' ";
	echo "onClick=\" document.form1.operation.value='Add'; 
			document.form1.code_name.value='$code_name'; document.form1.submit(); \"";
	echo "</td></tr>";
}
?>
</table>
</form>
<br>
<form name="form2" method="POST">
<table border="1" align="center" cellpadding="5" cellspacing="0" bgcolor="#DFDFDF" style="border:0px #333333 solid;border-top-width:1px;">
<?
// retrieve code info
// field names
echo "<tr><th></th>";
echo "<th>".\vwmldbm\code\get_field_name("$code_name",'use_yn')."</th>";
foreach($field_names as $val) if($val!='inst' && $val!='use_yn') echo "<th>".code\get_field_name("_vwmldbm_$code_name",$val)."</th>";
echo "</tr>";

// data
$sql="select * from {$DTB_PRE}$code_name order by code asc";
$res=mysqli_query($conn,$sql);
$cnt = 0;
if($res)while($rs=mysqli_fetch_assoc($res)){
	echo "<tr>";
	$cnt++;
  // update a code record	
	echo "<td align='center'>";
	if($perm['M']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) {
		echo "<img src='img/ok.png' class='img_button' 
	      onClick=\" document.form1.operation.value='Modify'; 
		  document.form1.code_name.value='$code_name'; document.form1.code.value='{$rs['code']}';";
		foreach($field_names as $val_fd) if($val_fd!='code' && $val_fd!='inst') echo "document.form1.h$val_fd.value=document.form2.".$val_fd."_".$rs[code].".value; ";
		echo "document.form1.submit();\">";
	}
  // delete a code record	
	if($perm['D']=='Y' && strtoupper($_SESSION['c_adm_role'])==SYSADMIN) {
		echo "<img src='img/delete.png' class='img_button' ";
		echo "onClick=\" document.form1.operation.value='Delete'; 
		  document.form1.code_name.value='$code_name'; document.form1.code.value='{$rs['code']}'; del_confirm(); \"";
	}
	echo "</td>";
	
 // display a code record
	echo "<td><input type=text name='use_yn_".$rs['code']."' size='1' value='".$rs['use_yn']."'></input></td>";
	foreach($rs as $key=>$val) {
		if($key=='use_yn') continue;
		$readonly="";
		$rs[$key]=htmlspecialchars($val);
		if(strlen($val)>0&&strlen($val)>3)$tlen=strlen($val)-2; // adjust the input box size
		else $tlen=4;
		if($key=='code') $readonly=" readonly";
		if($key!='inst') echo "<td><input type=text name='".$key."_".$rs['code']."' size='$tlen' value='$val' $readonly></input></td>";
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