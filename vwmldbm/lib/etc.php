<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
/*
function list:
	str_limit($string,$limit_length,$add_string)
	genPWD($length, $strength)
	short_txt($txt,$len)
	validate_username($uname)
	validate_pwd($p)
	checkContPassword($s)	
	validate_email($email)
	validate_url($website)
	makeup_4HTML($data) 
	valid_ip($ip)
	get_client_ip()
	return_bytes($val)
	get_time_zone($z=null,$form_name='time_zone',$opt=null)
	ajax_js($opt=null)
	validate_date($date, $format = 'Y-m-d')
	dir_size($dir)
	print_print_js($func='PlanPrint')
	print_paper_size($code='A4',$form_name='paper_size',$fevent=null)
	print_paper_orientation($code='portrait',$form_name='paper_orientation',$fevent=null)
	print_page_style($paper='A4',$size='portrait',$pMargin='0cm',$lMargin='1.0cm',$rMargin='1.0cm',$bMargin='1.0cm')
	print_excel_tag($fname)
	print_nums($form_name,$num=0,$lower=0,$upper=10,$fevent=null)
	print_tab_border_opt($form_name='form1',$fevent=null)
	two_way_encrypt($plaintext,$key=null)
	two_way_decrypt($ciphertext,$key=null)
	day_to_num($d)
	print_year($form_name,$code=null)
	arr_to_list($arr,$opt='SQL',$quot=null)
 ============================================================*/
namespace vwmldbm\etc;
function str_limit($string,$limit_length,$add_string){ 
	$full_length=strlen($string);
	for($k=0; $k<$limit_length-1; $k++){
		if(ord(substr($string, $k, 1))>127) $k++;
	}
	if ($full_length > $limit_length){
		$final_string=substr($string, 0, $k).$add_string;
	} else {
		$final_string=$string;
	}
	return $final_string;
}

function genPWD($length=9, $strength=3) {
//http://www.webtoolkit.info/php-random-password-generator.html
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	
	if ($strength >= 1) {
		$consonants .= '123456789';
		$vowels = '0123456789';
	}
	if ($strength >= 2) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength >= 3) {
		$consonants .= '@#$%*&^!~';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

function validate_pwd($p){
	global $WISE;
	if(!$WISE['min_pwd']) $WISE['min_pwd']=8;
	$pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{{$WISE['min_pwd']},}$/";
	return (preg_match($pattern, $p) && checkContPassword($p));
}

function checkContPassword($s) {
    // Check for 3 consecutive alphabetical characters
    for($i=0;$i<strlen($s)-2;$i++){ 
		if(is_numeric($sp[$i]) && is_numeric($sp[$i]) && is_numeric($sp[$i])){ // three numbers
			if($sp[$i]==$sp[$i+1]-1 && $sp[$i+1]==$sp[$i+2]-1) return false;
		}
		else if(!is_numeric($sp[$i]) && !is_numeric($sp[$i+1]) && !is_numeric($sp[$i+1])){ // three non-numbers
			if($sp[$i]==chr(ord($sp[$i+1])-1) && $sp[$i+1]==chr(ord($sp[$i+2])-1)) return false;
		}
	}		
	
    // Check for 3 same consecutive numbers
    for($i=0;$i<strlen($s)-2;$i++){ 
        if ($s[$i+1] == $s[$i] && 
            $s[$i+2] == $s[$i]) return false;		
	}		
    return true;
}

function short_txt($txt,$len){   // Sam Han 2013.10.25, eg, short_txt($txt,10)
	if(strlen($txt)-2>$len) { 
		$txt=substr($txt,0,$len); 
		$txt.="..";
	}
	return $txt;
}

function validate_username($uname){
	$pattern="/.*[\s~`@#$%^&*\(\)+=\/*+\\:;,<>?'\"{}\[\]|]+.*/";
	if(preg_match($pattern,$uname)) {
		return false;
	}
	else return true;
}

function validate_ID($id){
	$pattern="/.*[\s~`$%^\(\)+=\/+\\:;,<>?'\"{}\[\]|]+.*/";
	if(preg_match($pattern,$id)){
		return false;
	}
	else return true;
}

function validate_email($email){  //http://www.w3schools.com/php/php_form_url_email.asp
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
	{
		$emailErr = "Invalid email format";
		return false;
	}
	else return true;
}

function validate_url($website){  //http://www.w3schools.com/php/php_form_url_email.asp
	if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website))
	{
		$emailErr = "Invalid email format";
		return false;
	}
	else return true;
}

