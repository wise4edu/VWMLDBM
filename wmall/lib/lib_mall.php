<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
 /*==============================================================
function list:
	menu()
	css($url=null)
  ============================================================*/
  
$MALL_WWW_RT=$WMALL['MALL_WWW_RT'];
$MALL_RT=$WMALL['MALL_RT'];
require_once("$MALL_RT/vwmldbm/config.php");
function menu(){
	global $wmlang;
	if($_GET['vwmldbm_lang']) $_SESSION['vwmldbm_lang']=$_GET['vwmldbm_lang'];
	global $MALL_WWW_RT;
	echo "<p>
		[<a href='$MALL_WWW_RT/customer/'>".$wmlang[menu][cust_list]."</a>] 
		[<a href='$MALL_WWW_RT/product/'>".$wmlang[menu][prod_list]."</a>] 
		[<a href='$MALL_WWW_RT/sales/'>".$wmlang[menu][sales_list]."</a>] 
		";
	\vwmldbm\code\mlang_change_list(); // multi-lang list and change the setting
	echo "</p>";
}

function css($url=null){
	global $MALL_WWW_RT;
	if(!$url) $url=$MALL_WWW_RT."/css/common.css";
	echo "<link href='$url' rel='stylesheet' type='text/css'>";
	echo "<meta charset='utf-8'>";
}
?>