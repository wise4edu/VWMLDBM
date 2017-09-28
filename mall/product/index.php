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
<html><head>
	<title><?=$wmlang[menu][prod_list]?></title>
	<script>
		function check_delete(pid){
			if(confirm("<?=$wmlang[js][del_option]?>"+pid)){
				document.form1.process.value='delete'; 
				document.form1.pid.value=pid; 
				document.form1.submit();
			}			
		}
	</script>
	<?css();?>
</head>
<body><center>
<?menu();?>
<form name='form1' action='index.php' method='POST'>
<h2><font color=blue><?=$wmlang[menu][prod_list]?></font></h2>
<?
if($_REQUEST["process"]=='insert') {
	$pname=$_REQUEST["pname"];
	$c_pcat=$_REQUEST["c_pcat"];
	$price=$_REQUEST["price"];
	$quantity=$_REQUEST["quantity"];
	$c_pcat_no=vwmldbm\code\get_c_no('c_pcat',$c_pcat);
	$sql="INSERT into $DTB_PRE"."_product (name,c_pcat,price,quantity) VALUES ('$pname','$c_pcat_no','$price','$quantity')";
	mysqli_query($conn,$sql);
}
else if($_REQUEST["process"]=='delete') {
	$pid=$_REQUEST['pid'];	
	$sql="DELETE from $DTB_PRE"."_product where id='$pid'";
	mysqli_query($conn,$sql);
}
$sql2="SELECT * from $DTB_PRE"."_product";
$res=mysqli_query($conn,$sql2);
if($res) {
	echo "<table align=center border=1><tr>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","id")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","name")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","c_pcat")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","price")."</th>";
	 echo"<th>".\vwmldbm\code\get_field_name("product","quantity")."</th>";
     echo"<th></th>";
	While($rs=mysqli_fetch_array($res)){
		echo "<tr>";
		echo "<td>$rs[id]</td>";
		echo "<td>$rs[name]</td>";
		echo "<td>".vwmldbm\code\get_c_name('c_pcat',$rs[c_pcat])."</td>";
		echo "<td>$rs[price]</td>";
		echo "<td>$rs[quantity]</td>";
		echo "<td><input type=button name='delete' value='".$wmlang[txt]['delete']."' onClick=\"check_delete('$rs[id]')\"></td>";
		echo "</tr>";	
	}
	echo "</table>";
}
?>
<br><input type=button value='<?=$wmlang[title][prod_reg]?>' onClick="document.location='product.php';">
<input type='hidden' name='process'>
<input type='hidden' name='pid'>
</form>
</center></body></html>