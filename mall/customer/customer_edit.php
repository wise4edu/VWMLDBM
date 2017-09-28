<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../common/dbcon.php");
require_once("../lib/lib_mall.php");

$sql="select * from $DTB_PRE"."_customer where id='$_GET[cid]'";
$res=mysqli_query($conn,$sql);
$rs=mysqli_fetch_array($res);
?>

<!DOCTYPE HTML>
<html>
	<head><title><?=$wmlang[txt][modify]?></title>
	<?css();?>
</head>
<body><center>
<h2><font color=red><?=$wmlang[title][cust_info]?></font></h2>
<?
if($_REQUEST[process]=='edit') {
// Write your code here for the editing!!!

	echo"<script>window.close();</script>";
}
?>
<form>
<table border=1>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","name")?></td> 
		<td><input type="text" size=10 name="cname" value="<? echo($rs[name]);?>"></td>
	<tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","c_gender")?></td>
		<td><select name="gender"><option value='M' <? if($rs[gender]=='M') echo("selected");?>>Male</option><option value='F' <? if($rs[gender]=='F') echo("selected");?>>Female</option></select></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","dob")?></td>
		<td><input type="text" size=4 name="age" value="<? echo($rs[age]);?>"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","phone")?></td>
		<td><input type="text" size=20 name="phone" value="<? echo($rs[phone]);?>"></td>
	</tr>
	<tr align=center>
		<td colspan=2>
		 <input name='submit' type="submit" value="<?=$wmlang[txt]['modify']?>">
		 <input type="reset" value="<?=$wmlang[txt]['reset']?>">
		 <input type=button value="<?=$wmlang[txt]['goback']?>" onClick="window.close();">
		</td>		
	</tr>
</table>
</center>
<input type='hidden' name='process' value='edit'>
</form>
</center></body></html>