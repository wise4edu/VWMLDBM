<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
  
/*==============================================================
function list:
	print_code($opt=null,$code_name,$code=null,$form_name=null,$field_name=null,$use_yn=true,$return=null,$except_code=null)
	get_c_no($code_name,$code)
	get_c_code($code_name,$no)
	get_c_name($code_name,$no,$field_name=null,$opt=null,$form_name=null)
	print_lang($code=null, $form_name=null, $use_yn=true,$tag_inside=null)
	get_lang($code)
	lang_available($code=null)
	get_field_name($tb,$fd)
	js_alert($msg=null){
  ============================================================*/
namespace vwmldbm\code;

function print_code($opt=null,$code_name,$code=null,$form_name=null,$field_name=null,$use_yn=true,$return=null,$except_code=null) {
	global $conn,$DTB_PRE;
	$lang=$_SESSION['vwmldbm_lang'];
	if(!$conn || !$DTB_PRE || !$lang) return;
	
	$opt=strtoupper($opt);
	if($form_name==null) $form_name=$code_name;
	if($use_yn) $use_yn=" and use_yn='Y' "; // only show code with use_yn='Y'
	else $use_yn=""; // show all codes
	$rtxt="";// return text if return mode (not print mode)

	// check if the chosen field name is available	
	$sql="select $field_name from $DTB_PRE"."_$code_name where c_lang=$lang limit 1";
	$res_c = mysqli_query($conn,$sql);
	if($res_c) $rs_c = mysqli_fetch_array($res_c);
	if($rs_c[$field_name]==null || $rs_c[$field_name]=="") $field_name="name"; // not available, so make it default
	
	if($except_code) $except=" and code<>'$except_code' "; // except this code, eg, display lan list except the default one (English)
	if($opt=='RD_ONLY') { // The specified code name only 
		$sql = "select code,$field_name from $DTB_PRE"."_$code_name where c_lang=$lang and code='$code' isNULL(code)=false $use_yn $except order by code";
		$res = mysqli_query($conn,$sql);
		$t_code=array(); // consider case of c_year: 2,3,4
		$t_code=explode(",",$code);	
		if ($res) {	
			$txt="";
			while ($rs = mysqli_fetch_array($res)) {		
				if(in_array($rs[code],$t_code)>0) {
					if(!$rs[$field_name]) $rs[$field_name]=$rs[code];
					$txt.=$rs[$field_name].",";
				}
			}
			if($return=='RETURN') return substr($txt,0,-1);
			else echo substr($txt,0,-1);
		}
	}
	else if($opt=='RADIO') { // radio button
		$sql = "select code,$field_name from $DTB_PRE"."_$code_name where c_lang=$lang $use_yn $except order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {				
				$rtxt.="<input name='$form_name' type='radio' class='class_$form_name' value='$rs[code]' ";
				if($rs[code]=="$code") $rtxt.=" checked";
				if(!$rs[$field_name]) $rs[$field_name]=$rs[code];
				$rtxt.= ">$rs[$field_name] &nbsp;";
			}
		}
		if($return=='RETURN') return $rtxt;
		else echo $rtxt;
	}
	else if($opt=='CHECKBOX') { // multiple select checkbox
		$sql = "select code,$field_name from $DTB_PRE"."_$code_name where c_lang=$lang $use_yn $except order by code";
		$res = mysqli_query($conn,$sql);
		$t_code=array();
		$t_code=explode(",",$code);	
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {		
				$rtxt.="<input name='$form_name"."[]' type='checkbox' class='class_$form_name' value='$rs[code]' ";
				if(in_array($rs[code],$t_code)>0) $rtxt.=" checked";
				if(!$rs[$field_name]) $rs[$field_name]=$rs[code];
				$rtxt.= ">$rs[$field_name] &nbsp;";
			}
		}
		if($return=='RETURN') return $rtxt;
		else echo $rtxt;
	}
	else if($opt=='ONE') { // select 		 
		$rtxt.= "<select name='$form_name'>\n";
		$sql = "select code,$field_name from $DTB_PRE"."_$code_name where c_lang=$lang $use_yn and code='$code' $except order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {
				if ($code == $rs['code']) $sel=" selected";
				else $sel="";
				$rtxt.= "<option value=".$rs[code]." $sel>".$rs[$field_name]."</option>\n";
			}
		}	
		$rtxt.= "</select>";
		if($return=='RETURN') return $rtxt;
		else echo $rtxt;
	}
	else { // select 		
		$rtxt.= "<select name='$form_name'>\n";
		$rtxt.= "<option value='' selected>-select-</option>\n";
		$sql = "select code,$field_name $c_name from $DTB_PRE"."_$code_name where c_lang=$lang $use_yn $except order by code";
		if (($code_name=='c_lang') && ($field_name != 'name')) $c_name=', name'; // for c_lang select multi language

		$sql = "select code,$field_name $c_name from $DTB_PRE"."_$code_name where c_lang=$lang $use_yn $except order by code";
		$res = mysqli_query($conn,$sql);
		if ($res) {		
			while ($rs = mysqli_fetch_array($res)) {
				if (($code_name=='c_lang') && ($field_name != 'name')) $c_name='name'; // for c_lang select multi language
				if ($code == $rs['code']) $sel=" selected";
				else $sel="";
				$rtxt.= "<option value=".$rs[code]." $sel>".$rs[$field_name]." ".$rs[$c_name]."</option>\n";
			}
		}	
		$rtxt.= "</select>";
		if($return=='RETURN') return $rtxt;
		else echo $rtxt;
	}
}

