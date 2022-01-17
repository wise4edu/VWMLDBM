<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/

/*==============================================================
function list:
	print_code($code_name,$code=null,$form_name=null,$field_name=null,$use_yn=true,$except_code=null,$opt=null,$all_lang=null,$fevent=null)
	print_c_yn($code_name,$code=null,$form_name=null,$opt=null,$fevent=null,$N_RED=null)
	get_c_yn($code=null,$opt=null,$N_RED=null) 
	get_c_no($code_name,$code)
	get_c_code($code_name,$no)
	get_c_name($code_name,$no,$field_name=null,$opt=null,$form_name=null)
	get_code_name_all(&$arr,$code_name,$field_name=null,$opt=null){ // get all code names ahead for performance
	print_lang($code=null, $form_name=null, $use_yn=true,$tag_inside=null)
	get_lang($code)
	get_lang_list(&$arr,$use_yn='Y')
	lang_available($code=null)
	get_field_name($tb,$fd)
	get_field_name_all($tb,$opt=null)
	js_alert($msg=null)
	mlang_change_list()
	get_code_stat($code_name,$inst=null,$opt=null)
	is_code_usable($code) // 2021.03.24
	manage_lang($code,$type='code')
	get_n_data($tb,$fd,$list,&$arr,$opt=null)
	get_code_list(&$arr)
	
Classes:
	class Hdoc
	class Inst_var
  ============================================================*/
namespace vwmldbm\code;
if($VWMLDBM['MULTI_INST']) $instTxt=" inst='{$_SESSION['vwmldbm_inst']}' ";
else $instTxt=" isnull(code)=false ";

$codes=array();
get_code_set($codes);

if(!isset($codes['vwmldbm_c_lang'])){
	$sql="insert into {$DTB_PRE}code_cset (code,c_lang,code_name,name,en_only_yn,use_yn) 
		values(1,10,'vwmldbm_c_lang','Display Language','Y','Y')";
	mysqli_query($conn,$sql);
}

function get_code_set(&$codes){
	global $conn,$DTB_PRE,$VWMLDBM,$wmlang,$instTxt;
	
	// First add code_cset data
		$codes['code_cset']=array(
			"code_name"=>"code_cset",
			"name"=>"Code Set",
			"en_only_yn"=>"Y"
		);
	
	$sql = "select * from {$DTB_PRE}code_cset where $instTxt";

	$res = mysqli_query($conn,$sql);	
	if($res) while($rs = mysqli_fetch_assoc($res)){
		$codes[$rs['code_name']]=$rs;
	}
}

function check_code_set($c){
	global $conn,$DTB_PRE,$VWMLDBM,$wmlang,$instTxt;
	return (mysqli_query($conn,"DESCRIBE {$DTB_PRE}$c"));
}

