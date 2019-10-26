<?PHP

function getGodine($dir){
	 $retval = array();
	$fulldir = "{$_SERVER['DOCUMENT_ROOT']}/$dir";
	 $d = @dir($fulldir) or die("getImages: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
    	if(!in_array(godina("$entry"),$retval)) 
    		$retval[] =godina("$entry");
    }
    
    $d->close();

    return $retval;
}

 function getImages($dir,$god=0)
  {
    $imagetypes = array("image/jpeg", "image/gif");

    # array to hold return value
    $retval = array();

    # add trailing slash if missing
    if(substr($dir, -1) != "/") $dir .= "/";

    # full server path to directory
    $fulldir = "{$_SERVER['DOCUMENT_ROOT']}/$dir";

    $d = @dir($fulldir) or die("getImages: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
      # skip hidden files
      if($entry[0] == ".") continue;

      # check for image files
      if(in_array(mime_content_type("$fulldir$entry"), $imagetypes)) {
      	if ($god==0 || strpos($entry, $god)!== false)
        $retval[] = array(
         "file" => "../$dir$entry",
	  "thumb" => "../$dir"."thumb/$entry",
         "size" => getimagesize("$fulldir$entry"),
	  "filesize" =>  format_size(filesize("$fulldir$entry")),
	  "godina" =>  godina("$entry")

        );
      }
    }
    $d->close();

    return $retval;
  }
function format_size($size, $round = 0) { 
    //Size must be bytes! 
    $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'); 
    for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024; 
    return round($size,$round).$sizes[$i]; 
} 

function godina($file){

$idx=strpos($file,"20");
if (!$idx) $idx=strpos($file,"19");
if (!$idx) return "";
return substr($file,$idx,4);

}
/*
 function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
*/
function godsort($a,$b)
{
//return strcmp($b["godina"], $a["godina"]);
return intval($b["godina"])>intval($a["godina"]);
}

error_reporting(E_ERROR | E_PARSE);

include "hedder.php";

echo "<a href=karte.php>Prikazi sve</a><br>Izaberi godinu:<br>";

$godine=getGodine("karte");
sort($godine,SORT_STRING);
$godine=array_reverse($godine);
//print_r($godine);

foreach($godine as $g) {
	if (strlen($g)<1) continue;
	echo "|<a href=karte.php?god=".$g.">".$g."</a>| ";
}

echo "<hr><br><br>";

echo "
<style type=\"text/css\">
 .photo {
    float: center;
    margin: 0.5em;
    border: 1px solid #ccc;
    padding: 1em;
    font-size: 10px;
  }
</style>";

if (IsSet($_GET['god'])) $god=$_GET['god'];
else $god=0;

$images = getImages("karte",$god);

//if (IsSet($_GET['god'])) usort($images,"godsort");
//else sort($images);

echo "<table><tr>";
$br=0;
foreach($images as $img) {
    if ($br>2) 
	{
	echo "</tr>\n<tr>";
	$br=0;
	}
    echo "<td><div class=\"photo\" align=center>";
    echo "<a href=\"{$img['file']}\">",basename($img['file']),"<br><img src=\"{$img['thumb']}\"  alt=\"\"><br>\n";
    echo "</a>\n";
    echo "({$img['size'][0]}x{$img['size'][1]}) - {$img['filesize']}";
    echo "</div>\n";
    echo "</td>\n";
    $br=$br+1;
  }

echo "</tr></table>";

echo '
<script>


function checkfile()
{
var f = document.getElementById("file").value;
var filelength = parseInt(f.length) - 3;
var fileext = f.substring(filelength,filelength + 3);
if (fileext.toLowerCase() != "jpg"){
alert ("Mora biti JPEG fajl");
return false;
}
if (f.indexOf("19")==-1 && f.indexOf("20")==-1)
{
alert ("U imenu fajla se mora nalaziti godina izdanja karte");
return false;
}

//alert (document.getElementById("file").value);
formObj.submit();
}
</script>

<form action="uploadKarte.php" method="post" enctype="multipart/form-data" name="formObj" > 
  <br><br><h2>Upload karte:</h2><br>
  <input type="file" name="attachement" id="file" ></input> 
  <input type="button" value="Posalji" onClick="checkfile()"></input> 

</form>'; 


include "footer.php";

?>