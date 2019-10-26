<?php

include_once("functions.php");
$mysql=mysqlConnect();
$result=mysqli_query($mysql,"TRUNCATE sv_splits");

$file=dirname(__FILE__).'/upload/splits.csv';
$csv=csv_to_array($file,";");

$colSi=3;
$colFName=6;
$colSName=5;
$colStart=11;
$colTime=13;
$colClass=25;
$colNrCont=56;
$colFinish=59;
$colFrstCont=60;
$colPos=57;

foreach ($csv as $rec) {
	$si=$rec[$colSi];
	if(!is_numeric($si)) continue;
	$fname=fix($rec[$colFName]);
	$sname=fix($rec[$colSName]);
	$start=toSec($rec[$colStart]);
	if($start==0) continue;
	$time=toSec($rec[$colTime]);
	if($time==0) continue;
	$class=$rec[$colClass];
	$class=str_replace(chr(142), "Ž", $class);
	$cnt=intval($rec[$colNrCont]);
	$finish=toSec($rec[$colFinish]);
	//echo $finish-$start."-".$time."<br>";
	//$controlls=array();
	//$splits=array();
	
	$pos=$rec[$colPos];
	if(strlen($pos)<1) continue;
	
	$controlls="";
	$splits="";
	$last=0;
	for ($i=0;$i<$cnt;$i++)
	{
		$col=$colFrstCont+$i*2;
		//$controlls[]=$rec[$col];
		$controlls.=$rec[$col]."-";
		$sptime=toSec($rec[$col+1]);
		//$splits[]=$sptime-$last;
		$splits.=($sptime-$last)."-";
		$last=$sptime;
	}
	//$controlls[]="F1";
	//$splits[]=$time-$last;
	$controlls.="F1";
	$splits.=$time-$last;
	
	
	
	$sql="INSERT INTO `sv_splits` (`si`, `firstname`, `lastname`, `class`, `time`, `splits`, `controls`) VALUES ('".$si."', '".$fname."', '".$sname."', '".$class."', '".$time."', '".$splits."', '".$controlls."');";
	//echo $sql;
	$result=mysqli_query($mysql,$sql);
	
	//print_r($splits);
	//print_r($controlls);
	//break;
	
}




//print_r($csv);

function fix($text){
return iconv('Windows-1250', 'UTF-8', $text);
}

function toSec($time){
	$tm=explode(":", $time);	
	$ret=0;
	for ($i=0;$i<count($tm);$i++)
		$ret=$ret*60+intval($tm[$i]);
	return $ret;
}

function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
			        $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}
?>
