<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
$data_sql['vwmldbm_inst']="
	INSERT INTO $DTB_PRE"."_vwmldbm_inst(no) 
		VALUES (1)
";

$data_sql['vwmldbm_c_lang']="
	INSERT INTO $DTB_PRE"."_vwmldbm_c_lang(code,name,n_name,use_yn) 
		VALUES 
	(10,'English','English','Y'),
	(20,'Mongolian','Монгол хэл','N'),
	(30,'Korean','한국어','Y'),
	(40,'Russian','русский','N'),
	(50,'Chinese','中文（普通话）','Y'),
	(60,'Persian','فارسی','N'),
	(70,'Thai','ไทย','N');
";


?>