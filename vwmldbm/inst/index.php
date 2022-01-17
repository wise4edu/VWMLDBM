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
require_once("../lib/code.php");
require_once("../lib/install.php");

// Permission Control TBM
$perm['R']='Y';
$perm['M']='Y';
$perm['A']='Y';
$perm['D']='Y';

if($_POST['operation']=='UPDATE') {
	//print_r($_POST);
	if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1)
		$theInst=new code\Inst_var($_POST['no']);
	else $theInst=new code\Inst_var($_SESSION['vwmldbm_inst']);
	$post_email=trim($_POST['sadmin_email_'.$_POST['no']]);
	
	if($theInst->sadmin_email!=$post_email) {
		if($theInst::email_exist($post_email)) {
			$email_already_exist_error=true;
		}
		else {
			if($theInst::user_id_by_email($post_email)=="libadmin");
		}
	}
	
	$_POST['mode_'.$_POST['no']]=trim(strtoupper($_POST['mode_'.$_POST['no']]));

	if($email_already_exist_error) {
		$msg="<p><font color='red'>[Not Success] Email already exists!</font></p>";
	}
	else { // email is no problem, so proceed to the rest of the fields
		if($_SESSION['vwmldbm_inst']==1) {
			if($theInst->update($_POST,'SUPER')) 
				$msg="<p><font color='green'>[Success] Institution $theInst->no got updated!</font></p>";
			else $msg="<p><font color='orange'>[Not Success] Institution $theInst->no didn't get updated!</font></p>";
		}
		else {
			if($theInst->update($_POST)) 
				$msg="<p><font color='green'>[Success] Institution $theInst->no got updated!</font></p>";
			else $msg="<p><font color='orange'>[Not Success] Institution $theInst->no didn't get updated!</font></p>";
		}
	}
}
else if($_POST['operation']=='DELETE') {
	if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1)
		$theInst=new code\Inst_var($_POST['no']);
	else $theInst=new code\Inst_var($_SESSION['vwmldbm_inst']);
	
	$tbArr=array();
	install\get_tb_list($tbArr,'NO_INST');
	
	mysqli_begin_transaction($conn); // transaction: all or nothing!
	install\del_data($theInst->no,$tbArr);
	if(code\Inst_var::del($_POST['no'])) {
		$msg="<p><font color='green'>[Success] Institution $theInst->no got deleted!</font></p>";
		mysqli_commit($conn);
	}
	else {
		$msg="<p><font color='orange'>[Not Success] Institution $theInst->no didn't get deleted! Previous deletions were also rollbacked!</font></p>";
		mysqli_rollback($conn);
	}
}
else if($_POST['operation']=='ADD') {
	if(!filter_var($_POST['sadmin_email_add'],FILTER_VALIDATE_EMAIL)) {
		 $msg="<p><font color='red'>[Not Success] Check the email format!</font></p>";
	}
	else {
		if(code\Inst_var::add($_POST)) 
			$msg="<p><font color='green'>[Success] Institution added!</font></p>";
		else $msg="<p><font color='orange'>[Not Success] Institution was not added!</font></p>";
	}
}

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/common.css" rel="stylesheet" type="text/css" />
<style>
	th {
		background:#EEE;
		color:Black;
	}
</style>
</head>
  
<body style="text-align: center;">
<?=$msg;?>

<center><h3>Institution(s)</h3></center>

<form method="post" name="form1">
<input type=hidden name='no'>
<input type=hidden name='operation'>

