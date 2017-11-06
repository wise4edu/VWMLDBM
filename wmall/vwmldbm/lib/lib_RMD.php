<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
  
/*==============================================================
global variables:
	$constraints( declare again if not declared)
	
functions:		
	is_primary_key_field($db,$tb,$field)
	show_table($db=null,$tb=null,$table_id=null,$inst=null)
	show_DB_ERD($inst)
	show_DB_ERD_i($rs,$inst=null,$num_table_printed_so_far,$num_DB_printed_so_far)
	show_DBs()
	show_DB($rs)
	get_field_type($type_id)
	print_db_pos_js()
 ============================================================*/
namespace vwmldbm\RMD;

// if(isset($num_DB_printed_so_far)==false) 
	// $num_DB_printed_so_far=0; // for DB background color
// if(isset($num_table_printed_so_far)==false) 
	// $num_table_printed_so_far=0; // for table DIV ID name and more
if(isset($constraints)==false) 
	$constraints=array();  // for displaying/hiding fields
	
function is_primary_key_field($db,$tb,$field){  // called from show_table()
	global $conn,$DB,$DTB_PRE;
	$sql="select CONSTRAINT_SCHEMA from information_schema.key_column_usage
		where  CONSTRAINT_SCHEMA='$db' and table_name='$tb' and COLUMN_NAME='$field' and CONSTRAINT_NAME='PRIMARY'";
	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	if($rs[CONSTRAINT_SCHEMA]) return true;
	else return false;	
}

function show_table($db=null,$tb=null,$table_id=null,$inst=null){  // show the specified table of the DB
	global $conn,$DB,$DTB_PRE;
	global $constraints;	
	if(!$inst) $inst=$_SESSION[inst];
	\vwmldbm\sysmon\update_fkey_info($db,$tb,$inst); // lib_sysmon.php
	if($DB.".".$tb=="$DTB_PRE"."_vwmldbm_inst") $tfd="no";
	else $tfd="inst";

	$res = mysqli_query($conn,"SELECT * FROM $DB.$tb"); 
	$fields= mysqli_num_fields($res);		
	$sql="select t.no from $DTB_PRE"."_vwmldbm_tb t, $DTB_PRE"."_vwmldbm_db d where t.inst='$inst' and t.db=d.no and t.name='$tb' and d.name='$db'";
	$res_ss=mysqli_query($conn,$sql);
	if($res_ss) $rs_ss=mysqli_fetch_array($res_ss);
	
	$sql="select expanded_yn from $DTB_PRE"."_vwmldbm_rmd_tb_loc where inst='$inst' and tb_no='$rs_ss[no]'";
			
	$res_RMD=mysqli_query($conn,$sql);
	if($res_RMD) $rs_ERD=mysqli_fetch_array($res_RMD);
	$k=0;
	for ($i=0; $i < $fields; $i++) {
		$finfo = mysqli_fetch_field_direct($res,$i);
		
		$type  = get_field_type($finfo->type);
		$name  = $finfo->name;
		if(strlen($name>15))$name= substr($name,0,13)."..";
		$len   = $finfo->max_length;
		$flags = $finfo->flags;
		if(is_primary_key_field($db,$tb,$name)) { // field is primary key field
			$field_id='d'.$db.'.'.$tb.'['.$i.']';
			$to_table=$constraints[$db][$tb][$name][toDB].".".$constraints[$db][$tb][$name][toTable];
			echo "<div id='$field_id' style='background-color:yellow;display:inline-block;'>
					<span class='tcell' style='width:105px;max-width:105px;' onClick=\"show_mult_lang('$field_id','$name','$inst')\" >$name</span><span class='tcell'>$type</span><span class='tcell'>$len</span></div><br>";
			if($constraints[$db][$tb][$name][toField]){				
				echo "<script>fkey_info[fkidx]=new fkey('$db','$table_id','$field_id','$to_table',$k,$i);</script>";
				echo"<script>fkidx++;</script>";
			}			
			$k++;
		}
		else if($constraints[$db][$tb][$name][toField]){ // field is a foreign key 
			$field_id='d'.$db.'.'.$tb.'['.$i.']';
			
			$to_table=$constraints[$db][$tb][$name][toDB].".".$constraints[$db][$tb][$name][toTable];
						
			echo "<div id='$field_id'>
					<span class='tcell' style='width:105px;max-width:105px' onClick=\"show_mult_lang('$field_id','$name','$inst')\" ><font color=red>$name</font></span><span class='tcell'>$type</span><span class='tcell'>$len</span></div>";
			echo "<script>fkey_info[fkidx]=new fkey('$db','$table_id','$field_id','$to_table',$k,$i);</script>";
			echo"<script>fkidx++;</script>";
			$k++;
		}
		else { // field is not related to neigther primary key nor foreign key 
			$field_id='hd'.$db.'.'.$tb.'['.$i.']';
			
			if(trim($rs_ERD[expanded_yn])=="-") $tdisplay='inline-block'; else $tdisplay='none';
			echo "<div id='$field_id' style='display:$tdisplay;'>
					<span class='tcell' style='width:105px;max-width:105px;display:inline-block;' onClick=\"show_mult_lang('$field_id','$name','$inst')\" >$name</span><span class='tcell' display:inline-block;>$type</span><span class='tcell'>$len</span></div>";
		}
		
	}
	if(trim($rs_ERD[expanded_yn])=="+" || trim($rs_ERD[expanded_yn])=="") echo "
		<script>  // get the shrunk y size of each table
			var tmp_no=".\vwmldbm\sysmon\get_tree_no($db,$tb,$inst)."
			table_shrunk_y_size_by_table_no[tmp_no]=$k;	

			document.getElementById('$table_id').style.height=($k*21 +19);
		</script>
	";
	else echo "
		<script>  // get the shrunk y size of each table
			var tmp_no=".\vwmldbm\sysmon\get_tree_no($db,$tb,$inst)."
			table_shrunk_y_size_by_table_no[tmp_no]=$k;	
			document.getElementById('$table_id').style.height=($fields*21 +19)
		</script>
	";
}

