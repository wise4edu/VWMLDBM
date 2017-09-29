<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../common/dbcon.php");
require_once("../lib/lib_mall.php");
require_once("../vwmldbm/lib/lib_code.php");
?>
<!DOCTYPE HTML>
<html><head><title>Customer List</title>
<script>
function delete_confirm(name,id) {
	if(confirm("<?=$wmlang[js][del_option]?> " +name)) {
		document.form1.process.value='delete'; 
		document.form1.cid.value=id; 
		document.form1.submit();
	}
}
</script>
<?css();?>
</head>
<body><center>
<?menu();?>
<form name='form1' action='index.php' method='POST'>
<h2><font color=blue><?=$wmlang[menu][cust_list]?></font></h2>
<?
$cname=$_GET["cname"];
$gender=$_GET["gender"];
$age=$_GET["age"];
$phone=$_GET["phone"];

if($_REQUEST["process"]=='insert') {
	$cname=$_REQUEST["cname"];
	$c_gender_no=vwmldbm\code\get_c_no('c_gender',$_REQUEST["c_gender"]);
	$dob=$_REQUEST["dob"];
	$phone=$_REQUEST["phone"];
	
	$sql="INSERT into $DTB_PRE"."_customer (name,c_gender,dob,phone) VALUES ('$cname','$c_gender_no','$dob','$phone')";
	mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) {
		echo "<script>alert('".$wmlang[js][success]." ".$_REQUEST['cname']." ".$wmlang[js][suc_add]."');</script>";
	}
	else {
		echo "<script>alert('".$wmlang[js][error]." ".$_REQUEST['cname']." ".$wmlang[js][not_suc_add]."');</script>";
	}
}
else if($_REQUEST["process"]=='delete') {
	$cid=$_REQUEST['cid'];	
	$sql="DELETE from $DTB_PRE"."_customer where id='$cid'";
	mysqli_query($conn,$sql);
	if(mysqli_affected_rows($conn)>0) {
		echo "<script>alert('".$wmlang[js][success]." ".$wmlang[js][del_success]."');</script>";
	}
	else {
		echo "<script>alert('".$wmlang[js][error]." ".$wmlang[js][not_del_success]."');</script>";
	}
}


$sql2="SELECT * from $DTB_PRE"."_customer";
$res=mysqli_query($conn,$sql2);

if($res) {
	echo "<table border=1><tr>";
	 echo"<th>".\vwmldbm\code\get_field_name("customer","name")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("customer","c_gender")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("customer","dob")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("customer","phone")."</th>";
	 echo"<th></th>";
	 echo"<th></th>";
	 
	while($rs=mysqli_fetch_array($res)){
		echo "<tr>";
		echo "<td>$rs[name]</td>";
		echo "<td>".vwmldbm\code\get_c_name('c_gender',$rs[c_gender])."</td>";
		echo "<td>$rs[dob]</td>";
		echo "<td>$rs[phone]</td>";
		echo "<td><input type=button name='delete' value='".$wmlang[txt][delete]."' onClick=\"delete_confirm('$rs[name]','$rs[id]');\"></td>";
		echo "<td><input type=button name='edit' value='".$wmlang[txt][modify]."' onClick=\"window.open('customer_edit.php?cid=$rs[id]')\"></td>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
<br><input type=button value='<?=$wmlang[title][cust_reg]?>' onClick="document.location='customer.php';">
<input type='hidden' name='process'>
<input type='hidden' name='cid'>
</form>
</center></body></html>