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

if(!$_SESSION['vwmldbm_inst'] || $_SESSION['vwmldbm_utype']!='A') die;

// Permission Control TBM
$perm['R']='Y';
$perm['A']='Y';
$perm['M']='Y';
$perm['D']='Y';

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/common.css" rel="stylesheet" type="text/css" />
<link rel='stylesheet' href='../lib/jquery/ui/1.12.1/jquery-ui.css'>
<script src='../lib/jquery/jquery-3.2.1.min.js'></script>
<script src='../lib/jquery/ui/1.12.1/jquery-ui.min.js'></script>
</head>
  
<body style="text-align: center;">
<center><h3>Code List</h3></center>

  <script>
  $(document).ready(function() { // jQuery Dialog: change the size as you want
    $( "#dialog" ).dialog({
		width:'100%',height:640,
		autoOpen: false,
	});
	<?PHP
	if($_POST['frame_op']=='Modify' || $_POST['frame_op']=='Delete' || $_POST['frame_op']=='Add') { // when  code record was modified- to keep opening the working code window
		if($_REQUEST['highlightID']) $hParam="highlightID=".$_REQUEST['highlightID']."&";	
		echo "$('#dialog').dialog('open'); 
		document.getElementById('iframe').src='code_manage.php?".$hParam."code_name=".$_REQUEST['code_name']."&inst=".$_POST['inst']."';
		";
	}
	else echo "$('#dialog').dialog('close');"
	?>
	$('.open_modify').click(function(){ // modify a code set
        $('#dialog').dialog('open');
		document.getElementById('iframe').src="code_manage.php?code_name="+document.form1.code_name.value;
    });	
  });
 <?PHP
  echo "
	  function open_modify(code,inst){
		$('#dialog').dialog('open');
		document.getElementById('iframe').src='code_manage.php?code_name='+code+'&inst='+inst;
	  }
  ";
 ?>
  </script>
  
<div id="dialog" title="Code Operations" style="display:none;"> <!-- jQuery Dialog: iframes-->
  <iframe id='iframe' frameborder=0 width='100%' height='100%'></iframe>
</div>

<form method="post" name="form1" style="margin:0px;" onsubmit="document.form1.operation.value='Retrieve';">
<p style="text-align:center;"><font color=red>* <?=$wmlang['txt']['to_be_setup']?></font></p>
<p align=center>
<table border=1>
<tr>
	<th><?=$wmlang['txt']['code']?> <?=$wmlang['txt']['name']?></th>
	<th><?=$wmlang['txt']['code']?></th>
	<th># <?=$wmlang['txt']['code']?></th>
	<th># Y</th>
	<th></th>
</tr>
<?PHP

$cnt=0;
// $codes is from lib/code.php
foreach($codes as $key => $value){  // in-DB system codes
	echo "<tr>";
		echo "<td>";		
		if($key=='code_cset') echo "<font color='magenta'>{$value['name']}</font></td>";
		else echo "{$value['name']}</td>";
		
		echo "<td>";
			echo code\print_code($key,null,null,'code_name',null,null,null,'EN');
		echo "</td>";
		
		echo "<td align=center>";
			$tnum=code\get_code_stat($key,null,null,'EN');
			if($tnum<1) echo "<font color='red'><b>$tnum</b></font>";
			else echo $tnum;
		echo "</td>";		
		echo "<td align=center>";
			$ynum=code\get_code_stat($key,null,'USE_YN_Y','EN');
			if($ynum<1) echo "<font color='red'><b>$ynum</b></font>";
			else echo $ynum;
		echo "</td>";
		
		echo "<td>";
		echo "<img class='open_modify' src='../img/set.png' id='img_button_x' ";
		echo "onClick=\"document.form1.code_name.value='{$key}'\">";
		echo "</td>";	
	echo "</tr>";	  
	$cnt++;
}
// in-library system codes

?>
</table>
<br>
<b><?=$wmlang['txt']['total']?> : <?=$cnt?></b>
</p>
	<input type='hidden' name='operation' value='<?=$_POST['operation']?>'>
	<input type='hidden' name='code_name'> <!-- for manage_code.php -->
	<input type='hidden' name='frame_op'> 
	<input type='hidden' name='inst'>
	<input type='hidden' name='highlightID'> 
</form>

<center><h3 style='color:black;text-align:left;max-width:600px;'><pre>
<font color=blue>* You should create a code table as follows</font>:
CREATE TABLE `code_major` (
  <font color='red'>`code` int(11) NOT NULL AUTO_INCREMENT,</font>
  <font color='red'>`c_lang` int(11) NOT NULL DEFAULT '10',</font>
  </font>`name` varchar(255) DEFAULT NULL,</font>
  `abb_name` varchar(10) DEFAULT NULL,
  <font color='red'>`use_yn` char(1) DEFAULT 'Y',</font>
  <font color='red'>PRIMARY KEY (`code`,`c_lang`)</font>
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
</pre>
</h3>
</center>
</body>
</html>