<?php

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$ref_br_tak=150;

$params=getOFParams($tak,$params['dan']);

$max_start=120;
if (IsSet($_GET['max_start']))
$max_start=intval($_GET['max_start']);
if (IsSet($params['max_start']))
$max_start=intval($params['max_start']);

$default_interval=6;
if (IsSet($_GET['default_interval']))
$default_interval=intval($_GET['default_interval']);
if (IsSet($params['default_interval']))
$default_interval=intval($params['default_interval']);

$max_at_start=3;
if (IsSet($_GET['max_at_start']))
$max_at_start=intval($_GET['max_at_start']);
if (IsSet($params['max_at_start']))
$max_at_start=intval($params['max_at_start']);

$max_vacant_percent=30;
if (IsSet($_GET['max_vacant_percent']))
$max_vacant_percent=intval($_GET['max_vacant_percent']);
if (IsSet($params['max_vacant_percent']))
$max_vacant_percent=intval($params['max_vacant_percent']);

$dirigated=0;
if (IsSet($_GET['dirigated']))
$dirigated=intval($_GET['dirigated']);

$sql=mysqlConnect();


$takId=7;
if (IsSet($_GET['id']))
$takId=intval($_GET['id']);


$result = mysql_query("SELECT `dana` FROM `takmicenja` WHERE `id`=".$takId);
$row = mysql_fetch_assoc($result);
$dana=intval($row['dana']);

loadData($takId);

//$dan=1;
for ($dan=1;$dan<$dana+1;$dan++)
{
	
$sqlq="SELECT kat,count(kat) as cnt FROM `oris_fairtime` WHERE `tak`=".$takId." AND `dan`=".$dan." GROUP BY kat ORDER BY cnt DESC";
$result = mysql_query($sqlq);
//SELECT count(id) as cnt FROM `oris_fairtime` WHERE `tak`=16 AND `dan`=1 and kat in (select of_staze.kategorija from of_staze WHERE of_staze.takmicenje=16 and of_staze.dan=1 and of_staze.staza='A')

$ukupno=0;	
	
$kategorije = array();
while ($row = mysql_fetch_assoc($result))
	{
		$ime=$row["kat"];
		$cnt=$row["cnt"];
		$ukupno=$ukupno+intval($cnt);
		$kategorije[$ime]=$cnt;
	}		
//print_r($kategorije);
	
$max_start=$ukupno*$max_start/$ref_br_tak;

$start=array();
for ($i=0;$i<$max_start*1.5;$i++)
	$start[$i]=0;

foreach($kategorije as $kategorija=>$brtak) {
	//echo "<b>$kategorija</b></br>";
	
	$st=0;
	while ($start[$st]>=$max_at_start)	
		$st++;
	
	//$start[$st]++;
	
	$int=$default_interval;
	while (($int*$brtak*($max_vacant_percent/100+1)+$st) > $max_start)
		$int--;
	
	$nr_vacant=ceil($brtak*($max_vacant_percent/100));
	
	$lastKlub="";
	$lastSt=$st-$int; //umanjeno za interval zbog kasnijeg sabiranja
	$ubaceni=array();	
	while ($brtak > count($ubaceni))
	{
		
		// Get Takmicar
		if ($dirigated)
			$sqlq="(select * from (SELECT `takmicari`.`id`,`kat`,`ime`,`prezime`,`idSavez`,`klub` FROM `oris_fairtime`,`takmicari` WHERE `oris_fairtime`.`id`=`takmicari`.`id` AND `tak`=".$takId." AND `dan`=".$dan." AND `kat`=".$kategorija." AND `oris_fairtime`.`id`>0 AND `idSavez` IS NULL ORDER BY RAND())as nonreg) UNION (select * from(SELECT `takmicari`.`id`,`kat`,`ime`,`prezime`,`idSavez`,`klub` FROM `oris_fairtime`,`takmicari` WHERE `oris_fairtime`.`id`=`takmicari`.`id` AND `tak`=".$takId." AND `dan`=".$dan." AND `kat`=".$kategorija." AND `oris_fairtime`.`id`>0 AND `idSavez` > 0 ORDER BY RAND()) as reg)";
		else
			$sqlq="SELECT `takmicari`.`id`,`kat`,`ime`,`prezime`,`idSavez`,`klub` FROM `oris_fairtime`,`takmicari` WHERE `oris_fairtime`.`id`=`takmicari`.`id` AND `tak`=".$takId." AND `dan`=".$dan." AND `kat`=".$kategorija." AND `oris_fairtime`.`id`>0 ORDER BY RAND()";
		//echo $sqlq;
		$result = mysql_query($sqlq);
		$cnt=0;
		$tak=-1;
		while($row = mysql_fetch_assoc($result)){
			//echo ";".$row['ime'];
			if (in_array($row['id'],$ubaceni)) continue;
			
				if(strcmp($lastKlub,$row["klub"])!==0)
				{
					$tak=$row['id'];
					$lastKlub=$row['klub'];
					$ubaceni[]=$tak;
					break;
				}
				else
				{
					
					$lastKlub="";
					if ($nr_vacant>-1)
					{
						$tak=-1; //vacant	
						$nr_vacant--;
						break;
					}else {
						$tak=$row['id'];
						$ubaceni[]=$tak;
						break;
					}
					
					
				}
		}
		
		// Racunaj st vreme
		$st=$lastSt+$int;
		while($start[$st]>=$max_at_start)
			$st++;
		
		$start[$st]++;
		$lastSt=$st;	
		
		if ($tak==-1) {
			//echo "Vacant, Start:".$st."<br>";
			if ($nr_vacant<-2) break;
			$nr_vacant--;
			$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".$dan."', '".$takId."', '-1', '".$st."', '".$kategorija."');");
			
		}
		else {
		//echo $row['ime']." ".$row['prezime'].", ".$row['klub']. ", Id:".$row['idSavez'].", Start:".$st."<br>";
		$result = mysql_query("UPDATE `oris_fairtime` SET `st` = '".$st."' WHERE `oris_fairtime`.`dan` = ".$dan." AND `oris_fairtime`.`tak` = ".$takId." AND `oris_fairtime`.`id` = ".$tak." LIMIT 1;");
		}
	
	}
//break;

while($nr_vacant>0)
{
	// Racunaj st vreme
		$st=$lastSt+$int;
		while($start[$st]>=$max_at_start)
			$st++;
		
	$start[$st]++;
	$lastSt=$st;
	$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".$dan."', '".$takId."', '-1', '".$st."', '".$kategorija."');");
	$nr_vacant--;		
}

}

}

//echo "ok";

function loadData($tak)
{
	$result = mysql_query("DELETE FROM `oris_fairtime` WHERE `tak` = ".$tak);
	
	$result = mysql_query("SELECT `temp_prijave`.`takmicar`,`temp_prijave`.`kategorija`,`temp_prijave`.`dana` FROM `temp_prijave`,`takmicari` WHERE `temp_prijave`.`takmicar`=`takmicari`.`id` AND `temp_prijave`.`takmicenje`=".$tak);
	while ($row = mysql_fetch_assoc($result)) 
		{
			$dana=intval($row['dana']);
			for ($i=0;$i<6;$i++)
				if (intval($dana & pow(2,$i))>0)
				{
					$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".($i+1)."', '".$tak."', '".$row['takmicar']."', '0', '".$row['kategorija']."');");
				}
		
		}
		
	


}


		
?>