function print_code($code_name,$code=null,$form_name=null,$field_name=null,$use_yn=true,$except_code=null,$opt=null,$all_lang=null,$fevent=null,$extra_sql=null) {
 // $all_lang='ALL_LANG' when to show all codes

	global $conn,$DTB_PRE,$VWMLDBM,$wmlang,$instTxt;

	$lang=$_SESSION['vwmldbm_lang'];	
	if(!$conn || !$DTB_PRE || !$lang) return; // illegal access

	if(!$field_name) $field_name="name";
	if($form_name==null) $form_name=$code_name;
	if($use_yn) $use_yn=" and use_yn='Y' "; // only show code with use_yn='Y'
	else $use_yn=""; // show all codes
	$opt=strtoupper($opt);
	
	$rtxt="";// return text

	if($code_name =='vwmldbm_c_lang') $lang_sql=null;
	else if($all_lang!="ALL_LANG") $lang_sql=" and c_lang='$lang' "; 
	else if($all_lang!="EN") $lang_sql=" and c_lang='10' "; 
	
	// check if the chosen field name is available	
	$sql="select $field_name from {$DTB_PRE}$code_name where $instTxt $lang_sql $extra_sql limit 1";

	$res_c = mysqli_query($conn,$sql);
	if($res_c) $rs_c = mysqli_fetch_array($res_c);
	if($rs_c[$field_name]==null || $rs_c[$field_name]=="") $field_name="name"; // not available, so make it default
	
	$except=null;
	if($except_code) $except=" and code NOT in($except_code) "; // except this code, eg, display lan list except the default one (English)
	if($opt=='RD_ONLY') { // The specified code name only 
		$sql = "select code,$field_name from {$DTB_PRE}$code_name where $instTxt and code='$code' $use_yn $except $lang_sql $extra_sql order by code";
	
		$res = mysqli_query($conn,$sql);
		$t_code=array(); // consider case of c_year: 2,3,4
		$t_code=explode(",",$code);	
		
		if ($res) {	
			$txt="";
			while ($rs = mysqli_fetch_array($res)) {
				if(count($t_code)==1) return $rs[$field_name]; // non c_year like code. Just retrun one! 
				if(in_array($rs['code'],$t_code)>0) {
					if(!$rs[$field_name]) $rs[$field_name]=$rs['code'];
					$txt.=$rs[$field_name].",";
				}
			}
			return substr($txt,0,-1);
		}
	}
	else if($opt=='RADIO') { // radio button
		$sql = "select code,$field_name from {$DTB_PRE}$code_name where $instTxt $use_yn $except $lang_sql $extra_sql order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {				
				$rtxt.="<input name='$form_name' type='radio' class='class_$form_name' value='".$rs['code']."' ";
				if($rs['code']=="$code") $rtxt.=" checked";
				if(!$rs[$field_name]) $rs[$field_name]=$rs['code'];
				$rtxt.= "> $rs[$field_name] &nbsp;";
			}
		}
		return $rtxt;
	}
	else if($opt=='CHECKBOX') { // multiple select checkbox
		$sql = "select code,$field_name from $DTB_PRE"."_$code_name where $instTxt $use_yn $except $lang_sql $extra_sql order by code";
		$res = mysqli_query($conn,$sql);
		$t_code=array();
		$t_code=explode(",",$code);	
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {		
				$rtxt.="<input name='$form_name"."[]' type='checkbox' class='class_$form_name' value='$rs[code]' ";
				if(in_array($rs['code'],$t_code)>0) $rtxt.=" checked";
				if(!$rs[$field_name]) $rs[$field_name]=$rs['code'];
				$rtxt.= "> $rs[$field_name] &nbsp; ";
			}
		}
		return $rtxt;
	}
	else if($opt=='CHECKBOX_ALL') { // multiple select checkbox
		$sql = "select code,$field_name from {$DTB_PRE}$code_name where $instTxt $use_yn $except $lang_sql $extra_sql order by code";
		$res = mysqli_query($conn,$sql);
		$t_code=array();
		$t_code=explode(",",$code);	
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {		
				$rtxt.="<input name='$form_name"."[]' type='checkbox' class='class_$form_name' value='$rs[code]' $fevent ";
				
				if($code && in_array($rs['code'],$t_code)>0) $rtxt.=" checked"; // if code is given do not do all checked
				else if(!$code) $rtxt.=" checked";
				
				if(!$rs[$field_name]) $rs[$field_name]=$rs['code'];
				$rtxt.= "> $rs[$field_name] &nbsp; ";
			}
		}
		return $rtxt;
	}
	else if($opt=='ONE') { // select 		 
		$rtxt.= "<select name='$form_name'>\n";
		$sql = "select code,$field_name from {$DTB_PRE}$code_name where $instTxt $use_yn and code='$code' $except $lang_sql $extra_sql order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {
				if ($code == $rs['code']) $sel=" selected";
				else $sel="";
				$rtxt.= "<option value=".$rs['code']." $sel>".$rs[$field_name]."</option>\n";
			}
		}	
		$rtxt.= "</select>";
		return $rtxt;
	}
	else { // select 
		$c_name=null;
		$rtxt.= "<select name='$form_name' $fevent>\n";
		$rtxt.= "<option value='' selected>-- {$wmlang['txt']['select']} --</option>\n";
		if (($code_name=='vwmldbm_c_lang') && ($field_name != 'name')) $c_name=', name'; // for c_lang select multi language

		$sql = "select code,$field_name $c_name from {$DTB_PRE}$code_name where $instTxt $use_yn $except $lang_sql $extra_sql order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {
				if (($code_name=='c_lang') && ($field_name != 'name')) $c_name='name'; // for c_lang select multi language
				if ($code == $rs['code']) $sel=" selected";
				else $sel="";
				
				if($c_name) $c_name_txt=$rs[$c_name];
				else $c_name_txt=null;
				
				$rtxt.= "<option value=".$rs['code']." $sel>".$rs[$field_name]." $c_name_txt</option>\n";
			}
		}	
		$rtxt.= "</select>";
		return $rtxt;
	}
}

