<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
  
/*==============================================================
global variables:
	$wise_table ( declare again if not declared in RMD.php)
class:
	Table_node
		functions:
			static function get_new_node($no,$inst=null)
functions:		
	print_tables($db_name=null)
	investigate_table($db_no=null,$db_name=null,$inst=null)
	investigate_all_tables($option=null,$inst=null)
	delete_unnecessary_tb($db_no,$db_name,$option=null)
	insert_fields($db_name,$tb_name,$option=null,$inst=null)
	delete_fields($db_name,$tb_name,$option=null,$inst=null)
	type_of_table($db,$tb_pre,$tb_name)
	
	get_tree_no($db_name,$tb_name,$inst=null)
	get_db_no($db_name)
	get_tb_name($tb_no,$inst=null)
	update_node($from_tb,$to_tb)
	find_node($tb_no)
	df_traversal($node,$parent)
	rev_df_traversal($node,$parent)
	update_tables($inst=null)
	update_fkey_info($db=null,$tb=null,$inst=null)
	update_all_fkey_info($db,$inst=null)
	tb_list($db,$pre)
	view_list($db,$pre)
	add_lang_to_new_inst($inst_no) // add translations to new institution(s) if there is any
	check_DB_same_as_before()
}
 ============================================================*/
namespace vwmldbm\sysmon;

if(isset($wise_table)==false) // $wise_table should be a global variable
	$wise_table=array(); // array contains all the tree nodes
	
function print_tables($db_name=null){  // print tables of the DB
	$inst=$_SESSION['vwmldbm_inst'];
	if(!$inst) return;
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if($db_name==null) $db_name=$DB;
	$count=0;
	$sql="select name,type,creating_order,sql_txt from {$DTB_PRE}vwmldbm_tb where db='$db_name' order by creating_order asc";
	$res=mysqli_query($conn,$sql);
	echo "<table border=1 width=100%><tr><td align=center bgcolor=bbffbb>Name</td>
			<td align=center bgcolor=bbffbb>Type</td>
			<td align=center bgcolor=bbffbb>Creating Order</td>
			<td align=center bgcolor=bbffbb>SQL</td></tr>";
	if($res) while($rs=mysqli_fetch_array($res)){
		$count++;
		echo "<tr>";
		echo "	<td  width=120>[$count] ".$rs['name']."</td>";
		if($rs['type']=='T') $tb_type='Table';
		if($rs['type']=='C') $tb_type='Table(Code)';
		elseif($rs['type']=='V') $tb_type='VIEW';
		
		echo "	<td  width=100 align=center>$tb_type</td>";
		echo "	<td  width=100 align=center>".$rs['creating_order']."</td>";
		echo "	<td  width=300><textarea rows=2 cols=34>".$rs['sql_txt']."</textarea></td>";
		echo "</tr>";
	}
	echo "</table>";
}

function investigate_table($db_no=null,$db_name=null,$inst=null){ 
// called by investigate_all_tables; get the list of the tables of the DB and update the info into wise2.wise2_vwmldbm_tb
// also insert the fields of the table into wise2.wise2_vwmldbm_fd
	
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	global $conn,$DB,$DTB_PRE,$TB_PRE,$VWMLDBM;
// SJH_MOD 
	if($TB_PRE=="" || $TB_PRE==null) $sql="select table_name from information_schema.tables where table_schema='$DB' and table_name like '%'";
	else $sql="select table_name from information_schema.tables where table_schema='$DB' and table_name like '$TB_PRE"."\_%'";
	
	$res=mysqli_query($conn,$sql);
	if($res) {
		while($rs=mysqli_fetch_array($res)){		
		  // check if the table is already in the {$DTB_PRE}vwmldbm_tb, then the table can be skipped.
			$sql="select no from {$DTB_PRE}vwmldbm_tb where DB='$db_no' and name = '{$rs[0]}'";
			$res_check=mysqli_query($conn,$sql);
			if($res_check) {
				$rs_check=mysqli_fetch_array($res_check);
				insert_fields($db_name,$rs[0],'NO_SILENT',$inst);  // check and insert all the fields of a new table or any newly added field of the table.
				delete_fields($db_name,$rs[0],'NO_SILENT',$inst);  // check and delete unnecessary fields of a table.
			}
		  // end of check  

			$tb_type=type_of_table($DB,$TB_PRE,$rs[0]); // type of the table
			
		  // update or newly insert sql_txt
			$sql="SHOW CREATE TABLE $db_name.$rs[0]"; 	// table creation SQL		
			$res1=mysqli_query($conn,$sql);
			if($res1) $rs1=mysqli_fetch_array($res1);
			
			if($rs_check['no']!='') // update sql_txt
				$sql="update {$DTB_PRE}vwmldbm_tb set sql_txt=\"{$rs1[1]}\", type='$tb_type' where DB='$db_no' and name='{$rs[0]}'";	
			else   // newly insert
				$sql="insert into {$DTB_PRE}vwmldbm_tb (DB,name,type,sql_txt) values($db_no,\"{$rs[0]}\",'$tb_type',\"{$rs1[1]}\")";	
			mysqli_query($conn,$sql);
		}
	}	
}

