<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
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

$data_sql['c_pcat']="
	INSERT INTO $DTB_PRE"."_c_pcat(code,c_lang,name) 
		VALUES 
	(10,10,'CPU'),
	(20,10,'RAM'),
	(30,10,'HDD'),
	(10,30,'중앙처리장치'),
	(20,30,'메모리'),
	(30,30,'하드디스크'),
	(10,50,'中央处理器'),
	(20,50,'随机存取存储器'),
	(30,50,'硬盘驱动器');
";

$data_sql['c_gender']="
	INSERT INTO $DTB_PRE"."_c_gender(code,c_lang,name) 
		VALUES 
	(10,10,'Male'),
	(20,10,'Female'),
	(10,30,'남'),
	(20,30,'여'),
	(10,50,'男'),
	(20,50,'女');
";

$data_sql['customer']="
	INSERT INTO $DTB_PRE"."_customer(name,c_gender,phone,dob) 
		VALUES 
	('Sam Han',1,'9999-8888','1980-03-12'),
	('Paul Park',2,'7777-5555','1970-08-05'),
	('Jack Lee',1,'5555-2222','1985-03-12'),
	('Sam Kim',2,'9999-8888','1960-06-22'),
	('John Smith',1,'8888-4444','1975-03-12')
";

$data_sql['product']="
	INSERT INTO $DTB_PRE"."_product(name,c_pcat,price,quantity) 
		VALUES 
	('Intel i5',1,120,15),
	('Intel i7',1,200,10),
	('DDR3',2,100,30),
	('HDD 2TB',3,150,30)
";
?>