function print_c_yn($code_name,$code=null,$form_name=null,$opt=null,$fevent=null,$txt_color=null){
	global $wmlang;
	$rval=null;

	$opt=strtoupper($opt);
	if($form_name==null) $form_name=$code_name;
	
	if($txt_color=='N_RED') $NcolorStyle=" style='color:red' ";
	else if($txt_color=='Y_BLUE') $YcolorStyle=" style='color:blue' ";
	else $colorStyle="";
	
// multi-lang
	if($txt_color=='Y_BLUE') $y="<font color=blue><b>".$wmlang['txt']['yes']."</b></font>";
	else $y=$wmlang['txt']['yes'];
	
	if($txt_color=='N_RED') $n="<font color=red><b>".$wmlang['txt']['no']."</b></font>";
	else $n=$wmlang['txt']['no'];
	
	if($opt=='RD_ONLY_HD') { // Specified code name + hidden
		if($code=='Y') 	$rval.= "$y <input type=hidden name='$form_name' value='$code'>";
		else $rval.= "$n <input type=hidden name='$form_name' value='$code'>";
	}
	else if($opt=='HD') { // The specified code name as a hidden 
		$rval.= "<input type=hidden name='$form_name' value='$code'>";
	}
	else if($opt=='RD_ONLY') { // The specified code name only 
		if($code=='Y') 	$rval.= "$y";
		else if($code=='N') $rval.= "$n";
	}
	else if($opt=='RD_ONLY_Y') { // The specified code name only 
		if($code=='Y') 	$rval.= "$y";
	}
	else { // not a read only nor hidden mode
		if($opt=='RADIO'){ // radio button
			if($code=='Y') $c=" checked"; else $c="";
			$rval.="<input name='$form_name' type='radio' value='Y' $c $fevent> $y &nbsp;&nbsp;";
			
			if($code=='N') $c=" checked"; else $c="";
			$rval.="<input name='$form_name' type='radio' value='N' $c $fevent> $n ";
		}
		else { // select		
			if($code=='Y' || !$code) $colorStyle=null; // remove the red color
			$rval.= "<select name='$form_name' $fevent $colorStyle><option value=''>-select-</option>\n";
			if($code=='Y') $sel=" selected"; else $sel="";
			$rval.= "<option value='Y' $sel>$y</option>";
			if($code=='N') $sel=" selected"; else $sel="";
			$rval.= "<option value='N' $sel>$n</option>";
			$rval.= "</select>";
		}
	}
	return $rval;
}

function get_c_yn($code=null,$opt=null,$N_RED=null) {
	global $wmlang;
	
// multi-lang
	$y=$wmlang['txt']['yes'];
	
	if($N_RED=='N_RED') $n="<font color=red>".$wmlang['txt']['no']."</font>";
	else $n=$wmlang['txt']['no'];
	
	if($N_RED=='Y_BLUE') $y="<font color=blue>".$wmlang['txt']['yes']."</font>";
	else $y=$wmlang['txt']['yes'];
	
	if($code=='Y' && $opt!='N_ONLY') return $y;
	else if($code=='N' && $opt!='Y_ONLY') return $n;
}

function get_c_no($code_name,$code){
	global $conn,$DTB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	$lang=10; // it should be the default lang, English
	if($code_name =='vwmldbm_c_lang') $lang_sql=null;
	else $lang_sql=" and c_lang='$lang' ";
	
	$sql = "select no from {$DTB_PRE}$code_name where $instTxt and code='$code' $lang_sql";
	$res = mysqli_query($conn,$sql);
	if ($res) $rs = mysqli_fetch_array($res);
	return $rs['no'];
}

function get_c_code($code_name,$no){
	global $conn,$DTB_PRE;
	$inst=$_SESSION['vwmldbm_inst'];
	$sql = "select code from {$DTB_PRE}$code_name where $instTxt and no='$no'";
	$res = mysqli_query($conn,$sql);
	if ($res) $rs = mysqli_fetch_array($res);
	return $rs['code'];
}