function investigate_all_tables($option=null,$inst=null){ // $option='SILENT': no echo
// check if each table of the database exist in the wise2.wise2_vwmldbm_tb. If not, add the missing tables
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if($inst==1) {
		if($_SESSION['vwmldbm_inst']!=1) return; // inst=1 is super inst,so access should be protected
	}
	elseif($inst>1 && $inst!=$_SESSION['vwmldbm_inst'] && $_SESSION['vwmldbm_inst']!=1) return; // inst=1 can access other inst
	elseif(!$inst) $inst=$_SESSION['vwmldbm_inst']; 
	$res=mysqli_query($conn,"select distinct no,name from {$DTB_PRE}vwmldbm_db");

	if($res) while($rs=mysqli_fetch_array($res)){
		investigate_table($rs['no'],$rs['name'],$inst);
	}
// check if any unnecessary table is remaining in wise2.wise2_vwmldbm_tb.	(Table was deleted from the DB)
// 1. count the number of tables from each DB of wise2.wise2_vwmldbm_db and compare the number of tables in DB with the number of tables in wise2.wise2_vwmldbm_tb
// 2. if the number is different delete the unnecessary tables from wise2.wise2_vwmldbm_tb
	$num_tb=array();
	$res=mysqli_query($conn,"select * from {$DTB_PRE}vwmldbm_db");
	if($res) {
		if($option!='SILENT') echo "<br>DB, # tables in DB, # table in {$DTB_PRE}vwmldbm_tb<br>";
		while($rs=mysqli_fetch_array($res)){
			$num_tb[$rs['no']]=0; //initialize with zero
			if($option!='SILENT') echo $rs['name'].", "; // DB name
		// SJH_MOD 
			if($TB_PRE=="" || $TB_PRE==null) $res_a=mysqli_query($conn,"select table_name from information_schema.tables where table_schema='".$rs['name']."' and table_name like '%'");
			else $res_a=mysqli_query($conn,"select table_name from information_schema.tables where table_schema='".$rs['name']."' and table_name like '$TB_PRE"."\_%'");
			if($res_a) $num_tb[$rs['no']]=mysqli_num_rows($res_a);
			if($option!='SILENT') echo $num_tb[$rs['no']].", ";
			
			$res_b=mysqli_query($conn,"select no from {$DTB_PRE}vwmldbm_tb where db='".$rs['no']."'");
			if($option!='SILENT' && $res_b) echo mysqli_num_rows($res_b)." ";
			if($res_b && (mysqli_num_rows($res_b) !=$num_tb[$rs['no']])) { //#2 
				//echo "<font color=red>different!</font>";
				delete_unnecessary_tb($rs['no'],$rs['name'],'SILENT'); 
			}
			if($option!='SILENT') echo "<br>";
		} 
	}
}

