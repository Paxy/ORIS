<?php
error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
	

$result = mysql_query("SELECT DISTINCT  `liga` FROM `lige` order by `liga`", $sql);
	$row = mysql_fetch_assoc($result); // preskoci ligu 1
while($row = mysql_fetch_assoc($result))
{
	$liga=$row['liga'];

	$result1 = mysql_query("SELECT DISTINCT `takmicari`.`kategorija` FROM `bodovanje`,`takmicari` WHERE `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `bodovanje`.`liga".$liga."v`=1",$sql);
	while ($row1 = mysql_fetch_assoc($result1))
	{
		$kategorija=$row1['kategorija'];
		$br=1;
		$result2 = mysql_query("SELECT `takmicari`.`idSavez`,`liga".$liga."b`,`liga".$liga."p` FROM `bodovanje`,`takmicari` WHERE `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `bodovanje`.`liga".$liga."v`=1 AND `takmicari`.`kategorija`=".$kategorija." AND `takmicari`.`idSavez` > 0 ORDER BY `liga".$liga."b` DESC",$sql);
		while ($row2 = mysql_fetch_assoc($result2))
		{
			$id=$row2['idSavez'];
			$bodovi=$row2["liga".$liga."b"];
			$spoz=$row2["liga".$liga."p"];
			if ($br<$spoz)
				$status=1;
			elseif ($br==$spoz)
				$status=0;
			else
				$status=2;
				
			if (intval($bodovi)==0)
			$pozicija=100;
			else
			$pozicija=$br;

		if ($ignorePoziciju)
			$result3 = mysql_query("UPDATE `bodovanje` SET `liga".$liga."p` = '".$pozicija."' WHERE `bodovanje`.`takmicar` = ".$id." ;",$sql);
		else
			$result3 = mysql_query("UPDATE `bodovanje` SET `liga".$liga."p` = '".$pozicija."', `liga".$liga."n` = '".$status."' WHERE `bodovanje`.`takmicar` = ".$id." ;",$sql);

			$br++;

		}
	}

}

echo "OK";

?>