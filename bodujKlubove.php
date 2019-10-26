<?php
error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
	

$result = mysql_query("TRUNCATE TABLE `bodovanjeKluba`",$sql);
$result = mysql_query("TRUNCATE TABLE `bodovanjeKlubaUkupno`",$sql);

$result = mysql_query("SELECT DISTINCT  `liga` FROM `lige` order by `liga` ", $sql);
	$row = mysql_fetch_assoc($result); // preskoci ligu 1
while($row = mysql_fetch_assoc($result))
{
$liga=$row['liga'];
$result1 = mysql_query("SELECT `id` FROM `klubovi` WHERE `id`<100",$sql);
	while ($row1 = mysql_fetch_assoc($result1))
	{
		$klub=$row1['id'];
		$result2 = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0",$sql);
		while ($row2 = mysql_fetch_assoc($result2))
				{
					$kategorija=$row2['id'];
					$koeficient=$row2['koeficient'];
					
					$result3 = mysql_query("SELECT sum(`liga".$liga."b`) as suma FROM (SELECT `liga".$liga."b` FROM `bodovanje`,`takmicari` WHERE `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `takmicari`.`klub`=".$klub." AND `takmicari`.`kategorija`=".$kategorija." ORDER BY `liga".$liga."b` DESC LIMIT 3) as data",$sql);
					$row3 = mysql_fetch_assoc($result3);
					$suma=floatval($row3['suma'])*floatval($koeficient);
					
					$result3 = mysql_query("INSERT INTO `bodovanjeKluba` (`klub`, `kategorija`, `bodovi`, `liga`) VALUES ('".$klub."', '".$kategorija."', '".$suma."', '".$liga."');",$sql);
					
				}
	
	}
	$result1 = mysql_query("insert into bodovanjeKlubaUkupno(`klub`,`ukupno`,`liga`) SELECT `klub`, sum(`bodovi`) as ukupno,`liga` FROM `bodovanjeKluba` WHERE `liga`=".$liga." GROUP BY `klub`",$sql);
}

echo "OK";

?>