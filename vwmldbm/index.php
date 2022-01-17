<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;
session_start();

/// Check if logged in as Admin. If not show the login form
	require_once("lib/auth.php");
	if(!auth\auth()) {
		header("Location:logman/?OP=LOGIN");
	}

require_once("config.php");
require_once("lib/sysmon.php");
require_once("lib/code.php"); 
require_once("lib/doc.php");
require_once("lib/install.php"); 

// set the execution time limit in seconds (long execution time for DB checking updating may take a long time)
	set_time_limit(180); 

// check if the vwmldbm were installed. If not, forward to installation script.
	if(substr($VWMLDBM['DB'],0,9)=="_INSTALL_") header("Location:install/?mode=INSTALL");
	else { // config is okay now let's check if VWMLDB tables were installed in DB
		if(!install\installed())	{
			header("Location:install/?mode=INSTALL");
		}
	}

// check if the tables were same as before if not should click update button!
	sysmon\check_DB_same_as_before(); 


// HTML processing
	$doc = new Doc(["bootstrap4" => false,
				"jQueryUI"=>false,
				"fotawesome"=>false]);

	echo $doc->head_tag();
	//echo $doc->title('VWMLDBM');
	
// Display error/ohter message if there is any
	if($msg) echo "<div class='container' style='width:80%;background:yellow;padding:5px;'>$msg</div>";

?>
 <?PHP require_once("lib/include_jQuery.php"); // jquery ?>
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
	$inst_no=1; // Super Institution
	sysmon\investigate_all_tables(null,$inst_no);
	sysmon\update_all_fkey_info($DB,$inst_no);
	sysmon\update_tables($inst_no);
}	

if($_SESSION['vwmldbm_inst']!=1){	
	sysmon\add_lang_to_new_inst($_SESSION['vwmldbm_inst']); // add translations to new institution(s) if there is any
}
?>
<div align=center><h2><?=$DTB_PRE?> DBs and Tables</h2> 
	<p style="padding:0px 5px 5px 5px;">
		<input type=button value='Update' onClick="investigate_tables()">
		&nbsp;&nbsp;<input type=button value='Show RM Diagram' onClick="window.open('util/RMD.php')">
		&nbsp;&nbsp;<input type=button value='Codes' onClick="window.open('util/code_list.php')">
		&nbsp;&nbsp;<input type=button value='LOG OUT' style='color:magenta;' onClick="window.location.href='logman/?OP=LOGOUT&PAGE=MAIN'">
	<?PHP
		if($VWMLDBM['MULTI_INST']) {
			echo "&nbsp;&nbsp;<input type=button value='Institution' onClick=\"window.open('inst/')\">";
		}	
	?>	
	</p>
	<table border=1>
	<tr>
		<th>DB Name</th>
		<th>Tables</th>
		<th>Comment</th>
	</tr>
		<?PHP
		// Print all the tables of db(s)
		$dbArr=sysmon\get_dbs();
		foreach($dbArr as $db){
			echo "<tr>
					<td>".$db['name']."</td>";
			echo "	<td>";
			sysmon\print_tables($db['no']);
			echo "  </td>";
			echo "	<td>".$db['comment']."</td>
				  </tr>";
		}
		?>
	</table>
</div>
<form name='form1' method='POST'>
	<input type=hidden name='operation'>
</form>
<?PHP
	$doc->foot_tag();
?>