<?PHP
if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1) {
	$superInst=new code\Inst_var($_SESSION['vwmldbm_inst']);
	$cnt=1;
	echo "
	<p align=center>
		<table border=1>
		<tr>
			<th>Inst No</th>
			<th>Inst Name</th>
			<th>Host</th>
			<th>Inst Random ID</th>
			<th>Inst Secret</th>
			<th>WISE (School ERP)<br>Login URI</th>
			<th>WISE Library<br>Super Admin Code</th>
			<th>WISE Library<br>Admin Code</th>
			<th>Mode<br>(INDEPENDENT / WITH_ERP)</th>
			<th></th>
		</tr>
		<tr>
			<td><input type='text' name='inst_no_add' size=2></td>
			<td><input type='text' name='inst_uname_add'></td>
			<td><input type='text' name='host_add'></td>
			<td><input type='text' name='inst_id_add'></td>
			<td><input type='text' name='secret_add'></td>
			<td><input type='text' name='other_prg_login_uri_add'></td>
			<td><input type='text' name='other_prg_sadm_add'></td>
			<td><input type='text' name='other_prg_adm_add'></td>				
			<td><input type='text' name='mode_add'></td>							
			<td><button type='button' onClick='add();'>Add</button></td>
		</tr>
		</table>
	</p>
	<br>
	";
}
?>
<p align=center>
	<table border=1>
	<tr>
		<th>Inst No</th>
		<th>Inst User Name</th>
		<th>Host</th>
		<th>Random Inst ID</th>
		<th>Inst Secret</th>
		<th>School ERP(eg, WISE)<br> Login URI<br>(eg, https://wise4edu.net/wise)</th>
		<th>WISE Library <br>Super Admin Code<br>(eg, 100,1210)</th>
		<th>WISE Library <br>Admin Code<br>(eg, 1230)</th>
		<th>Mode</th>
		<th></th>
	</tr>

<?PHP

	if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1) { // multi-institution mode and super institution admin
		$cnt=1;
		echo "
		<tr>
			<td>1</td>
			<td>{$VWMLDBM['INST_UNAME']}<br>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td>(config/app.php)</td>
			<td></td>
		</tr>";
		
		$arr=array();
		code\Inst_var::get_other_insts($arr); // get information of other institutions
		
		foreach($arr as $key => $val) {
			$cnt++;
			echo "
			<tr>
				<td>{$key}</td>
				<td><input type='text' name='inst_uname_$key' value=\"{$val['inst_uname']}\"></td>
				<td><input type='text' name='host_$key' value=\"{$val['host']}\"></td>
				<td><input type='text' name='inst_id_$key' value=\"{$val['inst_id']}\"></td>
				<td><input type='text' name='secret_$key' value=\"{$val['secret']}\"></td>
				<td><input type='text' name='other_prg_login_uri_$key' value=\"{$val['other_prg_login_uri']}\"></td>
				<td><input type='text' name='other_prg_sadm_$key' value=\"{$val['other_prg_sadm']}\"></td>
				<td><input type='text' name='other_prg_adm_$key' value=\"{$val['other_prg_adm']}\"></td>				
				<td><input type='text' name='mode_$key' value=\"{$val['mode']}\"></td>								
				<td>
			";
			echo "<button type='button' onClick=\"update('$key');\">Update</button> &nbsp; ";
			echo "<button style='background:pink' type='button' onClick=\"del('$key');\">Delete</button>";
			echo "
				</td>
			</tr>";
		}
	}
	
	else if($VWMLDBM['MULTI_INST']) { // multi-institution and other institution admin		
		$arr=array();
		$theInst=new code\Inst_var($_SESSION['vwmldbm_inst']);
		$val=$theInst;
		$key=$_SESSION['vwmldbm_inst'];

		$cnt++;
		echo "
		<tr>
			<td>{$key}</td>
			<td><input type='text' name='inst_uname' value=\"{$val->inst_uname}\"></td>
			<td><input type='text' name='host' value=\"{$val->host}\"></td>
			<td><input type='text' name='inst_id' value=\"{$val->inst_id}\"></td>
			<td><input type='text' name='secret' value=\"{$val->secret}\"></td>
			<td><input type='text' name='other_prg_login_uri' value=\"{$val->other_prg_login_uri}\"></td>
			<td><input type='text' name='other_prg_sadm' value=\"{$val->other_prg_sadm}\"></td>
			<td><input type='text' name='other_prg_adm' value=\"{$val->other_prg_adm}\"></td>
		";
		if($_SESSION['vwmldbm_inst']==1)
			echo "<td><input type='text' name='mode' value=\"{$val->mode}\"></td>";
		else echo "<td>{$val->mode}</td>";
		
		echo "<td>";
		echo "<button type='button' onClick=\"update('$key');\">Update</button> &nbsp; ";
		echo "<button style='background:pink' type='button' onClick=\"del('$key');\">Delete</button>";
		echo "
			</td>
		</tr>";
	}
?>
	</table>
	<br>
	<?
	if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1)
		echo "<b>{$wmlang['txt']['total']} : $cnt</b>";
	?>
	
	
	<script>
	function update(no) {
		document.form1.no.value=no;
		document.form1.operation.value='UPDATE';
		document.form1.submit();		
	}
	
	function del(no) {
		if(confirm("Would you like to delete "+no+"?")){
			document.form1.no.value=no;
			document.form1.operation.value='DELETE';
			document.form1.submit();
		}
	}
	
	function add() {
		var f=document.form1;
		if(f.inst_uname_add.value=='') {			
			f.inst_uname_add.style.background='pink';
			return false;
		}
		else f.inst_uname_add.style.background='';
		
		if(f.host_add.value=='') {			
			f.host_add.style.background='pink';
			return false;
		}
		else f.host_add.style.background='';
		
		if(f.mode_add.value=='') {			
			f.mode_add.style.background='pink';
			return false;
		}
		else f.mode_add.style.background='';
		
		if(confirm("Would you like to add a new Institution?")){
			f.operation.value='ADD';
			f.submit();
		}
	}
	</script>
</p>
</form>
</body>
</html>