function get_c_name($code_name,$code=null,$field_name=null,$opt=null,$form_name=null,$extra_sql=null){ 
	global $conn,$DTB_PRE;
	$inst=$_SESSION['vwmldbm_inst'];
	$lang=$_SESSION['vwmldbm_lang'];
	if(!$conn || !$DTB_PRE || !$lang) return;
	$opt=strtoupper($opt);
	if(!$field_name)$field_name='name';
	
	/* This caused unexpected side-effect when there is no code, it still displays something. 2021/3/25
	if($code)
		$sql = "select $field_name from {$DTB_PRE}$code_name where $instTxt and c_lang='$lang' and code='$code' $extra_sql";
	else $sql = "select $field_name from {$DTB_PRE}$code_name where $instTxt and c_lang='$lang' $extra_sql";
	*/
	
	$sql = "select $field_name from {$DTB_PRE}$code_name where $instTxt and c_lang='$lang' and code='$code' $extra_sql";
	
	$res = mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	
	if(!$rs[$field_name]){ // the code name by specified user language doesn't exist. Make it default (english)
		$field_name="name";
		$lang=10; // default lang, English
		$sql = "select $field_name from $DTB_PRE"."_$code_name where c_lang='$lang' and code='$code' $extra_sql";
		$res = mysqli_query($conn,$sql);
		if($res) $rs=mysqli_fetch_array($res);
	}
	
	if($opt!="HIDDEN") $result=$rs[$field_name]; // value

	if($opt=="RD_HIDDEN"|| $opt=="HIDDEN") // hidden filed
		$result.=" <input type='hidden' name='$form_name' value='$code'>";
	return $result;
}

function get_code_name_all(&$arr,$code_name,$field_name=null,$lang=null,$opt=null,$use_yn=null){ // get all code names ahead for performance
	global $conn,$DTB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	if(!$lang) 
		$lang=$_SESSION['vwmldbm_lang'];
	if(!$lang) $lang=10; // default English
	
	if(!$conn || !$DTB_PRE || !$lang) return;
	if(!$field_name)$field_name='name';
	
	$use_yn_txt=null;
	if($use_yn) $use_yn_txt=" and use_yn='$use_yn'";
	
	if($opt=='ALL_LANG')
		$sql = "select code,$field_name from {$DTB_PRE}{$code_name} where $instTxt and isNULL(code)=false $use_yn_txt";
	else $sql = "select code,$field_name from {$DTB_PRE}{$code_name} where $instTxt and c_lang='$lang' $use_yn_txt";

	$res = mysqli_query($conn,$sql); 
	if(substr(phpversion(),0,3)>=7.2){ // PHP version
		if($res) $arr = mysqli_fetch_all($res,MYSQLI_ASSOC);
	}
	else {
		if($res) while($rs = mysqli_fetch_assoc($res)){
			$arr[]=$rs;
		}
	}
	
	$arr2=array();
	foreach($arr as $val) $arr2[$val['code']]=$val[$field_name];
	$arr=$arr2;
}

function check_code($code_name,$code,$inst=null) {
	global $conn,$DTB_PRE,$instTxt;
	$inst=($inst ? $inst : $_SESSION['vwmldbm_inst']);
	if(!$code) return false;
	
	if($code_name=='c_lang') $tb=$DTB_PRE."_vwmldbm_".$code_name;
	else $tb=$DTB_PRE."_code_".$code_name;
	
	$sql = "select code, use_yn from $tb where code='$code' and $instTxt";
	// echo $sql."<br>";
	$res = mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	
	if($rs['code'] && $rs['use_yn']!='N') return 1; // code exist and use_yn ='Y'(enabled)
	else if($rs['code']) return 2; // code exist but use_yn='N'
	else {
		return false; // code doesn't exist
	}
}

function print_lang($code=null, $form_name=null, $use_yn=true,$tag_inside=null,$opt=null) {
	global $conn,$DTB_PRE,$wmlang,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	if($form_name==null) $form_name='c_lang';
	if($use_yn) $use_yn=" and use_yn='Y' "; // only show code with use_yn='Y'
	else $use_yn=""; // show all codes
	
	echo "<select name='$form_name' $tag_inside>\n";
	
	if($opt=='ONE') {
		$c_lang_txt="and code='$code'";
	}
	else {
		$c_lang_txt=null;
	}
		
	if($opt!='ONE')echo "<option value='' selected>-- {$wmlang['txt']['select']}--</option>\n";
	
	$sql = "select * from {$DTB_PRE}vwmldbm_c_lang where $instTxt and isNULL(code)=false $use_yn $c_lang_txt order by priority asc";

	$res = mysqli_query($conn,$sql);
	if ($res) {		
		while ($rs = mysqli_fetch_array($res)) {
			if ($code == $rs['code']) $sel=" selected";
			else $sel="";
			echo "<option value={$rs['code']} $sel>{$rs['n_name']}</option>\n";
		}
	}	
	echo "</select>";
}

function get_lang($code) {
	global $conn,$DTB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	$sql = "select name from {$DTB_PRE}vwmldbm_c_lang where $instTxt and code='$code'";
	$res = mysqli_query($conn,$sql); 
	if ($res)	$rs = mysqli_fetch_array($res);
	return $rs['name'];
}

