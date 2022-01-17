<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han @ wise4edu.com, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm;
?>
<footer id='footer'>
	<center><p>Copyright &copy; 2014-<? echo date ("Y") ;?>  www.wise4edu.com reserved</p></center>
</footer>

<? require_once($VWMLDBM['VWMLDBM_RT']."/lib/include_jQuery.php"); // jQuery connect ?>
<div id='loading'><img id='loading-image' src='<?=$VWMLDBM['VWMLDBM_WWW_RT']?>/img/loading3.gif' alt='Loading...' /></div>
<link href="<?=$VWMLDBM['VWMLDBM_WWW_RT']."/css/loading.css?nocache=".$inst_var->cache_t?>" rel="stylesheet" type="text/css"> <!-- Loading page waiting CSS code -->