<?PHP
/*VWMLDBM DISCLAIMER*==================================================
 Copyright (c) 2022 Sang Jin Han and other contributors, 
	http://wise4edu.com
 Released under the MIT license
 =================================================*VWMLDBM DISCLAIMER*/
namespace vwmldbm\img;

function compress($source, $destination, $isize, $re_size=50000) { // change the color 
		$info = getimagesize($source);
		if ($info['mime'] == 'image/jpeg') 
			$image = imagecreatefromjpeg($source);

		elseif ($info['mime'] == 'image/gif') 
			$image = imagecreatefromgif($source);

		elseif ($info['mime'] == 'image/png') 
			$image = imagecreatefrompng($source);		
		
		if($isize>$re_size) 
			imagejpeg($image, $destination, $re_size/$isize*100);
		else imagejpeg($image, $destination, $isize);
		return $destination;
}	

function img_resize($tmp_name,$new_name,$new_width){ // change the resolution
    list($width, $height) = getimagesize($tmp_name);

	$new_height = abs($new_width * $height / $width);
    $image_p = imagecreatetruecolor($new_width, $new_height);
    $image = imagecreatefromjpeg($tmp_name); 
    imagecopyresampled($image_p, $image, 0, 0, 0, 0,$new_width, $new_height, $width, $height); 
    imagejpeg($image_p,$new_name); 
    return $new_name;
}

function genRandStr($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>