function show_DB_ERD($inst){
	global $conn,$DB,$DTB_PRE;
	$num_DB_printed_so_far=0;
	$num_table_printed_so_far=0;
	$sql="select * from $DTB_PRE"."_vwmldbm_db where inst=$inst";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)) {
		$num_table_printed_so_far=show_DB_ERD_i($rs,$inst,$num_table_printed_so_far,$num_DB_printed_so_far);
	}
	return $num_table_printed_so_far;
}

function show_DB_ERD_i($rs,$inst=null,$num_table_printed_so_far,$num_DB_printed_so_far){ // Show ERD. This function gets each DB and prints all the tables
	global $conn,$DB,$DTB_PRE;
	//global $num_table_printed_so_far;
	
	if($inst==1) {
		if($_SESSION['inst']!=1) return; // inst=1 is super inst,so access should be protected
	}
	elseif($inst>1 && $inst!=$_SESSION['inst'] && $_SESSION['inst']!=1) return; // inst=1 can access other inst
	elseif(!$inst) $inst=$_SESSION['inst']; 
	
	$num_table_local=0;
	$X_SPACE_BETWEEN_TABLES=240;
	if($num_DB_printed_so_far%10==0) $bcolor="faacfa";
	else if($num_DB_printed_so_far%10==1) $bcolor="33fa33";
	else if($num_DB_printed_so_far%10==2) $bcolor="acacfa";
	else if($num_DB_printed_so_far%10==3) $bcolor="33fafa";
	else if($num_DB_printed_so_far%10==4) $bcolor="acfaac";
	else if($num_DB_printed_so_far%10==5) $bcolor="ccffcc";
	else if($num_DB_printed_so_far%10==6) $bcolor="ccccff";
	else if($num_DB_printed_so_far%10==7) $bcolor="ccffff";
	else if($num_DB_printed_so_far%10==8) $bcolor="ffccff";
	else if($num_DB_printed_so_far%10==9) $bcolor="ffa500";
	else $bcolor="ffcccc";
	echo"<script>DB_color['$rs[name]']='$bcolor';</script>";

// find the table prefix	
	$cur_tb_prefix=""; // table prefix for chaning color

	$num_DB_printed_so_far=0;
	$sql="select * from $DTB_PRE"."_vwmldbm_tb where inst='$inst' and DB='$rs[no]'";
	$res2=mysqli_query($conn,$sql);
	
	if($res2) while($rs2=mysqli_fetch_array($res2)){ // print each table's div
		preg_match_all("@_([\w][^_]*)_@",$rs2[name],$out,PREG_PATTERN_ORDER);$wise_rt=$out[1][0]; // wise2 root should be placed into the server root
		if($cur_tb_prefix!=$out[1][0]) {
			$cur_tb_prefix=$out[1][0];
			$num_DB_printed_so_far++;
			if($num_DB_printed_so_far%10==0) $bcolor="faacfa";
			else if($num_DB_printed_so_far%10==1) $bcolor="33fa33";
			else if($num_DB_printed_so_far%10==2) $bcolor="acacfa";
			else if($num_DB_printed_so_far%10==3) $bcolor="33fafa";
			else if($num_DB_printed_so_far%10==4) $bcolor="acfaac";
			else if($num_DB_printed_so_far%10==5) $bcolor="ccffcc";
			else if($num_DB_printed_so_far%10==6) $bcolor="ccccff";
			else if($num_DB_printed_so_far%10==7) $bcolor="ccffff";
			else if($num_DB_printed_so_far%10==8) $bcolor="ffccff";
			else if($num_DB_printed_so_far%10==9) $bcolor="ffa500";
			else $bcolor="ffcccc";
		}
	
		// following four hidden fields are to save the location of the each table
		echo "<div style='display:none;'><input id='table_x_pos[$num_table_printed_so_far]' type=hidden name='table_x_pos[]'></div>";
		echo "<div style='display:none;'><input id='table_y_pos[$num_table_printed_so_far]' type=hidden name='table_y_pos[]'></div>";
		echo "<div style='display:none;'><input id='table_no[$num_table_printed_so_far]' name='table_no[]' type=hidden value='$rs2[no]'></div>";
		echo "<div style='display:none;'><input id='table_expanded[$num_table_printed_so_far]' type=hidden name='table_expanded[]'></div>";
		$sql="select zindex from $DTB_PRE"."_vwmldbm_rmd_tb_loc where inst='$inst' and tb_no='$rs2[no]'";
		$res_z=mysqli_query($conn,$sql); if($res_z) $rs_z=mysqli_fetch_array($res_z);
		echo "<div style='display:none;'><input id='table_zindex[$num_table_printed_so_far]' type=hidden name='table_zindex[]' value='$rs_z[zindex]'></div>";

// It should be improved such that the table, wise_inst's field name should be changed from no to inst			
$trs2name=$rs2[name];
if(substr($trs2name,-9)=='vwmldbm_inst') $sql="select * from $rs[name].$rs2[name] where no='$inst' ";
		else {
			$sql="select * from $rs[name].$rs2[name] ";
		}

		$res3=mysqli_query($conn,$sql);
		if($res3){
			$num_record=mysqli_num_rows($res3);
			$num_fields=mysqli_num_fields($res3);
		}
		$sql="select * from $DTB_PRE"."_vwmldbm_rmd_tb_loc where inst='$inst' and tb_no='$rs2[no]'";
		$res_RMD=mysqli_query($conn,$sql);
		if($res_RMD) $rs_ERD=mysqli_fetch_array($res_RMD);
		
		$id='tb['.$num_table_printed_so_far.']';
		
		if($rs_ERD[x_pos]){ 
			$x_pos=$rs_ERD[x_pos]; 
		}
		else $x_pos=$num_DB_printed_so_far*$X_SPACE_BETWEEN_TABLES -220; // 
		
		if($rs_ERD[x_pos]) $y_pos=$rs_ERD[y_pos]; 
		else $y_pos=40+$num_table_local*80;

	// views are initialled not displayed  [new]
		//if($rs2['type']=='V') $display_tag="display:none;";
		
	// mother DIV which contains a table	
		echo "<div id='$id' class='draggable' style='z-index:$rs_ERD[zindex]; position:absolute;left:$x_pos; top:$y_pos;padding: 0.4em;border: 2px solid #fff;background-color:#".$bcolor.";$display_tag'
				onMouseUp=\"document.getElementById('table_x_pos[$num_table_printed_so_far]').value=this.style.left;document.getElementById('table_y_pos[$num_table_printed_so_far]').value=this.style.top;\"
			>";
	
	// table expansion sign
		if(strlen($rs2[name])>22) $tname=substr($rs2[name],0,20)."..";
		else $tname=$rs2[name];
		if($rs_ERD[expanded_yn]=='') $tmp_expanded_yn='+'; else $tmp_expanded_yn=$rs_ERD[expanded_yn];
		echo "<b>".$tname."(".$num_fields.",".$num_record.")</b> <span id='sign_$rs2[no]' style='float:right; width:20px;height:20px;' onClick=\"show_hide_hidden_fields('$rs[name]','$rs2[name]','$id',$num_fields,this.id,$num_table_printed_so_far);\">$tmp_expanded_yn</span><br>";
		echo "<script>document.getElementById('table_expanded[$num_table_printed_so_far]').value='$tmp_expanded_yn'; </script>";         
		
		show_table($rs[name],$rs2[name],$id);
		$tDbTb=$rs[name].'.'.$rs2[name];
		echo "<script>myDT_to_TbID.push(new DT_to_TbID('$tDbTb','$id'));</script>";
		
		if($rs_ERD[x_pos]){ 
			echo"<script>
				document.getElementById('table_x_pos[$num_table_printed_so_far]').value=document.getElementById('$id').style.left;
				document.getElementById('table_y_pos[$num_table_printed_so_far]').value=document.getElementById('$id').style.top;
				</script>";
		}

		echo "</div>";
		$num_table_printed_so_far++;
		$num_table_local++;
	}
	echo"<br>";
	return $num_table_printed_so_far;
}

