<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
  
/*==============================================================
function list:
	display_ok_msg($msg=null)
	display_warn($msg=null)
	display_error($msg=null)
	install_html_header($title=null)
	install_html_footer()
	display_goto_next_tag($no,$opt=null)
	display_go_back_tag($no,$opt=null)
	display_license($mode=null)
	check_mysql_version($conn)
	get_version($item="version")
	get_license_version()
	tb_prefix_available($conn=null,$db=null,$pre=null)
	installed($path=null)
	innoDB_ok($conn)
	inst_exist($tbName)
	del_data($inst,$tbArr,$opt=null)
 ============================================================*/
namespace vwmldbm\install;

function display_ok_msg($msg=null){
	if($msg) echo "[OK] $msg<br>";
}

function display_warn($msg=null){
	if($msg) echo "<font color=magenta>[Warning] $msg</font><br>";
}

function display_error($msg=null){
	if($msg) echo "<font color=red>[Error] $msg</font><br>";
}

function install_html_header($title=null){
	$txt="
	<!DOCTYPE html>
	<html>
	<head>
	<title>WISE V2 $title</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	</head>
	<body>
	<form name='form1' method='POST'>
	<center>
	<div style='width:800px;text-align:left;'>
	<h2>$title</h2>
	";
	echo $txt;
}

function install_html_footer(){
	$txt="
	</div>
	</center></form></body></html>
	";
	echo $txt;
}
function display_goto_next_tag($no,$opt=null){
	$curr_step=$no-1;
	if($opt=='disabled') $but_option=' disabled ';
	echo "<input type='button' name='go_button' $but_option value='Go to Step $no' 
		onclick='checkForm()'>";
}

function display_go_back_tag($no,$opt=null){
	$curr_step=$no+1;
	if($opt=='disabled') $but_option=' disabled ';
	if($no==0) $action="index.php";
	else $action="install$no.php";
	echo "<input type='button' name='go_back_button' $but_option value='Go back to Step $no' 
		onclick=\"
			document.form1.from_step.value='$curr_step';
			document.form1.action='$action';
			document.form1.submit();
			\">";
}

function display_license($mode=null){
	//$license_version=get_license_version(); 
	$license_txt=file_get_contents('data/license.txt');
	//echo "<h3>$license_version</h3>";
	echo "<br><div id='license' style='border-style:solid; border-width:1px;width:700px; height:300px; line-height: 1.5;  overflow: auto;'>";
	echo "<pre style=' white-space: pre-wrap;'>$license_txt</pre>";
	echo "</div><br>";	
	
	echo "<div id='license' style='border-style:solid; border-width:0px;width:700px;'>";
	echo "I have read and agreed the above license conditions.";
	echo "<input type='checkbox' name='license_box' value='agreed' onClick='toggle_continue(this);'><br>";
	echo "</div>";
}

// Check MySQL Database Requirements
function check_mysql_version($conn){
	$MYSQL_MIN_VERSION=get_version('mysql_min'); 
	$my_mysql_version=mysqli_get_server_info($conn);
	if($my_mysql_version<$MYSQL_MIN_VERSION) {
		$msg= "<font color=red> [Problem] You should have MySQL version $MYSQL_MIN_VERSION or higher.</font><br>";	
		$msg.=" Your MySQL version: $my_mysql_version()<br>";
		$msg.=" Please upgrade your MySQL first.";
		display_error($msg);
		return false;
	}
	else {
		display_ok_msg("Your MySQL version: $my_mysql_version");
	}
	if(innoDB_ok($conn)==false) {
		$msg= "<font color=red> [Problem] You should have InnoDB enabled in your MYSQL</font><br>";	
		$msg.=" Please upgrade your MySQL first.";
		display_error($msg);
		return false;
	}
	return true;
}

