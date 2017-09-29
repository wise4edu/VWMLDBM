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
<html><head><title>Customer</title>
<SCRIPT>
function check_forms() {
	if(document.form1.cname.value=="") {
		alert("Enter Customer name!");
		document.form1.cname.focus();
		return false;
	}
	else return true;
}
</script>
<?css();?>
</head>
<body><center>
<?menu();?>
<h2><font color=blue><?=$wmlang[title][cust_reg]?></font></h2>
<form name='form1' action="index.php" method="POST" onSubmit="return check_forms();">
<center>
<table border=1>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","name")?></td> 
		<td><input type="text" size=10 name="cname"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","c_gender")?></td>
		<td><?\vwmldbm\code\print_code('RADIO','c_gender');?></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","dob")?></td>
		<td><input type="text" size=10 name="dob"></td>
	</tr>
	<tr>
		<td><?=\vwmldbm\code\get_field_name("customer","phone")?></td>
		<td><input type="text" size=20 name="phone"></td>
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
</center></body></html>