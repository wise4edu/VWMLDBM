<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
?>
<? 
namespace vwmldbm;
require_once("config.php");
if(trim($_REQUEST['inst_no'])){ // inst_no was passed
	$inst_no=$_REQUEST['inst_no'];
	if($inst_no==1) {
		if($_SESSION['inst']!=1) die; // inst=1 is super inst,so access should be protected
	}
	elseif($inst_no>1 && $inst_no!=$_SESSION['inst'] && $_SESSION['inst']!=1) die; // inst=1 can access other inst
}
else $inst_no=$_SESSION['inst']; 

?>
<html>
<head>
 <title></title>
 <meta charset="UTF-8">
 <link href="css/common.css" rel="stylesheet" type="text/css" />
 <script>
 function save_it(c_lang,count){
	document.form1.c_lang.value=c_lang;
	document.form1.l_name.value=document.getElementById('c_name['+count+']').value;
	document.form1.submit();
 }
 </script>
</head>
<body>
<?
$tmp_field_id=$_GET[fid]; // original field_id
$field_id; // trimmed field_id
$db_name;
$tb_name;
$field_name=$_GET[fname];;

if($tmp_field_id[0]=='d')  // the field is display-default one-either primary key or foreign key related field
	$field_id=substr($tmp_field_id,1);
else if($tmp_field_id[0]=='h' && $tmp_field_id[1]=='d')  // the field is hidden-default one-either primary key or foreign key related field
	$field_id=substr($tmp_field_id,2);
	
preg_match_all("/(.*)\.(.*)\[(.*)\]/",$field_id,$out,PREG_PATTERN_ORDER);
$db_name=$out[1][0];
$tb_name=$out[2][0];
$field_num=$out[3][0];	

$sql="select no from $DTB_PRE"."_vwmldbm_fd where inst='$inst_no' and db_name='$db_name' and tb_name='$tb_name' and field='$field_name'";
$res=mysqli_query($conn,$sql);
if($res)$rs=mysqli_fetch_array($res);
$fd_no=$rs[no];

if($_POST[operation]=='SAVE'){
	$sql="select count(no) as num from $DTB_PRE"."_vwmldbm_fd_mlang where inst='$inst_no' and fd_no='$fd_no' and c_lang='$_POST[c_lang]'";
	$res_i=mysqli_query($conn,$sql);
	if($res_i)$rs_i=mysqli_fetch_array($res_i);
	if($rs_i[num]<1) { // no record exist for the language, so insert a new record.
		$sql="insert into $DTB_PRE"."_vwmldbm_fd_mlang  
		(inst,fd_no,c_lang,name) values('$inst_no','$fd_no','$_POST[c_lang]','$_POST[l_name]')";
		mysqli_query($conn,$sql);	
	}
	else { // the record exist
		$sql="update $DTB_PRE"."_vwmldbm_fd_mlang set 
			name='$_POST[l_name]'
			where fd_no='$fd_no' and c_lang='$_POST[c_lang]'";
		mysqli_query($conn,$sql);
	}
}

echo "<form name='form1' method=POST action='mult_lang.php?fid=$tmp_field_id"."&fname=$field_name'>";
echo "<input type='hidden' name='operation' value='SAVE'>";
echo "<input type='hidden' name='c_lang'>";
echo "<input type='hidden' name='l_name'>";
echo "<table border=1 width=200>";
echo "<tr><td align=center>Language</td><td align=center>value</td><td></td></tr>";

// original: $res_a=mysqli_query($conn,"select code,name from $DTB_PRE"."_code_c_lang where inst='$inst_no' and name<>'English' order by priority asc");
$res_a=mysqli_query($conn,"select code,name from $DTB_PRE"."_vwmldbm_c_lang where use_yn='Y' order by priority asc");
$count=0;
if($res_a) while($rs_a=mysqli_fetch_array($res_a)){
	$sql="select * from $DTB_PRE"."_vwmldbm_fd_mlang where inst='$inst_no' and fd_no='$fd_no' and c_lang='$rs_a[code]'";
	$res_b=mysqli_query($conn,$sql);
	if($res_b) {
		$rs_b=mysqli_fetch_array($res_b);
		echo "<tr><td>$rs_a[name]</td>
				<td><input type=text id='c_name[$count]' value='$rs_b[name]' size=20></td>
				<td colspan=2 align=center><input type=button value='Update' onClick=\"save_it('$rs_a[code]',$count)\"></td>
			</tr>";
		$count++;
	}
}
echo "</table>";
echo "</form>";
?>
</body>
</html>