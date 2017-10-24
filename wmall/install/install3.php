<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
require_once("../lib/lib_install.php");
require_once("../common/dbcon.php");
$cur_step=2;
if($_POST['from_step']==($cur_step-1) || $_POST['from_step']==($cur_step+1) ) ; // pass
else if($_POST['from_step']==$cur_step || $_POST['operation']=='update_config'); // pass
else die; // illegal access
$program_name="WISE Mall";
install\install_html_header($mode);

set_time_limit(180); //  set the execution time limit in seconds

//////////////// Table installation
$table_error=false; // control message for table creation
$data_error=false; // control message for data
$msg_control=""; // general control message
$msg_summary=""; 
$msg_detail="";

echo "Installing , <b>$program_name</b><br>";

///// 1. Table installation
$table_sql=array();

file_exists($WISE['wise_rt']."sql/table.php")
	or die("Table SQL doesn't exist!<br>");
require_once($WISE['wise_rt']."sql/table.php");

// Drop tables 
foreach (array_reverse($table_sql) as $key=>$sql){
	mysqli_query($conn,"drop table if exists $DTB_PRE"."_".$key);
	if(mysqli_affected_rows($conn)>0)
		echo "$key was dropped<br>";
}

// Create tables 
foreach ($table_sql as $key=>$sql){
	echo "<p><pre>$sql</pre></p>";
	$sql=str_replace('`', '', $sql); // pre-processing for possible errors
	$res=mysqli_query($conn,$sql);
	if($res) $msg_detail.= "[OK] $key table was successfully created!<br><?font>";
	else {
		$msg_detail.= "<font color=red>[Error] $key table was not created!</br></font>";
		$table_error=true;
	}
}

///// 2. Data Insertion
$data_sql=array();

file_exists("sql/data.php")
	or die("Data SQL doesn't exist!<br>");
require_once("sql/data.php");

foreach ($data_sql as $key=>$sql){
	//echo "<p><pre>$sql</pre></p>";
	$sql=str_replace('`', '', $sql); // pre-processing for possible errors
	$res=mysqli_query($conn,$sql);
	if($res) $msg_detail.= "[OK] $key data were successfully inserted!<br><?font>";
	else {
		echo $sql;
		$msg_detail.= "<font color=red>[Error] $key Data were not inserted!</br></font>";
		$data_error=true;
	}
}


output(); // installation output

function output(){
	global $table_error,$data_error,$msg_control,$msg_summary,$msg_detail;
	echo "<h2>Installation Result:</h2>";
	if($table_error) echo "Table Error:<br>".$table_error."<br>";
	if($data_error) echo "Data Error:<br>".$data_error."<br>";
	echo "Summary: <br>$msg_summary<br>";
	echo "Message Detail:<br>".$msg_detail."<br>";
	if(!$table_error) {
		echo "<h2>Installation was completed!</h2>";
		echo "<a href='".dirname(dirname($_SERVER['PHP_SELF']))."/vwmldbm/' target='_blank'>Start VWMLDBM Installation</a>";
		echo "<br>After VMWMLDBM installation, come back to ";
		echo "<a href='".dirname(dirname($_SERVER['PHP_SELF']))."/'>WMALL</a>";
	}
}
?>