function get_lang_list(&$arr,$use_yn='Y',$opt=null) {
	global $conn,$DTB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	if($use_yn=='Y') $use_yn_txt=" and use_yn='Y'";
	$sql = "select * from {$DTB_PRE}vwmldbm_c_lang where $instTxt $use_yn_txt";
	$res = mysqli_query($conn,$sql); 
	if ($res) while($rs = mysqli_fetch_array($res)){
		if($opt=='KEY') $arr[]=$rs['code'];
		else $arr[$rs['code']]=$rs['name'];
	}
}

function lang_available($code=null){
	global $conn,$DTB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	$sql = "select name from {$DTB_PRE}vwmldbm_c_lang where $instTxt and code='$code' and use_yn='Y'";
	$res = mysqli_query($conn,$sql); 
	if ($res) $rs = mysqli_fetch_array($res);
	if($rs['name']) return true;
	else return false;
}

function get_field_name($tb,$fd){
	global $conn,$DB,$DTB_PRE,$TB_PRE,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	if($TB_PRE) $tb_name=$TB_PRE."_".$tb; //SJH_MOD
	else $tb_name=$tb;

  // 1. get the field no from fd_mlang
	$sql="select no from {$DTB_PRE}vwmldbm_fd where db_name='$DB' and tb_name='$tb_name' and field='$fd'";
	$res_t = mysqli_query($conn,$sql); 				
	if ($res_t)	$rs_t = mysqli_fetch_array($res_t);	

  // 2. get the name of the specified language
	$sql="select name from {$DTB_PRE}vwmldbm_fd_mlang where $instTxt and  fd_no='{$rs_t['no']}' and c_lang='{$_SESSION['vwmldbm_lang']}'";
	$res_f = mysqli_query($conn,$sql); 
	$rs_f=null;
	if ($res_f)	$rs_f = mysqli_fetch_array($res_f);

  // 3. If there is no name from above, try the default name (English)
	if($rs_f['name']!="") return $rs_f['name']; // in the specified language
	else {
		$sql="select name from {$DTB_PRE}vwmldbm_fd_mlang where $instTxt and fd_no='{$rs_t['no']}' and c_lang=10"; // English

		$res_f2 = mysqli_query($conn,$sql);
		$rs_f2=null;
		if ($res_f2) $rs_f2 = mysqli_fetch_array($res_f2);
		if($rs_f2['name']) return $rs_f2['name'];
		else return $fd;
	}
}

function get_field_name_all($tb,&$arr,$opt=null){
	global $conn,$DB,$DTB_PRE,$TB_PRE,$sys_var,$instTxt;		
	$tb_name=$TB_PRE."_".$tb;
	$inst=$_SESSION['vwmldbm_inst'];
	
	$sql="select no,field from {$DTB_PRE}vwmldbm_fd where db_name='$DB' and tb_name='$tb_name'";
	$res_t = mysqli_query($conn,$sql); 				
	if ($res_t)	while($rs_t = mysqli_fetch_array($res_t)){
		$fd=$rs_t['field'];
		$sql="select name from {$DTB_PRE}vwmldbm_fd_mlang where $instTxt and fd_no='{$rs_t['no']}' and c_lang='{$_SESSION['vwmldbm_lang']}'";
		$res_f = mysqli_query($conn,$sql); 
		if ($res_f)	$rs_f = mysqli_fetch_array($res_f);
		if($opt=='TWO_LANG' && $_SESSION['vwmldbm_lang']!=10){ // two c_lang: eg, KOREAN(ENGLISH) if c_lang is KOREAN
			$sql="select name from {$DTB_PRE}vwmldbm_fd_mlang where $instTxt and fd_no='{$rs_t['no']}' and c_lang=10"; // English
			$res_f2 = mysqli_query($conn,$sql); 
			
			if($res_f2) $rs_f2 = mysqli_fetch_array($res_f2);
			if($rs_f2['name']) $arr[$fd]= "{$rs_f['name']} ({$rs_f2['name']})";
			else if($rs_f['name']) $arr[$fd]= $rs_f['name'];
			else $arr[$fd]= $fd;
		}	
		else {
			if($rs_f['name']!="") $arr[$fd]= $rs_f['name']; // in the specified language
			else {
				$sql="select name from {$DTB_PRE}vwmldbm_fd_mlang where $instTxt and fd_no='{$rs_t['no']}' and c_lang=10"; // English
				$res_f2 = mysqli_query($conn,$sql); 
				if ($res_f2) $rs_f2 = mysqli_fetch_array($res_f2);
				if($rs_f2['name']) $arr[$fd]= $rs_f2['name'];
				else $arr[$fd]= $fd;
			}
		}
	}
}

