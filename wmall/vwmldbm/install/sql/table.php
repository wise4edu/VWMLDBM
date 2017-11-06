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

$table_sql['vwmldbm_inst']="
	CREATE TABLE $DTB_PRE"."_vwmldbm_inst (
	  no int NOT NULL AUTO_INCREMENT,
	  PRIMARY KEY (no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_DB']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vwmldbm_db (
	  inst int NOT NULL,
	  no int NOT NULL AUTO_INCREMENT,
	  name varchar(100),
	  comment varchar(300),
	  PRIMARY KEY (no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_TB']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vwmldbm_tb (
	  inst int NOT NULL,
	  no int NOT NULL AUTO_INCREMENT,
	  DB int,
	  name varchar(100),
	  type char(1) default NULL,
	  SQL_txt text,
	  creating_order int,
	  comment varchar(300),
	  PRIMARY KEY (no),
	  FOREIGN KEY(DB) REFERENCES $DTB_PRE"."_vwmldbm_db(no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_FD']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vwmldbm_fd (
	  inst int NOT NULL,
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
	  PRIMARY KEY (no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_FD_MLANG']="
	CREATE TABLE IF NOT EXISTS $DTB_PRE"."_vwmldbm_fd_mlang (
	  inst int NOT NULL,
	  no int NOT NULL AUTO_INCREMENT,
	  fd_no int,
	  c_lang int,
	  name varchar(200),
	  PRIMARY KEY (no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no),
	  FOREIGN KEY(fd_no) REFERENCES $DTB_PRE"."_vwmldbm_fd(no)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	  FOREIGN KEY(c_lang) REFERENCES $DTB_PRE"."_vwmldbm_c_lang(code)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_rmd_tb_loc']="
	CREATE TABLE $DTB_PRE"."_vwmldbm_rmd_tb_loc (
	  inst int NOT NULL,
	  tb_no int NOT NULL,
	  x_pos char(7),
	  y_pos char(7),
	  expanded_yn char(1) default '+',
	  zindex int,
	  PRIMARY KEY (tb_no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no),
	  FOREIGN KEY(tb_no) REFERENCES $DTB_PRE"."_vwmldbm_tb(no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$table_sql['vwmldbm_rmd_fkey_info']="
	CREATE TABLE $DTB_PRE"."_vwmldbm_rmd_fkey_info (
	  inst int NOT NULL,
	  no int NOT NULL AUTO_INCREMENT,
	  from_db varchar(100),
	  to_db varchar(100),
	  from_tb varchar(100),
	  to_tb varchar(100),
	  from_field varchar(100),
	  to_field varchar(100),
	  PRIMARY KEY (no),
	  FOREIGN KEY(inst) REFERENCES $DTB_PRE"."_vwmldbm_inst(no)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

?>
