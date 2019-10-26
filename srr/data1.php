<?php

$ctrl="99";
$tak=7;

$data= file_get_contents("log.txt");

$today = date("Y-m-d");                  

$idx=strpos($data,$today);
if ($idx === false) return;

$idx-=7;

$idxend=strpos($data,"pppppppppxpppppppx",$idx);
if ($idxend === false) return;

$data=substr($data,$idx,$idxend-$idx);

$pieces = explode("\x00", $data);
$datum=substr($pieces[1],1);
$si=substr($pieces[4],1);
echo $data;
print_r($pieces);

?>
