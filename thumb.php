<?php

error_reporting(E_ERROR | E_PARSE);

function CreateThumb($file,$maxwdt,$maxhgt, $dest) {
    list($owdt,$ohgt,$otype)=@getimagesize($file);

  switch($otype) {
    case 1:  $newimg=imagecreatefromgif($file); break;
    case 2:  $newimg=imagecreatefromjpeg($file); break;
    case 3:  $newimg=imagecreatefrompng($file); break;
    default: echo "Unkown filetype (file $file, typ $otype)"; return;
  }
    
  if($newimg) {
    if($owdt>1500 || $ohgt>1200)
            list($owdt, $ohgt) = Resample($newimg, $owdt, $ohgt, 1024,768,0);
            
    Resample($newimg, $owdt, $ohgt, $maxwdt, $maxhgt);
        
        if(!$dest) return $newimg;
        
        if(!is_dir(dirname($dest)))
            mkdir(dirname($dest));
    
    switch($otype) {
      case 1: imagegif($newimg,dest); break;     
      case 2: imagejpeg($newimg,$dest,90); break; 
      case 3: imagepng($newimg,$dest);  break; 
    }
        
        imagedestroy($newimg);
    
    chmod($dest,0644);
  }
}

function Resample(&$img, $owdt, $ohgt, $maxwdt, $maxhgt, $quality=1) { 
  if(!$maxwdt) $divwdt=0;
   else $divwdt=Max(1,$owdt/$maxwdt);
   
  if(!$maxhgt) $divhgt=0;
   else $divhgt=Max(1,$ohgt/$maxhgt);
    
  if($divwdt>=$divhgt) {
    $newwdt=$maxwdt;
    $newhgt=round($ohgt/$divwdt);
  } else {
    $newhgt=$maxhgt;
    $newwdt=round($owdt/$divhgt);
  }
    
    $tn=imagecreatetruecolor($newwdt,$newhgt);
    if($quality)
        imagecopyresampled($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);        
    else 
        imagecopyresized($tn,$img,0,0,0,0,$newwdt,$newhgt,$owdt,$ohgt);

    imagedestroy($img);
    
    $img = $tn;
    
    return array($newwdt, $newhgt);
}

$fulldir="{$_SERVER['DOCUMENT_ROOT']}/karte/";


$d = @dir($fulldir) or die("getImages: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
      # skip hidden files
      if($entry[0] == ".") continue;

if (!strpos($entry,".jpg")) 
	if (!strpos($entry,".JPG")) continue;

$src = "$fulldir$entry";
$dst ="$fulldir"."thumb/$entry";
if (file_exists($dst)) continue;
CreateThumb($src,200,200, $dst);
//echo "Processed: ".$entry."<br>";
}

//echo "ok";

?>