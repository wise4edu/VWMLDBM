<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
$table_sql['vwmldbm_c_lang']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}vwmldbm_c_lang (
		inst int NOT NULL,
		code int NOT NULL,
		name varchar(100) NOT NULL,
		n_name varchar(100), -- in native language
		ccode char(4) default NULL, -- country code: It can be EN, EN1, EN12
		priority INT,
		use_yn char(1) default 'Y',
		PRIMARY KEY (inst,code)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_inst']="
	CREATE TABLE {$DTB_PRE}vwmldbm_inst (
	  no int NOT NULL AUTO_INCREMENT,
	  inst_uname varchar(255) NOT NULL,
	  inst_id varchar(255) default NULL,
	  secret varchar(255) default NULL,
	  host varchar(255) default NULL,
	  other_prg_login_uri text default NULL,
	  other_prg_adm text default NULL,
	  other_prg_sadm text default NULL,
	  mode varchar(255) default NULL,
	  cache_t datetime default NULL,
	  sadmin_id char(20) default NULL,
	  PRIMARY KEY (no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
			
			
$table_sql['vwmldbm_DB']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}vwmldbm_db (
	  no int NOT NULL AUTO_INCREMENT,
	  name varchar(100),
	  comment varchar(300),
	  PRIMARY KEY (no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_TB']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}vwmldbm_tb (
	  no int NOT NULL AUTO_INCREMENT,
	  db int,
	  name varchar(100),
	  type char(1) default NULL,
	  SQL_txt text,
	  creating_order int,
	  comment varchar(300),
	  PRIMARY KEY (no),
	  FOREIGN KEY(db) REFERENCES {$DTB_PRE}vwmldbm_db(no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_FD']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}vwmldbm_fd (
	  no int NOT NULL AUTO_INCREMENT,
	  db_name char(100),
	  tb_name char(100),
	  field char(100),
	  eng_name varchar(300),
	  size_x int,
	  size_y int,
	  pos_x int,
	  pos_y int,
	  type char(30),
	  max_len int,
	  ftype char(30),
	  vals varchar(300),
	  rd_staff_yn char(1),
	  rd_fac_yn char(1),
	  rd_stud_yn char(1),
	  PRIMARY KEY (no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_FD_MLANG']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}vwmldbm_fd_mlang (
	  inst int NOT NULL,
	  no int NOT NULL AUTO_INCREMENT,
	  fd_no int,
	  c_lang int,
	  name varchar(200),
	  KEY(no),
	  PRIMARY KEY (inst,no),
	  FOREIGN KEY(inst) REFERENCES {$DTB_PRE}vwmldbm_inst(no),
	  FOREIGN KEY(fd_no) REFERENCES {$DTB_PRE}vwmldbm_fd(no)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	  FOREIGN KEY(inst,c_lang) REFERENCES {$DTB_PRE}vwmldbm_c_lang(inst,code)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_rmd_tb_loc']="
	CREATE TABLE {$DTB_PRE}vwmldbm_rmd_tb_loc (
	  inst int NOT NULL,
	  tb_no int NOT NULL,
	  x_pos char(7),
	  y_pos char(7),
	  expanded_yn char(1) default '+',
	  zindex int,
	  PRIMARY KEY (inst,tb_no),
	  FOREIGN KEY(inst) REFERENCES {$DTB_PRE}vwmldbm_inst(no),
	  FOREIGN KEY(tb_no) REFERENCES {$DTB_PRE}vwmldbm_tb(no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_rmd_fkey_info']="
	CREATE TABLE {$DTB_PRE}vwmldbm_rmd_fkey_info (
	  no int NOT NULL AUTO_INCREMENT,
	  from_db varchar(100),
	  to_db varchar(100),
	  from_tb varchar(100),
	  to_tb varchar(100),
	  from_field varchar(100),
	  to_field varchar(100),
	  PRIMARY KEY (no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
$table_sql['code_cset']="
	CREATE TABLE IF NOT EXISTS {$DTB_PRE}code_cset (
	  code int NOT NULL AUTO_INCREMENT,
	  c_lang int DEFAULT 10,
	  code_name varchar(255),
	  name varchar(255),
	  en_only_yn char(1) DEFAULT NULL,
	  use_yn char(1) default 'Y',	 
	  PRIMARY KEY (code)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

/* Drop Table
	drop table sims_vwmldbm_rmd_fkey_info;
	drop table sims_vwmldbm_rmd_tb_loc;
	drop table sims_vwmldbm_fd_mlang;
	drop table sims_vwmldbm_fd;
	drop table sims_vwmldbm_tb;
	drop table sims_vwmldbm_db;
	drop table sims_vwmldbm_inst;
	drop table sims_vwmldbm_c_lang;
	drop table sims_code_cset;
	
	
drop table sims_code_cset;
drop table vwmldbm_rmd_fkey_info;
drop table vwmldbm_rmd_tb_loc;
drop table vwmldbm_fd_mlang;
drop table vwmldbm_fd;
drop table vwmldbm_tb;
drop table vwmldbm_db;
drop table vwmldbm_inst;
drop table vwmldbm_c_lang;


*/
/*
CREATE TABLE `code_major` (
  `code` int(11) NOT NULL AUTO_INCREMENT,
  `c_lang` int(11) NOT NULL DEFAULT '10',
  `name` varchar(255) DEFAULT NULL,
  `abb_name` varchar(10) DEFAULT NULL,
  `use_yn` char(1) DEFAULT 'Y',
  PRIMARY KEY (`code`,`c_lang`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

*/
?>