function get_version($item="version"){
	$path="version.json";
	if(file_exists($path)){
		$json=file_get_contents($path);
		$jobj=json_decode($json);
		if($item=="type") return($jobj->type);
		else if($item=="php_min") return($jobj->php_min);
		else if($item=="mysql_min") return($jobj->mysql_min);
		else return($jobj->version);
	}
	else return null;
}
function get_license_version(){
	$path="data/license.json";	
	if(file_exists($path)){
		$json=file_get_contents($path);
		$jobj=json_decode($json);
		return($jobj->version.", ".$jobj->date);
	}
	else return null;
}

function tb_prefix_available($conn=null,$db=null,$pre=null){
	$sql= "select TABLE_NAME as tb from information_schema.tables 
		where table_schema='$db' and TABLE_NAME like '".$pre."_%' limit 1";
	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	if($rs['tb']) return false;
	else return true;
}

function installed($path=null){
	global $conn,$VWMLDBM,$DTB_PRE;
	if($path) require_once($path);
	if($VWMLDBM['TB_prefix']) $TB_prefix=$VWMLDBM['TB_prefix']."_";
	else $TB_prefix=null;
	$sql= "select TABLE_NAME as tb from information_schema.tables 
		where table_schema='".$VWMLDBM['DB']."' and TABLE_NAME like '{$TB_prefix}vwmldbm_%' limit 1"; // [SJH] 2020/03/31 vwmldbm_ added

	if($conn)$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	if($rs['tb']) return true;
	else return false;
}

function innoDB_ok($conn){
	$sql= "SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'";
	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	if($rs['SUPPORT']=='NO') return false;
	else return true;	
}

function get_tb_list(&$arr,$inst_opt='NO_INST',$opt="REV_CREATE_ORDER",$opt2='NO_VIEW') {
	global $conn,$VWMLDBM,$DTB_PRE;	
	
	if($opt=='REV_CREATE_ORDER')
		$sql="select no,type,name,creating_order from {DTB_PRE}vwmldbm_tb where ISNULL(no)=false and type!='V' $no_view_txt order by creating_order desc";
	else 
		$sql="select no,type,name,creating_order from {DTB_PRE}vwmldbm_tb where ISNULL(no)=false $no_view_txt order by creating_order desc";
	if($opt2=='NO_VIEW') $no_view_txt=" and type!='V' ";

	$res=mysqli_query($conn,$sql);
	
	if($inst_opt=='NO_INST') { // tables without inst
		if($res) while($rs=mysqli_fetch_array($res)){
			if(inst_exist($rs['name'])) {
				$arr[$rs['no']] =$rs;
			}
		}		
	}
	else {
		if($res) while($rs=mysqli_fetch_array($res)){
			$arr[$rs['no']] =$rs;
		}		
	}
		
}

function inst_exist($tbName) { // called by get_tb_list()
	global $conn,$VWMLDBM;
	$sql="select * from {$VWMLDBM['DB']}.{$tbName} limit 1";

	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_assoc($res);
	if(isset($rs['inst'])) return true;
	else return false;
}

function del_data($inst,$tbArr,$opt=null) { // $tbArr should be the one ordered by create_order desc
	global $conn,$VWMLDBM;
	$cnt=0;
	foreach($tbArr as $no => $tb) {
		$cnt++;
		$sql0="select count(inst) as cnt from {$VWMLDBM['DB']}.{$tb['name']} where inst='$inst'";				
		$sql="delete from {$VWMLDBM['DB']}.{$tb['name']} where inst='$inst'";
	
		$res0=mysqli_query($conn,$sql0);
		if($res0)$rs0=mysqli_fetch_array($res0);
		if($rs0['cnt']<1) {
			echo "[$cnt] {$tb['name']} data do <font color=green>NOT</font> exist.<br>"; // there is no datum to delete
		}
		else { // there are data to delete	
			mysqli_query($conn,$sql);
			if(mysqli_affected_rows($conn)>0) {
				if($opt!="SILENT") {
					echo "<font color=magenta>[$cnt] {$tb['name']} data were successfully deleted!</font><br>";
				}
			}
			else if($opt!="SILENT") {
				echo "[$cnt] {$tb['name']} data were <font color=red>NOT</font> deleted!<br>"; 
				// echo $sql."<br>";
				$error=true;
			}
		}	
	}
	
}

?>