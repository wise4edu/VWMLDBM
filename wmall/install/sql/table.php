<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2017 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
$table_sql['vwmldbm_c_lang']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vwmldbm_c_lang (
		code int NOT NULL,
		name varchar(100) NOT NULL,
		n_name varchar(100), -- in native language
		priority INT,
		use_yn char(1) default 'Y',
		PRIMARY KEY (code)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['c_pcat']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_c_pcat (
		no INT AUTO_INCREMENT PRIMARY KEY, 
		code INT, 
		c_lang INT, -- multi-lang code 
		name VARCHAR(100), 
		use_yn char(1) default 'Y',
		unique (code,c_lang),
		FOREIGN KEY (c_lang) REFERENCES $DTB_PRE"."_vwmldbm_c_lang(code)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['c_gender']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_c_gender (
		no INT AUTO_INCREMENT PRIMARY KEY, 
		code INT, 
		c_lang INT, -- multi-lang code 
		name VARCHAR(100), 
		use_yn char(1) default 'Y',
		unique (code,c_lang),
		FOREIGN KEY (c_lang) REFERENCES $DTB_PRE"."_vwmldbm_c_lang(code)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['customer']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_customer (
		id  INT AUTO_INCREMENT PRIMARY KEY, 
		pwd char(128) NOT NULL,
		name VARCHAR(50),
		c_gender int,
		phone CHAR(12),
		address varchar(100),
		email varchar(50),
		dob date,
		c_lang INT,
		FOREIGN KEY (c_lang) REFERENCES $DTB_PRE"."_vwmldbm_c_lang(code),
		FOREIGN KEY (c_gender) REFERENCES $DTB_PRE"."_c_gender(no)  		
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vendor']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vendor (
		id  INT AUTO_INCREMENT PRIMARY KEY,
		pwd char(128) NOT NULL,
		name VARCHAR(50),
		phone CHAR(12),
		address varchar(100),
		email varchar(50),
		c_lang INT,
		FOREIGN KEY (c_lang) REFERENCES $DTB_PRE"."_vwmldbm_c_lang(code)		
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['product']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_product (
		id INT AUTO_INCREMENT PRIMARY KEY, 
		name VARCHAR(100), 
		c_pcat INT,
		vendor INT,
		price 	FLOAT(10,2), 
		quantity  INT,
		FOREIGN KEY (vendor) REFERENCES $DTB_PRE"."_vendor(id),
		FOREIGN KEY (c_pcat) REFERENCES $DTB_PRE"."_c_pcat(no)		
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['sales']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_sales (
		no INT AUTO_INCREMENT PRIMARY KEY,
		cid INT,
		pid INT,
		quantity INT,
		sales_date DATE,
	 FOREIGN KEY (cid) REFERENCES $DTB_PRE"."_customer(id)
		ON DELETE CASCADE,
	 FOREIGN KEY (pid) REFERENCES $DTB_PRE"."_product(id)
		ON DELETE CASCADE	
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
?>
