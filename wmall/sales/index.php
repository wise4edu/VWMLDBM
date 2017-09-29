<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../common/dbcon.php");
require_once("../lib/lib_mall.php");
?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?=$wmlang[menu][sales_list]?></title>
	<?css();?>
</head>
<body><center>
<?menu();?>
<form name='form1' action='sales_result.php' method='POST'>
<h2><font color=blue><?=$wmlang[menu][sales_list]?></font></h2>
<?
if($_REQUEST["process"]=='insert') {
	$sdate=$_REQUEST["sdate"];
	$pid=$_REQUEST["pid"];
	$cid=$_REQUEST["cid"];
	$quantity=$_REQUEST["quantity"];
	$sql="INSERT into $DTB_PRE"."_sales (pid,cid,sales_date,quantity) VALUES ('$pid','$cid','$sdate','$quantity')";

	mysqli_query($conn,$sql);
	$sql2="select quantity from $DTB_PRE"."_product where id='$pid';";
	$res=mysqli_query($conn,$sql2);
	$rs=mysqli_fetch_array($res);
	$current_quantity=$rs['quantity'];
	$new_quantity=$current_quantity-$quantity;
	$sql3="UPDATE $DTB_PRE"."_product set quantity=$new_quantity where id='$pid'";
	mysqli_query($conn,$sql3);
}
else if($_REQUEST["process"]=='delete') {
	$sid=$_REQUEST['sid'];	
	$sql_quantity="SELECT pid, quantity from $DTB_PRE"."_sales where no='$sid'";
	$res=mysqli_query($conn,$sql_quantity);
	$rs=mysqli_fetch_array($res);
	$spid=$rs['id'];
	$quantity=$rs['quantity'];
	
	$sql="DELETE from $DTB_PRE"."_sales where no='$sid'";
	mysqli_query($conn,$sql);
	$sql_quantity2="update $DTB_PRE"."_product set quantity=quantity+$quantity where id='$spid'";
	mysqli_query($conn,$sql_quantity2);
}
$sql2="SELECT s.no, c.name, p.name as product_name,s.quantity, s.sales_date from $DTB_PRE"."_sales as s, $DTB_PRE"."_customer as c,
 $DTB_PRE"."_product as p where s.pid=p.id and s.cid=c.id";
$res=mysqli_query($conn,$sql2);

if($res) {
	echo "<table align=center border=1><tr>";
	 echo"<th align=center bgcolor='#ccffcc'>".\vwmldbm\code\get_field_name("customer","name")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","name")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("sales","quantity")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("sales","sales_date")."</th>";
	 echo"<th></th>";
	echo"</tr>";
	while($rs=mysqli_fetch_array($res)){
		echo "	<tr><td>$rs[product_name]</td>
					<td>$rs[name]</td>
					<td>$rs[quantity]</td>
					<td>$rs[sales_date]</td>
					<td><input type=button name='delete' value='".$wmlang[txt]['delete']."' onClick=\"document.form1.process.value='delete'; document.form1.sid.value='$rs[no]'; document.form1.submit();\"></td>
				</tr>";	
	}
	echo "</table>";
}
?>
<br><input type=button value='<?=$wmlang[title][sales_reg]?>' onClick="document.location='sales.php';">
<input type='hidden' name='process'>
<input type='hidden' name='sid'>
</form>
</center></body></html>