function makeup_4HTML($data) {  //http://www.w3schools.com/php/php_form_validation.asp
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Function to get the user IP address
//http://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
function valid_ip($ip) {
    // for list of reserved IP addresses, see https://en.wikipedia.org/wiki/Reserved_IP_addresses
    return $ip && substr($ip, 0, 4) != '127.' && substr($ip, 0, 4) != '127.' && substr($ip, 0, 3) != '10.' && substr($ip, 0, 2) != '0.' ? $ip : false;
}

function get_client_ip() {
    // using explode to get only client ip from list of forwarders. see https://en.wikipedia.org/wiki/X-Forwarded-For
    return
    @$_SERVER['HTTP_X_FORWARDED_FOR'] ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'], 2)[0] :
    @$_SERVER['HTTP_CLIENT_IP'] ? explode(',', $_SERVER['HTTP_CLIENT_IP'], 2)[0] :
    valid_ip(@$_SERVER['REMOTE_ADDR']) ?:
    'UNKNOWN';
}

function return_bytes($val) {
// http://php.net/manual/en/function.ini-get.php
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= (1024 * 1024 * 1024); //1073741824
            break;
        case 'm':
            $val *= (1024 * 1024); //1048576
            break;
        case 'k':
            $val *= 1024;
            break;
    }
    return $val;
}

function get_time_zone($z=null,$form_name='time_zone',$opt=null){
	global $perm;
	$tzone_arr=array();
	foreach(\DateTimeZone::listAbbreviations() as $timezone){
        foreach($timezone as $val){
			if(isset($val['timezone_id'])){
				if(array_search($val['timezone_id'],$tzone_arr)===false) array_push($tzone_arr,$val['timezone_id']);
			}
        }
	}
	$txt=null;
	
	asort($tzone_arr);
	if($opt=='RD_ONLY' || $perm['M']!='Y'){
		foreach($tzone_arr as $val){
			if(isset($val)){
				if($z==$val) $txt=$val;
			}
		}
	}
	else {
		$txt= "<select name='$form_name'>";
		$txt.= "<option value=''> -- Select -- </option>";
		foreach($tzone_arr as $val){
			if(isset($val)){
				$sel="";
				if($z==$val) $sel=" selected";
				$txt.= "<option $sel>$val</option>";
			}
		}
		$txt.= "</select>";
	}
	return $txt;
}

function ajax_js($opt=null){
	global $WISE,$wise_path;
	if(!$wise_path)$wise_path=$WISE['wise_rt'];
	require_once($wise_path."/lib/include_jQuery.php");
	if($opt!='no_loading' && $WISE) {
		require_once($WISE['wise_rt']."/lib/include_jQuery.php");
		echo "
		<div id='loading'><img id='loading-image' src='".$WISE['www_rt']."/img/icon/loading3.gif' alt='Loading...' /></div>
		<link href='".$WISE['www_rt']."/common/css/loading.css?nocache=3' rel='stylesheet' type='text/css'>
		";
		echo "<script src='".$WISE['www_rt']."/lib/js/loading.js'></script>";
		echo "<script>\$('#loading').hide();</script>";
		$load_show="\$('#loading').show();";
		$load_hide="\$('#loading').hide();";
	}
	else {
		$$load_show="";
		$load_hide="";
	}
	echo "
		<script>
		function run_ajax(script_name,data_to_pass,method,func){
			if(!method) method='POST';
			\$.ajax({
				type: method,
				url: script_name,
				data: data_to_pass,
				async: true,
				cache: false,
				beforeSend: function(xhr){      
					$load_show
				},
				
				success: function(result){
					if(result){	
						func(result);
					}
					$load_hide					
				},
				error: function(XMLHttpRequest,textStatus,errorThrown) {
					//console.log('error: '+textStatus + '  '+ errorThrown  );
					$load_hide
					//func(result);
				}
			});
		}
	</script>
	";
}

