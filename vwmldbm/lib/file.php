<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
 
/*
  Description : File related Library
*/

/*==============================================================

functions:
	return_bytes($val)
	post_max_size($opt=null)
	upload_max_filesize($opt=null)
	max_file_uploads($opt=null)
	
 ============================================================*/
namespace vwmldbm\files;

function return_bytes($val) {
// http://php.net/manual/en/function.ini-get.php
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val=(int) $val; // to remove 'M'

    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function post_max_size($opt=null) {
    if($opt=='bytes') return return_bytes(ini_get('post_max_size'));
    else return ini_get('post_max_size');
}

function upload_max_filesize($opt=null) {
    if($opt=='bytes') return return_bytes(ini_get('upload_max_filesize'));
    else return ini_get('upload_max_filesize');
}

function max_file_uploads($opt=null) {	
    return ini_get('max_file_uploads');
}

function dirStat($path,$dirArr) {
    $statArr['num']=0;
    $statArr['size']=0;
	
    return $statArr;
}

function format_fsize($n,$unit='MB',$fraction=0){
    if($unit=='KB')
        return number_format($n/1024,$fraction,'.',',');
    else if($unit=='MB')
        return number_format($n/(1024*1024),$fraction,'.',',');
    else if($unit=='GB')
        return number_format($n/(1024*1024*1024),$fraction,'.',',');
}


function get_dir_stat(&$dirStat,$fpath) {
	$ite=new \RecursiveDirectoryIterator($fpath);
	$dirStat['bytestotal']=0;
	$dirStat['nbfiles']=0;
	foreach (new \RecursiveIteratorIterator($ite) as $filename=>$cur) {
		if(!$cur->isFile()) continue;
		$filesize=$cur->getSize();
		$fname=$cur->getFileName;
		$dirStat['bytestotal']+=$filesize;
		$dirStat['nbfiles']++;
		
		//echo($cur->getRealPath().":".$cur->getFileName()."a<br>");
	}
}

function zipFilesAndDownload($file_names,$archive_file_name) { 
	global $VWMLDBM;
    $dpath=$VWMLDBM['VWMLDBM_UPLOAD_EBOOK_PATH'].$_SESSION['lib_inst']."/";
	
	$zip = new \ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open($dpath.$archive_file_name, \ZIPARCHIVE::CREATE )!==TRUE) {  // OR OVERWRITE
        exit("cannot open <$archive_file_name>\n");
    }

    foreach($file_names as $file)
    {
        $zip->addFile($file['path'],$file['fname']);
      // echo $file['fname']."<br>";

    }
	$zip->close();

	header("Content-type: application/zip"); 
	header("Content-Disposition: attachment; filename=$archive_file_name");
	header("Content-length: " . filesize($dpath.$archive_file_name));
	header("Pragma: no-cache"); 
	header("Expires: 0"); 
	ob_end_flush();
	readfile($dpath.$archive_file_name);
	unlink($dpath.$archive_file_name); 
}