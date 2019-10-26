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
	
$sqlq="SELECT staza,count(staza) as cnt FROM `oris_fairtime`,`of_staze` WHERE `tak`=".$takId." AND oris_fairtime.`dan`=".$dan." and `of_staze`.`kategorija`=oris_fairtime.kat and of_staze.takmicenje=oris_fairtime.tak and of_staze.dan=oris_fairtime.dan and `oris_fairtime`.`id`>0 GROUP BY staza ORDER BY cnt DESC";
$result = mysql_query($sqlq);


$ukupno=0;	
	
$staze = array();
while ($row = mysql_fetch_assoc($result))
	{
		$ime=$row["staza"];
		$cnt=$row["cnt"];
		$ukupno=$ukupno+intval($cnt);
		$staze[$ime]=$cnt;
	}		
//print_r($staze);
	
$max_start=($ukupno/$ref_br_tak)*$max_start*1.1;
	

$start=array();
for ($i=0;$i<$max_start*1.5;$i++)
	$start[$i]=0;

foreach($staze as $staza=>$brtak) {
	
	
	$st=0;
	while ($start[$st]>=$max_at_start)	
		$st++;
	
	//$start[$st]++;
	
	$int=$default_interval;
	while (($int*$brtak*($max_vacant_percent/100+1)+$st) > $max_start)
		$int--;
	
	$nr_vacant=ceil($brtak*($max_vacant_percent/100));
	
	//echo "<b>$staza</b>maxs:$max_start,int:$int,vac:$nr_vacant,brtak:$brtak</br>";
	
	$lastKlub="";
	$lastSt=$st-$int; //umanjeno za interval zbog kasnijeg sabiranja
	$ubaceni=array();	
	while ($brtak > count($ubaceni))
	{
		
		// Get Takmicar
		
		$sqlq="SELECT `takmicari`.`id`,`kat`,`ime`,`prezime`,`idSavez`,`klub`,`staza` FROM `oris_fairtime`,`takmicari`,of_staze WHERE `oris_fairtime`.`id`=`takmicari`.`id` AND `tak`=".$takId." AND oris_fairtime.`dan`=".$dan." AND of_staze.staza='".$staza."' AND `oris_fairtime`.`id`>0 and of_staze.takmicenje=oris_fairtime.tak and of_staze.dan=oris_fairtime.dan and of_staze.kategorija=oris_fairtime.kat ORDER BY RAND()";
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
			$vacant_kat=getRndKat($staza,$takId,$dan);
			$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".$dan."', '".$takId."', '-1', '".$st."', '".$vacant_kat."');");
			
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
	$vacant_kat=getRndKat($staza,$takId,$dan);
	$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".$dan."', '".$takId."', '-1', '".$st."', '".$vacant_kat."');");
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
					$result1 = mysql_query("INSERT INTO `oris_fairtime` (`dan`, `tak`, `id`, `st`, `kat`) VALUES ('".($i+1)."', '".$tak."', '".$row['takmicar']."', '-1', '".$row['kategorija']."');");
				}
		
		}
		
	


}

function getRndKat($staza,$takId,$dan){
	$result = mysql_query("SELECT `kategorija`  FROM `of_staze` WHERE `takmicenje` = ".$takId." AND `dan` = ".$dan." AND `staza` LIKE '".$staza."' ORDER BY RAND() LIMIT 1");
	$row = mysql_fetch_assoc($result);
	return $row['kategorija'];
}


		
?>