function validate_date($date, $format = 'Y-m-d') { // $format='Y-m-d H:i:s'
    if($date=='0000-00-00') return true;
	$d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function dir_size($dir){
//https://stackoverflow.com/questions/478121/how-to-get-directory-size-in-php
	$dirSize = 0;
	if(!is_dir($dir)){return false;};
	$files = scandir($dir);if(!$files){return false;}
	$files = array_diff($files, array('.','..'));

	foreach ($files as $file) {
		if(is_dir("$dir/$file")){
			 $dirSize += dir_size("$dir/$file");
		}else{
			$dirSize += filesize("$dir/$file");
		}
	}
	return $dirSize;
}

function print_print_js($func='PlanPrint'){
	return "
	<script>
		function ".$func."() {
			document.all.imgPrint.style.display = 'none';
			var img_button=document.getElementsByClassName('img_button');
			for(var i=0;i<img_button.length;i++) img_button[i].style.display='none';
			
			var hideMe=document.getElementsByClassName('hideMe');
			for(var i=0;i<hideMe.length;i++) hideMe[i].style.display='none';
			
			var no_print=document.getElementsByClassName('no_print');
			for(var i=0;i<no_print.length;i++) no_print[i].style.display='none';
			
			if(document.getElementById('pborder')!==null){
				var prevBorderStyle=document.getElementById('pborder').style.borderStyle;
				document.getElementById('pborder').style.borderStyle='none';				
			}
			
			var hideBorder=document.getElementsByClassName('hideBorder');
			for(var i=0;i<hideBorder.length;i++) hideBorder[i].style.borderStyle='none';
			
			window.print();
			
			document.all.imgPrint.style.display = '';
			var img_button=document.getElementsByClassName('img_button');
			for(var i=0;i<img_button.length;i++) img_button[i].style.display='';
						
			for(var i=0;i<hideMe.length;i++) hideMe[i].style.display='inline-block';
			
			for(var i=0;i<no_print.length;i++) no_print[i].style.display='inline-block';
			
			// if(document.getElementById('pborder')!==null)
				// document.getElementById('pborder').style.borderStyle=prevBorderStyle;
			
			var hideBorder=document.getElementsByClassName('hideBorder');
			for(var i=0;i<hideBorder.length;i++) hideBorder[i].style.borderStyle='';
		}
	</script>
	";
}

function print_paper_size($code='A4',$form_name='paper_size',$fevent=null){		
	global $wmlang;
	if($code=='A3') $A3Sel=' selected'; 
	else if($code=='Letter') $LetterSel=' selected'; 
	else if(!$code || $code=='A4') $A4Sel=' selected'; 
	
	$rtxt.= "<select name='$form_name' $fevent>\n";
	$rtxt.= "<option value=''>-select-</option>";
	
	$rtxt.= "<option value='A4' $A4Sel>".$wmlang['txt']['a4']."</option>";	
	$rtxt.= "<option value='Letter' $LetterSel>".$wmlang['txt']['letter']."</option>";
	$rtxt.= "<option value='A3' $A3Sel>".$wmlang['txt']['a3']."</option>";
	
	$rtxt.="</select>";
	return $rtxt;
}

function print_paper_orientation($code='portrait',$form_name='paper_orientation',$fevent=null){		
	global $wmlang;
	if($code=='landscape') $lSel=' selected'; 
	else $pSel=' selected'; 
	
	$rtxt.= "<select name='$form_name' $fevent>\n";
	$rtxt.= "<option value=''>-select-</option>";
	
	$rtxt.= "<option value='portrait' $pSel>".$wmlang['txt']['portrait']."</option>";
	$rtxt.= "<option value='landscape' $lSel>".$wmlang['txt']['landscape']."</option>";

	$rtxt.="</select>";
	return $rtxt;
}

function print_page_style($paper='A4',$size='portrait',$pMargin='0cm',$lMargin='1.0cm',$rMargin='1.0cm',$bMargin='0.0cm') {
	if($paper=='A4') { 
		$w='21cm';
		$h='29.7cm';
		$pw='595px';
		$ph='842px';
	}
	else if($paper=='A3') { 
		$w='29.7cm';
		$h='42.0cm';
		$pw='842px';
		$ph='1190px';
	}
	else if($paper=='Letter') { 
		$w='21.59cm';
		$h='27.94cm';
		$pw='612px';
		$ph='792px';
	}
	
	return "	
	<style>
	@page{
		margin: $pMargin;
		margin-left: $lMargin; 
		margin-right: $rMargin; 
		width: $w;
		height: $h;
		size: $size; 
		box-shadow: 0;
	}
	
	body { 
		margin-top: $bMargin;
		box-shadow: 0cm;
	}
	
	#pborder {
		border-style: dotted;
		min-width: $pw;
		min-height: $ph;
		max-width: $pw;
		max-height: $ph;
		
		// position: fixed; /* Sit on top of the page content */
		// top: 0; 
		// left: 0;
		// right: 0;
		// bottom: 0;
		//background-color: rgba(0,0,0,0.5); /* Black background with opacity */
		// z-index: -10; /* Specify a stack order in case you're using a different order for 
	}
	</style>
	";
}

