<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;
session_start();
require_once("config.php");
require_once("lib/lib_sysmon.php"); // system monitor
if(!$conn) header("Location:install/");
else {
	require_once("lib/lib_install.php");
	if(install\installed()==false)	{
		header("Location:install/");
	}
}
set_time_limit(180); //  set the execution time limit in seconds
?>
<html>
<head>
 <title>VWMLDBM</title>
 <div id='loading' class='overlay'><img id='loading-image' src='img/loading3.gif' alt='Loading...' /></div>
 <? require_once("lib/include_jQuery.php"); // jquery ?>
 <script src="js/loading.js"></script><!-- Loading page waiting JS code -->
 <link href="css/loading.css" rel="stylesheet" type="text/css"> <!-- Loading page waiting CSS code -->
 <link href="css/common.css" rel="stylesheet" type="text/css" />
 <script>
 function investigate_tables(){ // when user clicked "Update" button 
	document.form1.operation.value='UPDATE_TABLE';
	document.form1.submit();
 }
 </script>
</head>
<body>
<?PHP
if($_REQUEST['operation']=="UPDATE_TABLE"){
	sysmon\investigate_all_tables($inst_no);
	sysmon\update_all_fkey_info($DB,$inst_no);
	sysmon\update_tables($inst_no);
}	
?>
<div align=center><h2><?=$DTB_PRE?> DBs and Tables</h2> 
	<input type=button value='Update' onClick="investigate_tables()">
	&nbsp;&nbsp;<input type=button value='Show RM Diagram' onClick="window.open('RMD.php')">
	&nbsp;&nbsp;<input type=button value='Language Setting' onClick="window.open('lang_code.php')">
	<table border=1>
	<tr>
		<th>DB Name</th>
		<th>Tables</th>
		<th>Comment</th>
	</tr>
		<?PHP
		$sql="select * from $DTB_PRE"."_vwmldbm_db"; 
		$res=mysqli_query($conn,$sql);
		if($res) while($rs=mysqli_fetch_array($res)){
			echo "<tr>
					<td>$rs[name]</td>";
			echo "	<td>";
			sysmon\print_tables($rs[no]);
			echo "  </td>";
			echo "	<td>$rs[comment]</td>
				  </tr>";
		}
		?>
	</table>
</div>
<form name='form1' method='POST'>
	<input type=hidden name='operation'>
</form>
</body>
</html>
