<?php

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
	

$result = mysql_query("SELECT DISTINCT  `liga` FROM `lige` order by `liga`", $sql);
	$row = mysql_fetch_assoc($result); // preskoci ligu 1
while($row = mysql_fetch_assoc($result))
{
	$liga=$row['liga'];

	$result1 = mysql_query("SELECT COUNT(*) FROM `lige`,`takmicenja` WHERE `takmicenja`.`id`=`lige`.`takmicenje` AND `takmicenja`.`status`=2 AND `liga`=".$liga,$sql);
	$row1 = mysql_fetch_assoc($result1);
	$kola=$row1['COUNT(*)'];
	$bodovati=floor($kola/2)+1;
	
  if(Isset($_GET['limit']))
	    $limit=" LIMIT 100 OFFSET ".intval($_GET['limit'])*100; // ako mora deo po deo
	else $limit=" ";
	
  
	$result1 = mysql_query("SELECT DISTINCT `reg` FROM `rezultati`,`lige` WHERE `rezultati`.`takmicenje`=`lige`.`takmicenje` AND `rezultati`.`dan`=`lige`.`dan` AND `rezultati`.`reg` IS NOT NULL AND `lige`.`liga`=".$liga." order by `reg` asc ".$limit,$sql);
	while($row1 = mysql_fetch_assoc($result1))
	{		
		$takmicar=$row1['reg'];
		$sqlq="SELECT * FROM `rezultati`,`lige` WHERE `rezultati`.`takmicenje`=`lige`.`takmicenje` AND `rezultati`.`dan`=`lige`.`dan` AND `lige`.`liga`=".$liga." AND `rezultati`.`reg`= ".$takmicar." ORDER BY `bodovi".$liga."` DESC";
		//echo $sqlq."<br>";
		$result2 = mysql_query($sqlq,$sql);
		
		$ukupno=0;
		$brRez=0;
		while($row2 = mysql_fetch_assoc($result2))
				if ($brRez++ < $bodovati)
					{
						$ukupno+=$row2["bodovi".$liga];
						$result3 = mysql_query("UPDATE `rezultati` SET `ok".$liga."`=5 WHERE `rezultati`.`takmicenje` = ".$row2['takmicenje']." AND `rezultati`.`reg` = ".$row2['reg']." AND `rezultati`.`dan` = ".$row2['dan'].";",$sql);
					}
					
				else
				{
					$result3 = mysql_query("UPDATE `rezultati` SET `ok".$liga."`=6 WHERE `rezultati`.`takmicenje` = ".$row2['takmicenje']." AND `rezultati`.`reg` = ".$row2['reg']." AND `rezultati`.`dan` = ".$row2['dan'].";",$sql);
				}
		
		//$ukupno=$row2['SUM(`bodovi'.$liga.'`)'];
		$result2 = mysql_query("UPDATE `bodovanje` SET `liga".$liga."b` = '".$ukupno."' WHERE `bodovanje`.`takmicar` = ".$takmicar." ;",$sql);
	}
     echo "OK";
}

?>
