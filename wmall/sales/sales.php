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
	<title>Sales</title>
	<?css();?>
</head>
<body><center>
<?menu();?>
<h2><font color=blue><?=$wmlang[title][sales_reg]?></font></h2>
<form name='form1' action="index.php" method="POST">
<center>
<table border=1>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("sales","sales_date")?></td> 
		<td><input type="text" size=10 name="sdate">  (eg, 2017-05-01)</td>
	<tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("product","name")?></td>
		<td><input type="text" size=5 name="pid"> <input type='button' value='<?=$wmlang[txt]['search']?>' onClick="window.open('../product/product_search.php');"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","name")?></td>
		<td><input type="text" size=5 name="cid"> <input type='button' value='<?=$wmlang[txt]['search']?>' onClick="window.open('../customer/customer_search.php');"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("sales","quantity")?></td> 
		<td><input type="text" size=4 name="quantity"></td>
	<tr>
</table>
<br>
<p>
 <input type='hidden' name='process' value='insert'>
 <input type='submit' value='<?=$wmlang[txt]['add']?>'>
 <input type='reset' value='<?=$wmlang[txt]['reset']?>'>
</p>
 </center>
</form>
</center></body></html>