function show_DBs(){
	global $conn,$DB,$DTB_PRE;
	$sql="select * from $DTB_PRE"."_vwmldbm_db";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)) {
		show_DB($rs);
	}
}

function show_DB($rs){ // print the DB names on the top
	global $num_DB_printed_so_far;
	if($num_DB_printed_so_far%10==0) $bcolor="faacfa";
	else if($num_DB_printed_so_far%10==1) $bcolor="33fa33";
	else if($num_DB_printed_so_far%10==2) $bcolor="acacfa";
	else if($num_DB_printed_so_far%10==3) $bcolor="33fafa";
	else if($num_DB_printed_so_far%10==4) $bcolor="acacac";
	else if($num_DB_printed_so_far%10==5) $bcolor="ccffcc";
	else if($num_DB_printed_so_far%10==6) $bcolor="ccccff";
	else if($num_DB_printed_so_far%10==7) $bcolor="ccffff";
	else if($num_DB_printed_so_far%10==8) $bcolor="ffccff";
	else if($num_DB_printed_so_far%10==9) $bcolor="ffffcc";
	else if($num_DB_printed_so_far%10==6) $bcolor="acfaac";
	else $bcolor="ffcccc";
	
	echo "<div id='DB[".$rs[no]."]' style='padding: 0.2em;background-color:fff;position:relative;display:inline-block;'>";
	echo "<span style='background-color:#".$bcolor.";position:relative;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
	echo ":".$rs[name];	
	echo "</div>";
	$num_DB_printed_so_far++;
}

function get_field_type($type_id) {
	static $types;
	if (!isset($types))
	{
		$types = array();
		$constants = get_defined_constants(true);
		foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
	}

	return array_key_exists($type_id, $types)? $types[$type_id] : NULL;
}

function print_db_pos_js(){
// get the no of DB
	global $conn,$DB,$DTB_PRE;
	$res_n=mysqli_query($conn,"select no from $DTB_PRE"."_vwmldbm_db");
	if($res_n){
		while($rs_n=mysqli_fetch_array($res_n))
			echo "db_nos.push($rs_n[no]);";
	}
}
?>