function delete_unnecessary_tb($db_no,$db_name,$option=null){ // called by investigate_all_tables(), delete unnecessary tables and their fields
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	$inst=($_SESSION['vwmldbm_inst']?$_SESSION['vwmldbm_inst']:1);
	$res=mysqli_query($conn,"select name from {$DTB_PRE}vwmldbm_tb where db='$db_no'");
	if($res) while($rs=mysqli_fetch_array($res)){
		$sql="select count(table_name) as num from information_schema.tables where table_schema='$db_name' and table_name='".$rs['name']."';";
	
		$res_a=mysqli_query($conn,$sql);
		if($res_a) {
			$rs_a=mysqli_fetch_array($res_a);
			if($rs_a['num']<1) {
				mysqli_query($conn,"delete from {$DTB_PRE}vwmldbm_tb where db='$db_no' and name='".$rs['name']."'");
				if($option!='SILENT') echo " '<font color=red>".$rs['name']."' was deleted!</font>";
			}
		}
	}
}

function insert_fields($db_name,$tb_name,$option=null,$inst=null) { // called by investigate_table()
	// insert the fields of the table into wise2.wise2_vwmldbm_fd
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	$sql="select COLUMN_NAME, DATA_TYPE,CHARACTER_MAXIMUM_LENGTH from information_schema.columns where table_schema='$db_name' and table_name='$tb_name'";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		$res_a=mysqli_query($conn,"select count(no) as num from {$DTB_PRE}vwmldbm_fd where db_name='$db_name' and tb_name='$tb_name' and field='".$rs['COLUMN_NAME']."'");
		if($res_a) $rs_a=mysqli_fetch_array($res_a);
		if($rs['CHARACTER_MAXIMUM_LENGTH']=='') $rs['CHARACTER_MAXIMUM_LENGTH']=0;
		if($rs_a['num']==0) {
			mysqli_query($conn,"insert into {$DTB_PRE}vwmldbm_fd (db_name,tb_name,field,type,max_len) values('$db_name','$tb_name','".$rs['COLUMN_NAME']."','".$rs['DATA_TYPE']."',".$rs['CHARACTER_MAXIMUM_LENGTH'].")");	
			if($option!='SILENT')echo"$db_name.$tb_name.".$rs['COLUMN_NAME']." was added.<br>";
		}
	}
}

function delete_fields($db_name,$tb_name,$option=null,$inst=null) { // called by investigate_table()
	// check and delete unnecessary fields of a table.
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	$sql="select field from {$DTB_PRE}vwmldbm_fd where db_name='$db_name' and tb_name='$tb_name'";

	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){ // get a field from wise2.wise2_vwmldbm_fd
		$res_a=mysqli_query($conn,"select count(COLUMN_NAME) as num from information_schema.columns where table_schema='$db_name' and table_name='$tb_name' and COLUMN_NAME='".$rs['field']."'");
		if($res_a) $rs_a=mysqli_fetch_array($res_a);
		if($rs_a['num']<1) { // unnecessary field was found.
			mysqli_query($conn,"delete from {$DTB_PRE}vwmldbm_fd where db_name='$db_name' and tb_name='$tb_name' and field='".$rs['field']."'");
			if($option!='SILENT')echo"<font color=red>$db_name.$tb_name.".$rs['field']." was deleted.</font><br>";
		}
	}	
}

function type_of_table($db,$tb_pre,$tb_name){ // determine whether it is table or view
	global $conn;
	$sql="select table_type from information_schema.tables where table_schema='$db' and table_name='$tb_name'";
	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	if($rs['table_type']=="VIEW") return 'V';
	else {	// table
		if(strtolower(substr($tb_name,strlen($tb_pre),6))=="_code_") return 'C'; // code table
		else return 'T'; // normal table
	}
}

///////////////////////// Table Tree
class Table_node {
	public $inst; 
	public $db_no;
	public $tb_no; 
	public $name; 
	public $type; 	
	public $level; // root is level 0
	public $order_of_creation; // the order in which all the tables can be created
	public $is_root; // true if the node is a root
	public $is_terminal; // true if the node is terminal
	public $upLink; // uplinks to upper tree nodes(parents)
	public $downLink; // downlinks to lower tree nodes(children)
	