function print_excel_tag($fname){
	global $WISE,$sys_var;
	$rval= "
	<script>
	function down_exel_process(){
		showTHeader(down_excel);	
	}

	function down_excel(f) {		
		$('#Gtable').table2excel({
			exclude: '.exclude_this',
			exclude_inputs: true,
			filename: '$fname.xls'
		});
		hideTHeader();
	}
	function showTHeader(f){
		var hiddenTr=document.getElementsByClassName('transcriptHeader');
		for(var i=0;i<hiddenTr.length;i++) hiddenTr[i].style.display='inline';
		f();
	}
	function hideTHeader(){
		var hiddenTr=document.getElementsByClassName('transcriptHeader');
		for(var i=0;i<hiddenTr.length;i++) hiddenTr[i].style.display='none';
	}
	</script>

	<script src='".$WISE['www_rt']."/lib/jquery/jquery.table2excel.min.js?nocache=".$sys_var->cache_t."'></script>
	";
	return $rval;
}

function print_nums($form_name,$num=0,$lower=0,$upper=10,$fevent=null,$opt=null){		
	if($opt=='RD_ONLY') return $num;
	$rtxt.= "<select name='$form_name' $fevent>";
	$rtxt.= "<option value=''>-select-</option>";
	for($i=$lower;$i<=$upper;$i++){		
		if($num==$i) $sel=" selected";
		else $sel="";		
		$rtxt.= "<option value='$i' $sel>$i</option>";	
	}
	$rtxt.="</select>";
	return $rtxt;
}

function print_tab_border_opt($form_name='form1',$fevent=null){
	global $wmlang;
	if($_POST['tab_border_opt']=='N'){
		$_SESSION['tab_border']='N';
		$selN=' selected';
	}
	else if($_POST['tab_border_opt']=='Y'){
		$_SESSION['tab_border']='Y';
		$selY=' selected';
	}
	else if($_SESSION['tab_border']=='N'){
		$selN=' selected';
	}
	else {
		$selY=' selected';
	}
	/*else if($_POST['tab_border_opt']=='Y' || !$_POST['tab_border_opt'] || $_SESSION['tab_border']=='Y' || !$_SESSION['tab_border']) {
		$_SESSION['tab_border']='Y';
		$selY=' selected';
	}*/

	if(!$fevent) $fevent=" onChange=\"document.$form_name.submit().value='retrieve'\"";
	$rtxt.= "<select name='tab_border_opt' class='hideMe' $fevent style='background:GreenYellow ;'>";
		$rtxt.= "<option value='Y' $selY>".$wmlang['txt']['show_border']."</option>";
		$rtxt.= "<option value='N' $selN>".$wmlang['txt']['hide_border']."</option>";
	$rtxt.= "</select>";
	return $rtxt;
}

function two_way_encrypt($plaintext,$key=null){
	global $sys_var;
	// https://secure.php.net/openssl_encrypt
	
	try {
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
	}
	catch(Exception $e){
		//echo "<p><font color=red>$e</font></p>";
	}
	return $ciphertext;
}

