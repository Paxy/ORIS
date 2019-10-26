<?php


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";

$sql=mysqlConnect($godina);


	$result = mysql_query("SELECT * FROM `rezultati` WHERE `minut` > 400 AND `sekund` = 0", $sql);
while ($row = mysql_fetch_assoc($result)) 
{	
	$min=$row['minut'];
	$sek=$min%60;
	$minuta=floor($min/60);
	$sqlq="UPDATE `rezultati` SET `minut` = '".$minuta."', `sekund` = '".$sek."' WHERE `rezultati`.`takmicenje` = ".$row['takmicenje']." AND `rezultati`.`dan` = ".$row['dan']." AND `rezultati`.`minut` = ".$min." AND `rezultati`.`sekund` = 0 AND `rezultati`.`ime` = '".$row['ime']."' AND `rezultati`.`prezime` = '".$row['prezime']."' LIMIT 1;";
	echo $sqlq."<br>";
	$result1 = mysql_query($sqlq);
}
echo "OK";

?>