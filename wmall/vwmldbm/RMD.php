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
require_once("lib/lib_RMD.php"); // system RMD

if($_POST['inst_no']){ // inst_no was passed
	$inst_no=$_POST['inst_no'];
	if($inst_no==1) {
		if($_SESSION['inst']!=1) die; // inst=1 is super inst,so access should be protected
	}
	elseif($inst_no>1 && $inst_no!=$_SESSION['inst'] && $_SESSION['inst']!=1) die; // inst=1 can access other inst
}
else $inst_no=$_SESSION['inst']; 
set_time_limit(180); //  set the execution time limit in seconds
?>
<html>
<head>
 <title>WISE2 Relational Model Diagram</title>
 <meta charset="UTF-8">
 
 <div id='loading' class='overlay'><img id='loading-image' src='img/loading3.gif' alt='Loading...' /></div>
 <? require_once("lib/include_jQuery.php"); // jquery ?>
 <script src="js/loading.js"></script><!-- Loading page waiting JS code -->
 <link href="css/loading.css" rel="stylesheet" type="text/css"> <!-- Loading page waiting CSS code -->
 <link href="css/common.css" rel="stylesheet" type="text/css" />
 <link href='css/rmd.css' rel='stylesheet' type='text/css'>
<script src='js/rmd.js'></script>
<script>
var dbclicked_tb=null; // for showing only clicked table's arrows
var click_x; var click_y; // for pop-up window position
var is_mult_lang_open=false; // to close when a user open a new field attribute window.
$(function() {
	$( ".draggable" ).draggable
	({
		 drag: function(){
				$(".draggable").draggable({ revert: false });
				var offset = $(this).offset();

				// var xPos = offset.left;
				 //var yPos = offset.top;
		},
		 stop: function()
		{
			var offset = $(this).offset();
			//var xPos = offset.left;
			//var yPos = offset.top;
			draw_all_arrows();
		}
	});	
	
	$(function() {
		$(".draggable").click	(function(e){
			make_this_zindex_max(this.id)
			window.click_x=e.pageX;
			window.click_y=e.pageY;
		});
	});
	
	$(function() {
		$(".draggable").dblclick	(function(e){
			make_this_zindex_max(this.id);
			toggle_dblclikced_tb(this.id); 	
			show_hide_all_but_this_tb(this.id);
			draw_all_arrows();
		});
	});
});
</script>
<script>
/// Global Variables
var db_nos=[];// used for multi-DB app, so usually one
var DB_color=[]; // for the color of arrow by DB
var fkey_info =[]; // object array for foreign key object below
var fkidx=0; // current index of foreign_key_info 
var myDT_to_TbID=[]; // object array for the table which contains information about mapping DB_name.Table_name to Table's ID
var table_shrunk_y_size_by_table_no=[];
var wise_table=[]; // it is used for aligning tables by its level or db
var show_view=true;
</script>
</head>
<body>
<div id="field_attrib" title="Field Attributes" style='display:none;'></div>
<div id="mult_lang" title="Multi-language" style='display:none;'></div>
<form name='form1' method='POST'>
<?PHP	
if($_POST[operation]=='SAVE_TABLE_POSITION'){ // When user submit "Save table positions"
	global $conn,$DB,$DTB_PRE;
	mysqli_query($conn,"delete from $DTB_PRE"."_vwmldbm_rmd_tb_loc where inst='$inst_no'");
	for($i=0;$i<count($_POST[table_no]);$i++){
		$table_x_pos=$_POST[table_x_pos][$i];
		$table_y_pos=$_POST[table_y_pos][$i];
		$tb_no=$_POST[table_no][$i];
		$zidx=$_POST[table_zindex][$i];
		if($_POST[table_expanded][$i] =='+') { // not expanded
			$expanded_yn='+';
		}
		else if($_POST[table_expanded][$i] =='-'){
			$expanded_yn='-';
		}
		if($_POST[table_expanded][$i]!=NULL) $sql="insert into $DTB_PRE"."_vwmldbm_rmd_tb_loc (inst,tb_no,x_pos,y_pos,expanded_yn,zindex) 
			values('$inst_no','$tb_no','$table_x_pos','$table_y_pos','$expanded_yn','$zidx')";
		else $sql="insert into $DTB_PRE"."_vwmldbm_rmd_tb_loc (inst,tb_no,x_pos,y_pos,zindex) 
			values('$inst_no','$tb_no','$table_x_pos','$table_y_pos','$zidx')";
		mysqli_query($conn,$sql);
	}
}

echo " <input type=submit onClick=\"document.form1.operation.value='SAVE_TABLE_POSITION';\" value='Save table positions'> ";
echo " <input type=button onClick=\"align_by_DB();\" value='Align by DB'> ";
echo " <input type=button onClick=\"align_by_level();\" value='Align by Level'>";
echo " <input type=button onClick=\"align_by_sub();\" value='Align by Sub'>";
echo " <input type=hidden name='operation'>";
echo " Show VWMLDBM Tables <input type=checkbox id='vwmldbm_check' checked onClick=\"show_hide_vwmldbm(this);\"> ";
echo "<b><font color=blue>Double Click a table to see its own related tables!</font></b>";
echo "<br>";

$num_DB_printed_so_far=0; // for DB background color
$num_table_printed_so_far=0; // for table DIV ID name and more
$constraints=array();  // for displaying/hiding fields

$num_DB_printed_so_far=RMD\show_DBs(); //show DB names at the top
$num_table_printed_so_far=RMD\show_DB_ERD($inst_no);
	
?>
</form>
<div id='arrow_divs' style='position:absolute;top:0px;left:0px'></div>
<?PHP
$wise_table=array(); // array which contains all the tree nodes
sysmon\update_tables($inst_no);
?>
<script>
	var num_table_printed_so_far=<?=$num_table_printed_so_far?>; // used by get_max_table_zindex()
	document.body.onload=function(){	
		<? RMD\print_db_pos_js();?>
		<?
		// Create JavaScript object array for aligning tables by level or by db
		  for($i=0;$i<count($wise_table);$i++){
			 echo "wise_table.push(new Table_node('".$wise_table[$i]->tb_no."','".$wise_table[$i]->db_no."','".$wise_table[$i]->level."','$i','".$wise_table[$i]->type."','".$wise_table[$i]->name."'));";
		  }
		?>
		assign_table_id();
		draw_all_arrows(); // draw the arrows initially	
	}
</script>
</body>
</html>