function two_way_decrypt($ciphertext,$key=null){
	// https://secure.php.net/openssl_encrypt
	try {
		$c = base64_decode($ciphertext);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
		{
			return $original_plaintext;
		}
	}
	catch(Exception $e){
		//echo "<p><font color=red>$e</font></p>";
	}
}

function day_to_num($d){
	switch ($d){
		case 'Mon': return 1;
		case 'Tue': return 2;
		case 'Wed': return 3;
		case 'Thu': return 4;
		case 'Fri': return 5;
		case 'Sat': return 6;
		case 'Sun': return 0;
		default: return null;
	}
}

function num_to_day($d){
	switch ($d){
		case 1: return 'Mo';
		case 2: return 'Tu';
		case 3: return 'We';
		case 4: return 'Th';
		case 5: return 'Fr';
		case 6: return 'Sa';
		case 0: return 'Su';
		default: return null;
	}
}

function print_year($form_name=null,$code=null,$from_yr,$to_yr,$form_action=null){
	if(!$form_name) $form_name='year';
	$rval="<select name='$form_name' $form_action>";
		
	for($yr=$from_yr;$yr<=$to_yr;$yr++) {
		if($yr==$code) $sel=' selected ';
		else $sel='';
		$rval.="<option value='$yr' $sel>$yr</option>";
	}

	$rval.="</select>";
	return $rval;
}

function print_month($form_name=null,$code=null,$from_m=1,$to_m=12,$form_action=null){
	if(!$form_name) $form_name='month';
	$rval="<select name='$form_name' $form_action>";
	if($from_m<1) $from_m=1;	
	if($to_m<1) $to_m=12;	
	for($m=$from_m;$m<=$to_m;$m++) {
		$m=(int)$m;
		$code=(int)$code;
		
		if($m==$code) $sel=' selected ';
		else $sel='';
		
		$rval.="<option value='$m' $sel>$m</option>";
	}

	$rval.="</select>";
	return $rval;
}

function print_wk($form_name=null,$code=null,$from_w=1,$to_w=5,$form_action=null){
	global $wmlang;
	if(!$form_name) $form_name='week';
	$rval="<select name='$form_name' $form_action>";
		
	for($w=$from_w;$w<=$to_w;$w++) {
		$w=(int)$w;
		$code=(int)$code;
		
		if($w==$code) $sel=' selected ';
		else $sel='';
		
		$rval.="<option value='$w' $sel>$w</option>";
	}

	$rval.="</select>";
	return $rval;
}

function average($arr,$fraction=1){
	$cnt = count($arr);
	if(!$cnt) return;
	$average = array_sum($arr)/$cnt;
	return round($average,$fraction);
}

function stdev($arr,$fraction=1){
	$cnt = count($arr);
	if(!$cnt) return;
	$variance = 0.0;

	$average = array_sum($arr)/$cnt;

	foreach($arr as $i)	{
		$variance += pow(($i - $average), 2);
	}

	return round(sqrt($variance/$cnt),$fraction);
}

function arr_to_list($arr,$opt='SQL',$quot=null){
	if(!$arr) return;
	$rval=null;
	if($quot=='DOUBLE_QUOT') $q="\"";
	else $q="'";
	if($opt=='SQL'){
		foreach($arr as $val) $rval.="$q$val$q,";
	}
	else if($opt=='KEY_SQL'){
		foreach($arr as $key =>$val) $rval.="$q$key$q,";
	}
	else if($opt=='KEY') foreach($arr as $key => $val) $rval.="$key,";
	else foreach($arr as $val) $rval.="$val,";
	
	if($rval) return mb_substr($rval,0,-1);
}

function to_mong_num($num=null){	
	$num=$num+hexdec(1810); 
	return mb_convert_encoding('&#'.$num.';', 'UTF-8', 'HTML-ENTITIES');
}

function convert_mong_num($str=null){
	if($_SESSION['lang']!=25) return $str;
	$str=(string) $str;
	$rval=null;
	for($i=0;$i<strlen($str);$i++){
		if(is_numeric($str[$i])) {
			$rval.=to_mong_num($str[$i]);
		}
		else $rval.=$str[$i];
	}
	return $rval;
}
?>