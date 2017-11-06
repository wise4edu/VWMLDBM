<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../common/dbcon.php");
require_once("../lib/lib_mall.php");
require_once("../vwmldbm/config.php"); // VWMLDBM
?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?=$wmlang[title][prod_reg]?></title>
	<?css();?>
</head>
<body><center>
<?menu();?>
<h2><font color=blue><?=$wmlang[title][prod_reg]?></font></h2>
<form name='form1' action="index.php" method="POST">
<table border=1>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("product","name")?></td> 
		<td><input type="text" size=10 name="pname"></td>
	<tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("product","c_pcat")?></td> 
		<td><?\vwmldbm\code\print_code(null,'c_pcat');?></td>
	<tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("product","price")?></td>
		<td><input type="text" size=10 name="price"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("product","quantity")?></td>
		<td><input type="text" size=4 name="quantity"></td>
	</tr>
	<tr align=center>
		<td colspan=2>
		 <input name='submit' type="submit" value="<?=$wmlang[txt][add]?>">
		 <input type="reset" value="<?=$wmlang[txt]['reset']?>"> 
		 <input type=button value="<?=$wmlang[txt]['list']?>" onClick="document.location='index.php';">
		</td>	
	</tr>
</table>
</center>
<input type='hidden' name='process' value='insert'>
</form>
</center>
</body></html>