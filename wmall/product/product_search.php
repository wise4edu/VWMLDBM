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
	<title><?=$wmlang[txt][search]?></title>
	<?css();?>
</head>
<body><center>

<h2><font color=red><?=$wmlang[menu][prod_list]?></font></h2>
<?
$sql="SELECT id,name,quantity from $DTB_PRE"."_product";
$res=mysqli_query($conn,$sql);
if($res) {
	echo "<table align=center border=1><tr>";
	 echo "<td align=center bgcolor='#ccffcc'>".\vwmldbm\code\get_field_name("product","id")."</td>";
	 echo "<td align=center bgcolor='#ccffcc'>".\vwmldbm\code\get_field_name("product","name")."</td>";
	 echo "<td align=center bgcolor='#ccffcc'>".\vwmldbm\code\get_field_name("product","quantity")."</td>";
	 echo "<td align=center bgcolor='#ccffcc'></td>";
	While($rs=mysqli_fetch_array($res)){
		echo "	<tr><td>$rs[id]</td>
					<td>$rs[name]</td>
					<td>$rs[quantity]</td>
					<td><input type=button value='".$wmlang[txt]['select']."' onClick=\"window.opener.form1.pid.value='$rs[id]'; window.close();\"></td>
				</tr>";	
	}
	echo "</table>";
}
?>
</center></body></html>