function get_c_no($code_name,$code){
	global $conn,$DTB_PRE;
	$lang=10; // it should be the default lang, English
	$sql = "select no from $DTB_PRE"."_$code_name where code='$code' and c_lang=$lang";
	$res = mysqli_query($conn,$sql);
	if ($res) $rs = mysqli_fetch_array($res);
	return $rs['no'];
}

function get_c_code($code_name,$no){
	global $conn,$DTB_PRE;
	$sql = "select code from $DTB_PRE"."_$code_name where no='$no'";
	$res = mysqli_query($conn,$sql);
	if ($res) $rs = mysqli_fetch_array($res);
	return $rs['code'];
}

function get_c_name($code_name,$no,$field_name=null,$opt=null,$form_name=null){ 
	global $conn,$DTB_PRE;
	$lang=$_SESSION['vwmldbm_lang'];
	if(!$conn || !$DTB_PRE || !$lang) return;
	$code=get_c_code('c_pcat',$no);
	$opt=strtoupper($opt);
	if(!$field_name)$field_name='name';
	$sql = "select $field_name from $DTB_PRE"."_$code_name where c_lang='$lang' and code='$code'";
	$res = mysqli_query($conn,$sql);
	if($res) $rs=mysqli_fetch_array($res);
	
	if(!$rs[$field_name]){ // the code name by specified user language doesn't exist. Make it default (english)
		$field_name="name";
		$lang=10; // default lang, English
		$sql = "select $field_name from $DTB_PRE"."_$code_name where c_lang='$lang' and code='$code'";
		$res = mysqli_query($conn,$sql);
		if($res) $rs=mysqli_fetch_array($res);
	}
	if($opt!="HIDDEN") $result=$rs[$field_name]; // value
	if($opt=="RD_HIDDEN"|| $opt=="HIDDEN") // hidden filed
		$result.=" <input type='hidden' name='$form_name' value='$code'>";
	return $result;
}

function print_lang($code=null, $form_name=null, $use_yn=true,$tag_inside=null) {
	global $conn,$DTB_PRE;
	if($form_name==null) $form_name='c_lang';
	if($use_yn) $use_yn=" and use_yn='Y' "; // only show code with use_yn='Y'
	else $use_yn=""; // show all codes
	$sql = "select * from $DTB_PRE"."_vwmldbm_c_lang where isNULL(code)=false $use_yn order by priority asc";
	echo "<select name='$form_name' $tag_inside>\n";
	echo "<option value='' selected>-select-</option>\n";
	$sql = "select * from $DTB_PRE"."_vwmldbm_c_lang where isNULL(code)=false $use_yn order by priority asc";
	$res = mysqli_query($conn,$sql);
	if ($res) {		
		while ($rs = mysqli_fetch_array($res)) {
			if ($code == $rs['code']) $sel=" selected";
			else $sel="";
			echo "<option value=".$rs[code]." $sel>".$rs[n_name]."</option>\n";
		}
	}	
	echo "</select>";
}

function get_lang($code) {
	global $conn,$DTB_PRE;
	$sql = "select name from $DTB_PRE"."c_lang where code='$code'";
	$res = mysqli_query($conn,$sql); 
	if ($res)	$rs = mysqli_fetch_array($res);
	return $rs[name];
}

function lang_available($code=null){
	global $conn,$DTB_PRE;
	$sql = "select name from $DTB_PRE"."c_lang where code='$code' and use_yn='Y'";
	$res = mysqli_query($conn,$sql); 
	if ($res)$rs = mysqli_fetch_array($res);
	if($rs[name]) return true;
	else return false;
}

function get_field_name($tb,$fd){
	global $conn,$DB,$DTB_PRE,$TB_PRE;
	$tb_name=$TB_PRE."_".$tb;

	$sql="select no from ".$DTB_PRE."_vwmldbm_fd where inst=$_SESSION[inst] and db_name='$DB' and tb_name='$tb_name' and field='$fd'";
	$res_t = mysqli_query($conn,$sql); 				
	if ($res_t)	$rs_t = mysqli_fetch_array($res_t);	
	
	$sql="select name from ".$DTB_PRE."_vwmldbm_fd_mlang where inst=$_SESSION[inst] and fd_no='$rs_t[no]' and c_lang='$_SESSION[vwmldbm_lang]'";
	$res_f = mysqli_query($conn,$sql); 
	if ($res_f)	$rs_f = mysqli_fetch_array($res_f);
	
	if($rs_f['name']!="") return $rs_f['name']; // in the specified language
	else {
		$sql="select name from ".$DTB_PRE."_vwmldbm_fd_mlang where inst=$_SESSION[inst] and fd_no='$rs_t[no]' and c_lang=10"; // English
		$res_f2 = mysqli_query($conn,$sql); 
		if ($res_f2)	$rs_f2 = mysqli_fetch_array($res_f2);
		if($rs_f2[name]) return $rs_f2[name];
		else return $fd;
	}
}

function js_alert($msg=null){
	$msg=addslashes($msg);
	echo "<script>alert(\"$msg\");</script>";
}
?>