	public function __construct($t,$d,$tp=null,$inst=null,$n=null){  
		if(!$inst) $this->inst=$_SESSION['vwmldbm_inst'];
		else $this->inst=$inst;
		$this->inst=$inst;
		$this->tb_no=$t;
		$this->db_no=$d;
		$this->name=$n;
		$this->type=$tp;
		$this->level=0; 
		$this->order_of_creation=0; 
		$this->is_root=true; 
		$this->is_terminal=true; 
		$this->upLink=array();
		$this->downLink=array(); 
	}
	
	public static function get_new_node($no,$inst=null){
		global $conn,$DB,$DTB_PRE,$TB_PRE;
		if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
		//Original(before vwmldbm): $sql="select t.no, t.db,t.type,t.name as tname from {$DTB_PRE}vwmldbm_tb t, {$DTB_PRE}vwmldbm_db d where t.inst='$inst' and t.db=1 and t.no='$no'";
		$sql="select t.no, t.db,t.type,t.name as tname from {$DTB_PRE}vwmldbm_tb t, {$DTB_PRE}vwmldbm_db d where t.db=d.no and t.no='$no'";
		$res=mysqli_query($conn,$sql);
		if($res) $rs=mysqli_fetch_array($res);
		if($rs) {
			$aNode=new Table_node($rs['no'],$rs['db'],$rs['type'],$inst,$rs['tname']);
			return $aNode;
		}
		//else echo "Skipping table #$no<br>";
	}
}

function get_tree_no($db_name,$tb_name,$inst=null){
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	$db_no=get_db_no($db_name);
	$res=mysqli_query($conn,"select no from {$DTB_PRE}vwmldbm_tb where name='$tb_name' and DB='$db_no'");
	if($res) {
		$rs=mysqli_fetch_array($res);
		return ($rs['no']);
	}
}

function get_db_no($db_name){  // called from get_tree_no()
	global $conn,$DB,$DTB_PRE,$TB_PRE;

	$inst=1; //TBM
	$res=mysqli_query($conn,"select no from {$DTB_PRE}vwmldbm_db where name='$db_name'");
	if($res) {
		$rs=mysqli_fetch_array($res);
		return ($rs['no']);
	}
}
function get_tb_name($tb_no,$inst=null){  // called from update_tables()
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	$res=mysqli_query($conn,"select name from {$DTB_PRE}vwmldbm_tb where no='$tb_no'");
	if($res) {
		$rs=mysqli_fetch_array($res);
		return ($rs['name']);
	}
}
function update_node($from_tb,$to_tb){	
	$from_node=find_node($from_tb);
	$from_node->downLink[]=$to_tb;
			
	$to_node=find_node($to_tb);
	if($to_node) $to_node->upLink[]=$from_tb;
}

function find_node($tb_no){ // return the index of the table; called by update_node(),df_traversal()
	//global $conn;
	global $wise_table;
	for($i=0;$i<count($wise_table);$i++){
		if($wise_table[$i]->tb_no==$tb_no) return $wise_table[$i]; // found
	}
	return null; // not found	
}

function df_traversal($node,$parent){ //depth first tree traversal
	if(!$node) return;	
	if($node == $parent) return;
	
	if($node->level <= $parent->level) $node->level=$parent->level+1;
	$node->is_root=false;
	if(isset($node->downLink)) foreach($node->downLink as $child) {
		df_traversal(find_node($child),$node);
	}
	$node->visited=true;
}

