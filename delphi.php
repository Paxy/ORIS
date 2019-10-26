<?PHP

function readByte($f , $size){
$contents = fread($f , $size);
return $contents;
}

function readString($f){
fread($f , 1);
$size =  hexdec(bin2hex(fread($f , 1)));
if ($size==0) return "";
$contents = fread($f , $size);
return $contents;
}

function readInt($f){
$tip =  hexdec(bin2hex(fread($f , 1)));
$br = hexdec(bin2hex(fread($f , 1)));
if ($tip==3)
	$br=$br+hexdec(bin2hex(fread($f , 1)))*256;
if ($tip==4)
{
	$br=$br+hexdec(bin2hex(fread($f , 1)))*256;
	$br=$br+hexdec(bin2hex(fread($f , 1)))*65536;
	$br=$br+hexdec(bin2hex(fread($f , 1)))*16777216;
}
return $br;
}

function readBool($f){
$bool =  hexdec(bin2hex(fread($f , 1)));
if ($bool==9) 
return 1;
else return 0;
enfif;
}

function readFloat($f){
fread($f , 1);
$float =  fread($f , 10);
return $float;
}

function readLong($f){
$br=hexdec(bin2hex(fread($f , 1)));
$br=$br+hexdec(bin2hex(fread($f , 1)))*256;
$br=$br+hexdec(bin2hex(fread($f , 1)))*65536;
$br=$br+hexdec(bin2hex(fread($f , 1)))*16777216;
return $br;
}

function writeString($msg)
{
echo "\x06";
$num=strlen($msg);
echo pack('C', (int) $num);
echo $msg;
}

function writeInt($br)
{
if ($br<128)
	echo "\x02" . pack('C', (int) $br);
elseif ($br < 32768)
	echo "\x03" . pack("S",$br);
else
	echo "\x04" . pack("L",$br);
}

function writeBool($br)
{
if ($br>0)
	echo "\x09";
else
	echo "\x08";
}

function writeFloat()
{
echo "\x05\0\0\0\0\0\0\0\0\0\0";
}
/*
function writeFloat($br){
	if ($br>65534) die ("Najveci broj za float je 65534");
	
	if ($br==0) {
		echo "\x05\0\0\0\0\0\0\0\0\0\0";
		return;
	}
	if ($br==1) {
		echo "\x05\0\0\0\0\0\0\0\x80\xFF\x3F";
		return;
	}
	
echo "\x05";
// 8 bajtova frakcije
echo "\x00\x00\x00\x00\x00\x00";
// 6 bajtova 00, 2 bajta short
for ($i=0;$i<16;$i++)
{
if (($br & 32768)>0) break;
$br=$br << 1;
}
$i++;
$i++;

echo pack("v", $br);

// drugi deo mantise
echo pack("C", 16-$i);
echo "\x40";
	
}
*/

function writeDouble($br){
	echo pack("d",$br);	
}

?>