<?php

$ctrl="99";
$tak=7;

$data= file_get_contents("php://input");

$file = 'log.txt';

echo "\xac\xed\x00\x05\x73\x72\x00\x10\x6a\x61\x76\x61\x2e\x75\x74\x69" .
"\x6c\x2e\x56\x65\x63\x74\x6f\x72\xd9\x97\x7d\x5b\x80\x3b\xaf\x01" .
"\x03\x00\x03\x49\x00\x11\x63\x61\x70\x61\x63\x69\x74\x79\x49\x6e" .
"\x63\x72\x65\x6d\x65\x6e\x74\x49\x00\x0c\x65\x6c\x65\x6d\x65\x6e" .
"\x74\x43\x6f\x75\x6e\x74\x5b\x00\x0b\x65\x6c\x65\x6d\x65\x6e\x74" .
"\x44\x61\x74\x61\x74\x00\x13\x5b\x4c\x6a\x61\x76\x61\x2f\x6c\x61" .
"\x6e\x67\x2f\x4f\x62\x6a\x65\x63\x74\x3b\x78\x70\x00\x00\x00\x00" .
"\x00\x00\x00\x03\x75\x72\x00\x13\x5b\x4c\x6a\x61\x76\x61\x2e\x6c" .
"\x61\x6e\x67\x2e\x4f\x62\x6a\x65\x63\x74\x3b\x90\xce\x58\x9f\x10" .
"\x73\x29\x6c\x02\x00\x00\x78\x70\x00\x00\x00\x0a\x74\x00\x02\x4f" .
"\x4b\x74\x00\x01\x31\x74\x00\x05\x31\x32\x30\x36\x00\x70\x70\x70" .
"\x70\x70\x70\x70\x78";

file_put_contents($file,$data."\n" , FILE_APPEND | LOCK_EX);

$today = date("Y-m-d");                  

$idx=strpos($data,$today);
if ($idx === false) return;

$idx-=7;

$idxend=strpos($data,"pppppppppxpppppppx",$idx);
if ($idxend === false) return;

$data=substr($data,$idx,$idxend-$idx);

$pieces = explode("\x00", $data);
$ctrl=substr($pieces[0],1);
$datum=substr($pieces[1],1);
$si=substr($pieces[4],1);

file_put_contents($file,$datum." - ".$si."\n" , FILE_APPEND | LOCK_EX);


require_once( '../functions.php' ); 
$sql=mysqlConnect("");
$result = mysql_query("INSERT INTO `srr` (`tak`, `datum`, `si`,`ctrl`) VALUES ('".$tak."', '".$datum."', '".$si."', '".$ctrl."');", $sql);

?>