function update_tables($inst=null) {
///////////////////////// Table Tree Operation	
// create the nodes of all the tables
	global $conn,$DB,$DTB_PRE,$TB_PRE,$wise_table,$VWMLDBM;
	if($inst==1) {
		if($_SESSION['vwmldbm_inst']!=1) return; // inst=1 is super inst,so access should be protected
	}
	elseif($inst>1 && $inst!=$_SESSION['vwmldbm_inst'] && $_SESSION['vwmldbm_inst']!=1) return; // inst=1 can access other inst
	elseif(!$inst) $inst=$_SESSION['vwmldbm_inst']; 

// SJH_MOD 
	if($TB_PRE=="" || $TB_PRE==null) $sql="select no from {$DTB_PRE}vwmldbm_tb where name like '%'";
	else $sql="select no from {$DTB_PRE}vwmldbm_tb where name like '$TB_PRE"."\_%'";

	$res_tables=mysqli_query($conn,$sql);
 
	if($res_tables) {
		while($rs_tables=mysqli_fetch_array($res_tables)) $wise_table[]=Table_node::get_new_node($rs_tables['no'],$inst);
	}
	$num_tables=0;
	for($i=0;$i<count($wise_table);$i++){
		if($wise_table[$i]->tb_no=="") continue; // skip unnecessary tables
		$num_tables++;
	}
	echo "The number of tables:  <b>$num_tables</b> <br>";
	
	if($num_tables<1) return;

// update upLinks and downlinks
	$res_f=mysqli_query($conn,"select * from {$DTB_PRE}vwmldbm_rmd_fkey_info ");
	
	if($res_f) {
		if(mysqli_num_rows($res_f)<1) return; // there is no data in wise_rmd_feky_info
		while($rs_f=mysqli_fetch_array($res_f)) {
			$from_tb_no=get_tree_no($rs_f['from_db'],$rs_f['from_tb'],$inst);
			$to_tb_no=get_tree_no($rs_f['to_db'],$rs_f['to_tb'],$inst);
			//echo $from_tb_no ." => ".$to_tb_no."<br>";
			update_node($from_tb_no,$to_tb_no);
		}
	}

	
// root/terminal node unmarking	
	for($i=0;$i<count($wise_table);$i++){
		if(count($wise_table[$i]->upLink))
			$wise_table[$i]->is_root=false;
		if(count($wise_table[$i]->downLink)){
			$wise_table[$i]->is_terminal=false;
		}
	}
	
	
// Find each root node and do the df_traversal.

	for($i=0;$i<count($wise_table);$i++){
		if($wise_table[$i]->is_root) { // root node found
			if($wise_table[$i]->is_terminal) {
				//echo "#".$wise_table[$i]->tb_no." is an isolated node.<br>";
			
			}
			else {
				foreach($wise_table[$i]->downLink as $child) 
					df_traversal(find_node($child),$wise_table[$i]);
			}
		}
	}
	

// Update the orders of creation into wise2_vwmldbm_tb
	$how_many_in_each_level=array();
	for($i=0;$i<count($wise_table);$i++){
		$how_many_in_each_level[$wise_table[$i]->level]++;	
	}
	$cnt=0;
	for($i=0;$i<count($wise_table);$i++){ // update the isolated nodes
		if($wise_table[$i]->tb_no=="") continue; // skip unnecessary tables
		if(type_of_table($DB,$TB_PRE,get_tb_name($wise_table[$i]->tb_no),$inst)=='V') continue; // skip VIEW
		if($wise_table[$i]->is_root==true && $wise_table[$i]->is_terminal==true) $wise_table[$i]->order_of_creation=++$cnt;
	}
	for($i=count($how_many_in_each_level)-1;$i>=0;$i--){ // update all other nodes
		for($j=0;$j<count($wise_table);$j++){
			if($wise_table[$j]->tb_no=="") continue; // skip unnecessary tables
			if(type_of_table($DB,$TB_PRE,get_tb_name($wise_table[$i]->tb_no,$inst))=='V') continue; // skip VIEW
			if($wise_table[$j]->is_root==true && $wise_table[$j]->is_terminal==true) continue; // these isolated nodes were counted already before.
			if($wise_table[$j]->level==$i){
				$wise_table[$j]->order_of_creation=++$cnt;			
			}
		}
	}
 // update the order of creation info into wise2.wise2_system.wise_table	
	for($i=0;$i<count($wise_table);$i++){  // Tables (not views)
		if($wise_table[$i]->tb_no=="") continue; // skip unnecessary tables
		$tb_name=get_tb_name($wise_table[$i]->tb_no,$inst);
		if(type_of_table($DB,$TB_PRE,$tb_name)=='V') continue; // skip VIEW
			
		mysqli_query($conn,"update {$DTB_PRE}vwmldbm_tb set creating_order='".$wise_table[$i]->order_of_creation."' where name='$tb_name'");
		//echo $wise_table[$i]->tb_no.": ".$wise_table[$i]->order_of_creation."<br>";	
	}

// Determine the creating order of views from installation file
	if(file_exists($VWMLDBM['wise_rt']."/install/sql/view.php")){		
		$sql_view=array();
		require_once($VWMLDBM['wise_rt']."/install/sql/view.php");	
	}
	
// Remark: during the init_inst, view.php didn't become the part of the array.
// But second time is okay. So for now let it go.
//echo "COUNT SQL_VIEW=".count($sql_view);

	for($i=0;$i<count($wise_table);$i++){  // Views (not tables)
		if($wise_table[$i]->tb_no=="") continue; // skip unnecessary tables
		$tb_name=get_tb_name($wise_table[$i]->tb_no,$inst);
		if(type_of_table($DB,$TB_PRE,$tb_name)!='V') continue; // skip Tables (Not views)
		$view_name=substr($tb_name,strlen($TB_PRE)+1);
		if($sql_view) $tb_c_order=array_search($view_name,array_keys($sql_view))+$cnt+1;
		
		mysqli_query($conn,"update {$DTB_PRE}vwmldbm_tb set creating_order='$tb_c_order' where name='$tb_name'");	
	}
}

