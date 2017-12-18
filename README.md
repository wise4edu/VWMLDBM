# VWMLDBM 
VWMLDBM stands for Visual Web Multi-Language Database Manager.
It is a multi-language supporting plug-in and Relational Model Diagramming tool for Mysql(MariaDB)/PHP web applications.

To install VWMLDBM, you should have PHP5.5(or higher) and mysql 5.1(or higher).
The host operating system can be Windows or Linux(directory permission should be checked).

A. Install VWMLDBM:
 1. Copy all files (located under "wmall/") into a directory, e.g., yourdomainname/host_program/vwmldbm
 2. From a web browser,run "yourdomainname/host_program/vwmldbm"
 3. For reinstallation, 
      delete "wmall/host_program/vwmldbm/dbcon.php" and copy and rename "host_program/vwmldbm/dbcon(default).php" to "wmall/vwmldbm/dbcon.php".
    
B. Install Sample Host System with VWMLDBM:
 1. Copy all files into a directory, e.g., yourdomainname/wmall/
 2. From a web browser,run "yourdomainname/wmall/"
 3. For reinstallation, 
    a. delete "wmall/common/dbcon.php" and copy and rename "wmall/common/dbcon(default).php" to "wmall/common/dbcon.php". 
    b. delete "wmall/vwmldbm/dbcon.php" and copy and rename "wmall/vwmldbm/dbcon(default).php" to "wmall/vwmldbm/dbcon.php".


C. To use multi-lang change list box,
  1. include VWMLDBM "config.php" from the host script. 
	eg, Suppose the host script is "/htdocs/host_program/customer/index.php"
		and VWMLDBM path is "/htdocs/host_program/vwmldbm/".	
		From the host script, " require_once("../vwmldbm/config.php"); "
  
  2. call "\vwmldbm\code\mlang_change_list();"
	
	
D. To use multi-lang field names,
  1. Enter field names using "RMD"
  
  2. include VWMLDBM "config.php" from the host script. 
	eg, Suppose the host script is "/htdocs/host_program/customer/index.php"
		and VWMLDBM path is "/htdocs/host_program/vwmldbm/".	
		From the host script, " require_once("../vwmldbm/config.php"); "
  
  3. call "\vwmldbm\code\get_field_name("table_name_without_prefix","field_name")"
		
	
E. To use multi-lang Texts (not field names),
  1. Modify JSON files: eg, "/htdocs/host_program/vwmldbm/mlang/30.json" for Korean:
  2. include VWMLDBM "config.php" from the host script. 
	eg, Suppose the host script is "/htdocs/host_program/customer/index.php"
		and VWMLDBM path is "/htdocs/host_program/vwmldbm/".	
		From the host script, " require_once("../vwmldbm/config.php"); "
  
  3. insert code: "$wmlang[menu][customer_list]"
		
