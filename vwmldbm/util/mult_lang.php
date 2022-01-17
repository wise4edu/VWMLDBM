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
$inst_no=$_SESSION['vwmldbm_inst']; 

?>
<html>
<head>
 <title></title>
 <meta charset="UTF-8">
 <link href="../css/common.css" rel="stylesheet" type="text/css" />
 <script>
 function save_it(c_lang,count){
	document.form1.c_lang.value=c_lang;
	document.form1.l_name.value=document.getElementById('c_name['+count+']').value;
	document.form1.submit();
 }
 </script>
</head>
<body>
<?PHP

$tmp_field_id=$_GET['fid']; // original field_id
$field_id; // trimmed field_id
$db_name;
$tb_name;
$field_name=$_GET['fname'];;

if($tmp_field_id[0]=='d')  // the field is display-default one-either primary key or foreign key related field
	$field_id=substr($tmp_field_id,1);
else if($tmp_field_id[0]=='h' && $tmp_field_id[1]=='d')  // the field is hidden-default one-either primary key or foreign key related field
	$field_id=substr($tmp_field_id,2);
	
preg_match_all("/(.*)\.(.*)\[(.*)\]/",$field_id,$out,PREG_PATTERN_ORDER);
$db_name=$out[1][0];
$tb_name=$out[2][0];
$field_num=$out[3][0];	

$sql="select no from {$DTB_PRE}vwmldbm_fd where db_name='$db_name' and tb_name='$tb_name' and field='$field_name'";
$res=mysqli_query($conn,$sql);
if($res)$rs=mysqli_fetch_array($res);
$fd_no=$rs['no'];

echo "<form name='form1' method=POST action='mult_lang.php?fid=$tmp_field_id"."&fname=$field_name'>";
echo "<input type='hidden' name='operation' value='SAVE'>";
echo "<input type='hidden' name='c_lang'>";
echo "<input type='hidden' name='l_name'>";
echo "<div style='margin-bottom:4px;font-weight:bold;'>$field_name</div>";
if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1) { // multi-inst mode
	$inst_arr=array();
	code\Inst_var::get_all_insts($inst_arr); // get information of other institutions
	
	// Update inst check box sessions
	if($_POST['operation']=='SAVE') {
		foreach($inst_arr as $key => $val) {
			if($_POST['inst_'.$key]=='Y') {
				$_SESSION['inst_'.$key]=true;				
			}
			else {
				$_SESSION['inst_'.$key]=false;
				$chk[$key]=null;
			}
		}
	}
	// Present institution list with checkboxes
	
	foreach($inst_arr as $key => $val) {
		if($_SESSION['inst_'.$key]) $chk[$key]="checked";
		else $chk[$key]=null;
	}
	
	echo "<div style='width:95%;'>";
	
	foreach($inst_arr as $key => $val) {
		echo "<input type='checkbox' name='inst_{$key}' value='Y' {$chk[$key]}>$key.{$val['inst_uname']}  &nbsp;";
	}
	echo "</div>";
}
	
	
if($_POST['operation']=='SAVE'){
	if($VWMLDBM['MULTI_INST'] && $_SESSION['vwmldbm_inst']==1) { // multi-institution mode
		foreach($inst_arr as $key => $val) {
			if($_SESSION['inst_'.$key]) {
				$i=$key;
				$sql="select count(no) as num from {$DTB_PRE}vwmldbm_fd_mlang where inst='$i' and fd_no='$fd_no' and c_lang='".$_POST['c_lang']."'";
			
				$res_i=mysqli_query($conn,$sql);
				if($res_i)$rs_i=mysqli_fetch_array($res_i);
				$_POST['l_name']=htmlspecialchars($_POST['l_name'], ENT_QUOTES); // SJH_MOD
				if($rs_i['num']<1) { // no record exist for the language, so insert a new record.
					$sql="insert into {$DTB_PRE}vwmldbm_fd_mlang  
					(inst,fd_no,c_lang,name) values('$i','$fd_no','{$_POST['c_lang']}','{$_POST['l_name']}')";	
					mysqli_query($conn,$sql);	
				}
				else { // the record exist
					$sql="update {$DTB_PRE}vwmldbm_fd_mlang set 
						name='{$_POST['l_name']}'
						where fd_no='$fd_no' and c_lang='{$_POST['c_lang']}' and inst='$i'";
					mysqli_query($conn,$sql);
				}
			}
		}
	}
	else { // single institution mode
		$sql="select count(no) as num from {$DTB_PRE}vwmldbm_fd_mlang where inst='$inst_no' and fd_no='$fd_no' and c_lang='".$_POST['c_lang']."'";
		$res_i=mysqli_query($conn,$sql);
		if($res_i)$rs_i=mysqli_fetch_array($res_i);
		$_POST['l_name']=htmlspecialchars($_POST['l_name'], ENT_QUOTES); // SJH_MOD
		if($rs_i['num']<1) { // no record exist for the language, so insert a new record.
			$sql="insert into {$DTB_PRE}vwmldbm_fd_mlang  
			(inst,fd_no,c_lang,name) values('$inst_no','$fd_no','{$_POST['c_lang']}','{$_POST['l_name']}')";
			mysqli_query($conn,$sql);	
		}
		else { // the record exist
			$sql="update {$DTB_PRE}vwmldbm_fd_mlang set 
				name='{$_POST['l_name']}'
				where fd_no='$fd_no' and c_lang='{$_POST['c_lang']}' and inst='$inst_no'";
			mysqli_query($conn,$sql);
		}
	}
}

echo "<table border=1 width=200>";
echo "<tr><td align=center>Language</td><td align=center>value</td><td></td></tr>";

// original: $res_a=mysqli_query($conn,"select code,name from {$DTB_PRE}code_c_lang where inst='$inst_no' and name<>'English' order by priority asc");
$res_a=mysqli_query($conn,"select code,name from {$DTB_PRE}vwmldbm_c_lang where inst='$inst_no' and use_yn='Y' order by priority asc");
$count=0;
if($res_a) while($rs_a=mysqli_fetch_array($res_a)){
	$sql="select * from {$DTB_PRE}vwmldbm_fd_mlang where inst='$inst_no' and fd_no='$fd_no' and c_lang='".$rs_a['code']."'";
	$res_b=mysqli_query($conn,$sql);
	if($res_b) {
		$rs_b=mysqli_fetch_array($res_b);
		echo "<tr><td>".$rs_a['name']."</td>
				<td><input type=text id='c_name[$count]' value='".$rs_b['name']."' size=20></td>
				<td colspan=2 align=center><input type=button value='Update' onClick=\"save_it('".$rs_a['code']."',$count)\"></td>
			</tr>";
		$count++;
	}
}
echo "</table>";
echo "</form>";
?>
</body>
</html>