function update_fkey_info($db=null,$tb=null,$inst=null){ 
	if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
	global $conn,$DB,$DTB_PRE;
	global $constraints;	
	if($DB.".".$tb=="{$DTB_PRE}vwmldbm_inst") $tfd="no";
	else $tfd="inst";
	mysqli_query($conn,"delete from {$DTB_PRE}vwmldbm_rmd_fkey_info where from_db='$db' and from_tb='$tb'");
			
	if($DB.".".$tb=="{$DTB_PRE}vwmldbm_inst") $tfd="no";
	else $tfd="inst";
	
	$sql="select CONSTRAINT_SCHEMA as fromDB, TABLE_NAME as fromTable,COLUMN_NAME as fromField,
		REFERENCED_TABLE_SCHEMA as toDB, REFERENCED_TABLE_NAME as toTable, REFERENCED_COLUMN_NAME as toField 
		from information_schema.key_column_usage
		where  CONSTRAINT_SCHEMA='$db' and table_name='$tb' and CONSTRAINT_NAME<>'PRIMARY'";

	$res_f=mysqli_query($conn,$sql);
	if($res_f) while($rs_f=mysqli_fetch_array($res_f)){
		$constraints[$rs_f['fromDB']][$rs_f['fromTable']][$rs_f['fromField']]['toDB']=$rs_f['toDB'];
		$constraints[$rs_f['fromDB']][$rs_f['fromTable']][$rs_f['fromField']]['toTable']=$rs_f['toTable'];
		$constraints[$rs_f['fromDB']][$rs_f['fromTable']][$rs_f['fromField']]['toField']=$rs_f['toField'];

		$sql="insert into {$DTB_PRE}vwmldbm_rmd_fkey_info (from_db,to_db,from_tb,to_tb,from_field,to_field) values('".$rs_f['fromDB']."','".$rs_f['toDB']."','".$rs_f['fromTable']."','".$rs_f['toTable']."','".$rs_f['fromField']."','".$rs_f['toField']."')";
		mysqli_query($conn,$sql);
	} 
}

function update_all_fkey_info($db,$inst=null){
	global $conn,$TB_PRE;
	
	if($inst==1) {
		if($_SESSION['vwmldbm_inst']!=1) return; // inst=1 is super inst,so access should be protected
	}
	elseif($inst>0 && $inst!=$_SESSION['vwmldbm_inst'] && $_SESSION['vwmldbm_inst']!=1) return; // inst=1 can access other inst
	elseif(!$inst) $inst=$_SESSION['vwmldbm_inst']; 
	
	$tb_list=tb_list($db,$TB_PRE);
	foreach($tb_list as $val){
		update_fkey_info($db,$val,$inst);
	}
}

function tb_list($db,$pre){
	global $conn;	
	$tb_list=array();
// SJH_MOD 
	if($pre=="" || $pre==null) 		
		$sql= "select table_name,table_type from information_schema.tables 
			where table_schema='$db' and table_name like '%' and table_type='BASE TABLE'";
	else $sql= "select table_name,table_type from information_schema.tables 
			where table_schema='$db' and table_name like '".$pre."_%' and table_type='BASE TABLE'";
			
	$res = mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		array_push($tb_list,$rs['table_name']);
	}
	return $tb_list;
}