function js_alert($msg=null){
	$msg=addslashes($msg);
	echo "<script>alert(\"$msg\");</script>";
}

function mlang_change_list(){
	if($_GET['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=$_GET['vwmldbm_lang'];
	if(!$_SESSION['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=10; // default English
	$inside_tag="onChange=\"window.location='$_SELF?vwmldbm_lang='+this.value\"";	
	
	print_lang($_SESSION['vwmldbm_lang'],null,true,$inside_tag);
	echo "</p>";
}

function get_code_stat($code_name,$inst=null,$opt=null,$opt2=null){
	global $conn,$DTB_PRE,$instTxt;

	if($code_name=='vwmldbm_c_lang') { // vwmldbm_c_lang is the language code table of vwmldbm
		$langName="code";
	}
	else $langName="c_lang";
	
	$sql="select count(code) cnt from {$DTB_PRE}$code_name where $instTxt";

	// if($opt=='USE_YN_Y') $sql.=" and NOT use_yn <=>'N' ";
	if($opt=='USE_YN_Y') $sql.=" and (use_yn ='Y' AND ISNULL(use_yn)=false) ";
	if($opt2=='EN' && $code_name!='vwmldbm_c_lang') $sql.=" and $langName=10 "; // vwmldbm_c_lang should show all langs

	$res=mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	return $rs['cnt'];
}

function is_code_usable($code) { // random id for security (eg,for e-resource storage's folder name)
    global $conn,$DTB_PRE,$instTxt;

    if(!isset($_SESSION['vwmldbm_inst'])) return; 

    $sql = "select count(code) as cnt from {$DTB_PRE}code_cset where inst='{$_SESSION['vwmldbm_inst']}' and code_name='$code' and use_yn='Y'";
    $res=mysqli_query($conn,$sql);
    if($res) $rs=mysqli_fetch_array($res);
    return($rs['cnt']);
}

function manage_lang($code,$type='code') { // if the country code / or c_lang is changed, apply it (eg, Laravel)
	global $perm,$conn,$DTB_PRE,$instTxt;
	if(!isset($_SESSION['vwmldbm_inst']) || !isset($_SESSION['vwmldbm_lang'])) return; // [TBM]
	
	$inst=$_SESSION['vwmldbm_inst'];
	
	$cc_arr=array();
	$sql="select code, ccode from $DTB_PRE"."_vwmldbm_c_lang where $instTxt ";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		$cc_arr[$rs['code']]=$rs['ccode'];
	}
	
	if($type=='code') { // c_lang
		if($_SESSION['vwmldbm_lang']!=$code){
			$_SESSION['vwmldbm_lang']=$code;
			return true; // change
		}
	}
	else { // country code
		if(!isset($cc_arr[$_SESSION['vwmldbm_lang']])) return false;
		if(strtolower($cc_arr[$_SESSION['vwmldbm_lang']])!=strtolower($code)){
			$_SESSION['vwmldbm_lang']=array_search($code,$cc_arr);	
			return true; // change
		}
	}
	
	return false; // no change;
}

function get_n_data($tb,$fd,$list,&$arr,$opt=null){
	global $conn,$DB,$DTB_PRE,$TB_PRE,$instTxt;
	$tb_name=$TB_PRE."_".$tb;
	$inst=$_SESSION['vwmldbm_inst'];
	
	$sql="select * from $tb_name where $instTxt and $fd IN($list)";
	
	$res = mysqli_query($conn,$sql); 
	if(substr(phpversion(),0,3)>=7.2){ // PHP version
		if($res) $arr = mysqli_fetch_all($res,MYSQLI_ASSOC);
	}
	else {
		if($res) while($rs = mysqli_fetch_assoc($res)){
			$arr[]=$rs;
		}
	}
	
	if($opt=='KEY'){
		$arr2=array();
		foreach($arr as $val) $arr2[$val[$fd]]=$val;
		$arr=$arr2;
	}
}

class Hdoc { //  HTML
	public function __construct($ht=null) {
		$this->htitle=$ht;
	}
	
	public function print_head($opt=null) {
		global $VWMLDBM,$loading_included,$inst_var;
		echo"
		<html>
		<head>
		<title>$this->htitle</title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		require_once $VWMLDBM['VWMLDBM_RT'].'/lib/include_jQuery.php';
		echo "<link href='".$VWMLDBM['VWMLDBM_WWW_RT']."/css/common.css?nocache=".$inst_var->cache_t."' rel='stylesheet' type='text/css'>";
		echo "</head>";
		
		if($opt!="NO_LOADING") {
			echo "<div id='loading'><img id='loading-image' src='{$VWMLDBM['VWMLDBM_WWW_RT']}/img/loading3.gif' alt='Loading...' /></div>";
			echo "<script src='{$VWMLDBM['VWMLDBM_WWW_RT']}/js/loading.js?nocache={$inst_var->cache_t}'></script>";
			$loading_included=true;
		}
	}
	
	public function print_title($t=null){
		echo "<h1>$t</h1>";
	}
	
	public function print_body_tag($option=null,$style=null,$onload=null){
		if(!$style)$style="style='text-align: center;'";
		if($onload) $onload="onload=\"$onload\"";
		echo"<body $style $onload>";
	}
	
	public function print_foot($opt=null){
		global $VWMLDBM;
		require_once($VWMLDBM['VWMLDBM_RT']."/footer.php");
		echo"</body></html>";
	}
}

function get_code_list(&$arr) {
	global $conn,$DB,$DTB_PRE,$TB_PRE,$instTxt;	
	$sql="select * from {$DTB_PRE}code_cset where $instTxt";

	$res = mysqli_query($conn,$sql); 
	if($res) while($rs = mysqli_fetch_assoc($res)){
		$arr[$rs['name']]=array($rs['code_name'],'name');
	}
}

class Inst_var {
	public function __construct($inst=null,$inst_uname=null) {
		global $conn,$DTB_PRE;
		$this->tb="{$DTB_PRE}vwmldbm_inst";
		
		if(!$inst_uname){ // inst name was not specified
			if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
			if(!$conn || !$inst) return;
			$sql = "select * from {$this->tb} where no='$inst'";
		}
		else { // inst name was specified
			$sql = "select * from {$this->tb} where inst_uname='$inst_uname'";
		}		

		$res = mysqli_query($conn,$sql);
		
		if($res) {
			$rs=mysqli_fetch_array($res);		
			$this->no=$rs['no'];
			$this->cache_t=$rs['cache_t'];
			$this->inst_uname=$rs['inst_uname'];
			$this->inst_id=$rs['inst_id'];
			$this->secret=$rs['secret'];
			$this->host=$rs['host'];
			$this->other_prg_login_uri=$rs['other_prg_login_uri'];
			$this->other_prg_adm=$rs['other_prg_adm'];
			$this->other_prg_sadm=$rs['other_prg_sadm'];
			$this->mode=$rs['mode'];
			
			if(!$rs['cache_t']) {
				$this->cache_t=date('Y-m-d H:i:s');
				$this->update_cache_time();
			}
		}
	}
	
	public function update($data,$mode=null){ // update fields
		global $conn,$DTB_PRE,$instTxt;
		if($mode=='SUPER') {
			$instNo="_".$this->no;
		}
		$sql="update {$this->tb} set inst_uname=\"{$data['inst_uname'.$instNo]}\"  ";
		$sql.=",host=\"{$data['host'.$instNo]}\"  ";
		$sql.=",inst_id=\"{$data['inst_id'.$instNo]}\"  ";
		$sql.=",secret=\"{$data['secret'.$instNo]}\"  ";
		$sql.=",other_prg_login_uri=\"{$data['other_prg_login_uri'.$instNo]}\"  ";
		$sql.=",other_prg_sadm=\"{$data['other_prg_sadm'.$instNo]}\"  ";
		$sql.=",other_prg_adm=\"{$data['other_prg_adm'.$instNo]}\"  ";
		$sql.=",mode=\"{$data['mode'.$instNo]}\"  ";
		$sql.="  where no='{$this->no}' ";

		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) {
			return true;
		}
		else return false;
	}
	
	public function update_cache_time(){ // update field
		global $conn,$DTB_PRE,$instTxt;
		$inst=$this->no;
		$sql="update {$this->tb} set cache_t='".date('Y-m-d H:i:s')."' ";
		$sql.="  where no='$inst' ";
		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) {
			return true;
		}
		else return false;
	}
	
	public static function get_all_insts(&$arr){
		global $conn,$DTB_PRE;
		if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
		$sql = "select * from {$DTB_PRE}vwmldbm_inst";
		$res = mysqli_query($conn,$sql);
		
		if($res) while($rs=mysqli_fetch_array($res)) {
			$arr[$rs['no']]=$rs;
		}			
	}
	
	public static function get_other_insts(&$arr,$except_no=1){
		global $conn,$DTB_PRE;
		if(!$inst) $inst=$_SESSION['vwmldbm_inst'];
		$sql = "select * from {$DTB_PRE}vwmldbm_inst where no!='$except_no' ";
		$res = mysqli_query($conn,$sql);
		
		if($res) while($rs=mysqli_fetch_array($res)) {
			$arr[$rs['no']]=$rs;
		}			
	}
	
	public static function del($no){ // delete
		global $conn,$DTB_PRE;
		$sql="delete from {$DTB_PRE}vwmldbm_inst where no='$no' ";
	
		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) {
			return true;
		}
		else return false;
	}
	
	public static function add($data){ // add
		global $conn,$DTB_PRE;
		$sql="insert into {$DTB_PRE}vwmldbm_inst (no,inst_uname,host,inst_id,secret,other_prg_login_uri,other_prg_adm,other_prg_sadm,mode)
			values (\"{$data['inst_no_add']}\",\"{$data['inst_uname_add']}\",\"{$data['host_add']}\",\"{$data['inst_id_add']}\",\"{$data['secret_add']}\",
			\"{$data['other_prg_login_uri_add']}\",\"{$data['other_prg_adm_add']}\",\"{$data['other_prg_sadm_add']}\",\"{$data['mode_add']}\")";

		mysqli_query($conn,$sql);
		if(mysqli_affected_rows($conn)>0) {
			return true;
		}
		else return false;
	}
	
	public static function email_exist_in_user($email){ // check if it is not already there in user table
		global $conn,$DTB_PRE,$VWMLDBM;
		
		$userTB=($VWMLDBM['USER_TB']?$VWMLDBM['USER_TB']:'users');
		
		$sql="select id from {$DTB_PRE}$userTB where email='$email'";
		$res = mysqli_query($conn,$sql);
		if($res) $rs=mysqli_fetch_assoc($res);
		return($rs['id']);
	}
}

function check_n_create_lang_code($code_name,$c_lang_arr) {
	global $inst,$DTB_PRE,$conn,$instTxt;
	
	// first get the code list of the mother code
	$code_list=array();
	$sql="select * from {$DTB_PRE}{$code_name} where $instTxt and c_lang='10'";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		$code_list[$rs['code']]=$rs;
	}
	
	foreach($code_list as $c => $val) {
		foreach($c_lang_arr as $ln) {
			$sql="select * from {$DTB_PRE}{$code_name} where $instTxt and code='$c' and c_lang='$ln'";
			$res2=mysqli_query($conn,$sql);
			if($res2) $rs2=mysqli_fetch_array($res2);
			if(!$rs2['code']) { // not exist, so add one				
				if($VWMLDBM['MULTI_INST']) {
					$sql="insert into {$DTB_PRE}$code_name (inst,code,c_lang,name,use_yn) 
						values('$inst','$c','$ln','{$val['name']}','{$val['use_yn']}')";
				}
				else {
					$sql="insert into {$DTB_PRE}$code_name (code,c_lang,name,use_yn) 
						values('$c','$ln','{$val['name']}','{$val['use_yn']}')";
				}
				mysqli_query($conn,$sql);
				mysqli_affected_rows($conn);
			}
		}
	}	
}

function add_langs() {
	global $DTB_PRE,$conn,$instTxt;
	$inst=$_SESSION['vwmldbm_inst'];
	$mod_ok=false;
	$code_name='vwmldbm_c_lang';
	// first get the code list of the mother code
	$code_list=array();
	$sql="select * from {$DTB_PRE}{$code_name} where $instTxt";
	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)){
		$code_list[$rs['code']]=$rs;
	}
	foreach($code_list as $key => $val) {		
		$sql="insert into $DTB_PRE"."_$code_name (inst,code,name,use_yn,n_name,ccode,priority) 
			values('$inst','$key','{$val['name']}','{$val['use_yn']}','{$val['n_name']}','{$val['ccode']}','{$val['priority']}')";
		mysqli_query($conn,$sql);
		$mod_ok=mysqli_affected_rows($conn);
	}
	
	if($mod_ok) return true;
}

function get_all_field_names($code_name,&$field_names) {
	global $DTB_PRE,$conn,$instTxt;
	$sql="show columns from {$DTB_PRE}$code_name";

	$res=mysqli_query($conn,$sql);
	if($res) while($rs=mysqli_fetch_array($res)) {
		array_push($field_names,$rs['Field']);
	}
}