function view_list($db,$pre){
	global $conn;	
	$view_list=array();
// SJH_MOD 
	if($pre=="" || $pre==null) 	
		$sql= "select table_name,table_type from information_schema.tables 
			where table_schema='$db' and table_name like '%' and table_type='VIEW'";
	else $sql= "select table_name,table_type from information_schema.tables 
			where table_schema='$db' and table_name like '".$pre."_%' and table_type='VIEW'";
			
	$res = mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		array_push($view_list,$rs['table_name']);
	}
	return $view_list;
}

function add_lang_to_new_inst($inst_no) {
	global $conn,$DTB_PRE;
	if($inst_no==1) return; // super inst cannot be done.
	$lang_arr=array();
	\vwmldbm\code\get_lang_list($lang_arr,$use_yn='Y');
	if(count($lang_arr)<1) {
		echo "<h2 style='color:red; text-align:center; margin-top:10px;padding:10px;background:yellow;'>First Add Language Code(s)!</h2>";
		return;
	}
	
	$sql="select * from {$DTB_PRE}vwmldbm_fd_mlang where inst=1";
	
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)) {
		if(fd_mlang_exist($inst_no,$rs['fd_no'],$rs['c_lang'])) continue;
		
		$sql="insert into {$DTB_PRE}vwmldbm_fd_mlang  
			(inst,fd_no,c_lang,name) values('$inst_no','{$rs['fd_no']}','{$rs['c_lang']}','{$rs['name']}')";
		if(mysqli_query($conn,$sql)) echo "<font color='green'>{$rs['name']} ({$rs['c_lang']}) added</font><br>";
	}		
}

function fd_mlang_exist($inst_no,$fd_no,$lang) {
	global $conn,$DTB_PRE;
	$sql="select fd_no from {$DTB_PRE}vwmldbm_fd_mlang where inst='$inst_no' and fd_no='$fd_no' and c_lang='$lang'";
	
	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	return($rs['fd_no']);
}


function check_DB_same_as_before() {
	global $conn,$DTB_PRE,$DB;
	$DB_list=array();  // include ones that you want to monitor and make the Relational Model (ER) diagram
	$DB_list[]=$DB;
	$is_DB_same_as_before=true;

	$res=mysqli_query($conn,"select count(name) as num from {$DTB_PRE}vwmldbm_db");

	if($res) $rs=mysqli_fetch_array($res);
	else if($rs['num']===null) {	  // initial DB creation was not done yet. 
		echo"<font color=red>You have not executed '<b>.sql</b>' yet, please first do that.</font><br>";
		die;
	}
	else if($rs['num']!=count($DB_list)) $is_DB_same_as_before=false;  // DB list has been changed 
	else {
		$res2=mysqli_query($conn,"select name from {$DTB_PRE}vwmldbm_db");
		if($res2) for($i=0;$i<$rs['num'];$i++){
			$rs2=mysqli_fetch_array($res2);
			if($rs2['name']!=$DB_list[$i]) {
				$is_DB_same_as_before=false;
				break;
			}
		}
	}

	if(!$rs['num'] || $is_DB_same_as_before==false) { // 1st time VWMLDBM or renew the table, wise2.wise2_wise_db
		echo"Renewing DBs again! Please press <b><font color=red>UPDATE</font></b> button!<br>";
		mysqli_query($conn,"delete from {$DTB_PRE}vwmldbm_db");
		for($i=0;$i<count($DB_list);$i++){
			$sql="INSERT INTO {$DTB_PRE}vwmldbm_db (name) values('{$DB_list[$i]}')"; 
			mysqli_query($conn,$sql);
		}
	}
}

function get_dbs() {
	global $conn,$DTB_PRE;
	$sql="select * from {$DTB_PRE}vwmldbm_db"; 
	$res=mysqli_query($conn,$sql);
	$arr=array();
	if($res) while($rs=mysqli_fetch_assoc($res)) {
		$arr[]=$rs;
	}
	return $arr;
}
?>