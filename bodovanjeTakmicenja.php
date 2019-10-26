<?PHP

function bodovanjeTakmicenja($takid)
{

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

//$takid=$_GET['id'];

$sql=mysqlConnect();
$result = mysql_query("SELECT `dana` FROM `takmicenja` WHERE `id`=".$takid, $sql);
$row = mysql_fetch_assoc($result);

$result = mysql_query("update `rezultati` set bodovi1=0, bodovi2=0, bodovi3=0 WHERE `takmicenje` = ".$takid);

$dana=$row['dana'];
for ($i=1;$i<=$dana;$i++)
{
	$result = mysql_query("SELECT `liga` FROM `lige` WHERE `takmicenje`=".$takid." AND `dan`=".$i, $sql);	
	while($row = mysql_fetch_assoc($result))
	{
		$liga=$row['liga'];
		$result1 = mysql_query("SELECT DISTINCT `kategorija` FROM `rezultati` WHERE `takmicenje` = ".$takid." AND `dan` = ".$i, $sql);
		while($row1 = mysql_fetch_assoc($result1))	
		{
			$kat=$row1['kategorija'];
			$result2 = mysql_query("SELECT `takmicari`.`idSavez`,`takmicari`.`id`, `rezultati`.`minut`, `rezultati`.`sekund`, `takmicari`.`ime` as time, `takmicari`.`prezime` as tprezime, `rezultati`.`ime` as rime, `rezultati`.`prezime` as rprezime  FROM `rezultati`,`takmicari`,`bodovanje` WHERE `rezultati`.`reg`=`takmicari`.`idSavez` AND `rezultati`.`kategorija`=`takmicari`.`kategorija` AND `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `rezultati`.`disq`=0 AND `rezultati`.`takmicenje`=".$takid." AND `rezultati`.`dan`=".$i." AND `bodovanje`.`liga".$liga."v`=1 AND `takmicari`.`kategorija`=".$kat." ORDER BY `minut` ASC, `sekund` ASC",$sql);

			$vrPrvog=0;
			$brtakmicara=0;

			while($row2 = mysql_fetch_assoc($result2))	
			{

				//similar_text($row2['time'],$row2['rime'], $p1);
				//similar_text($row2['tprezime'],$row2['rprezime'], $p2);
				//if ($p1<90 || $p2<90) 
				//{
				//	$result3 = mysql_query("UPDATE `rezultati` SET `ok".$liga."`=1, `reg`=NULL WHERE `rezultati`.`takmicenje` = ".$takid." AND `rezultati`.`reg` = ".$row2['idSavez']." AND `rezultati`.`dan` = ".$i." ;" ,$sql);
				//	continue;
				//}

				//echo "ID:".$row2['id']." IDSavez:".$row2['idSavez']." Minut:".$row2['minut']." Sekund:".$row2['sekund']."<br>";
				if ($vrPrvog==0)
				{
					$vrPrvog=$row2['minut']*60+$row2['sekund'];
					$result3 = mysql_query("UPDATE `rezultati` SET `bodovi".$liga."` = '100', `ok".$liga."`=5 WHERE `rezultati`.`takmicenje` = ".$takid." AND `rezultati`.`reg` = ".$row2['idSavez']." AND `rezultati`.`dan` = ".$i." ;" ,$sql);
					$brtakmicara++;
				}
				else
				{
				
					$vreme=$row2['minut']*60+$row2['sekund'];
					$bodovi=$vrPrvog*100/$vreme;
					//if ($bodovi<50) 
					//	$bodovi=0;
					//else $brtakmicara++;
					$brtakmicara++;
					$result3 = mysql_query("UPDATE `rezultati` SET `bodovi".$liga."` = '".$bodovi."', `ok".$liga."`=5 WHERE `rezultati`.`takmicenje` = ".$takid." AND `rezultati`.`reg` = ".$row2['idSavez']." AND `rezultati`.`dan` = ".$i." ;" ,$sql);
				}

			}

			if($brtakmicara!=0 && $brtakmicara<2)
			{
				mysql_data_seek($result2,0);
				while($row2 = mysql_fetch_assoc($result2))	
					$result3 = mysql_query("UPDATE `rezultati` SET `bodovi".$liga."` = '0' WHERE `rezultati`.`takmicenje` = ".$takid." AND `rezultati`.`reg` = ".$row2['idSavez']." AND `rezultati`.`dan` = ".$i." ;" ,$sql);
			}
			
		}


	}
}

$result1 = mysql_query("SELECT DISTINCT `takmicari`.`kategorija` FROM `rezultati`,`takmicari` WHERE `rezultati`.`kategorija`=`takmicari`.`kategorija` AND `rezultati`.`reg`=`takmicari`.`idSavez` AND `rezultati`.`takmicenje`=".$takid,$sql);
while ($row1 = mysql_fetch_assoc($result1))
{
	$kategorija=$row1['kategorija'];
	$dana=getdana($takid);
	
	for ($i=1;$i<=$dana;$i++)
	{	
		$br=1;
		$result2 = mysql_query("SELECT `takmicari`.`idSavez` FROM `rezultati`,`takmicari` WHERE `takmicenje`=".$takid." AND `dan`=".$i." AND `takmicari`.`kategorija`=`rezultati`.`kategorija` AND `rezultati`.`reg`=`takmicari`.`idSavez` AND `takmicari`.`kategorija`=".$kategorija." AND `disq`=0 ORDER BY `minut` ASC, `sekund` ASC",$sql);
		while ($row2 = mysql_fetch_assoc($result2))
		{
			$result3 = mysql_query("UPDATE `rezultati` SET `plasman` = '".$br."' WHERE `rezultati`.`reg` = ".$row2['idSavez']." AND `takmicenje`=".$takid." AND `dan`=".$i.";",$sql);
			$br++;

		}
	}

}




return "OK";
}

function fixByName(){
	
error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
	$result = mysql_query("SELECT * FROM `rezultati`,`takmicari` WHERE `takmicari`.`ime`=`rezultati`.`ime` AND `takmicari`.`prezime`=`rezultati`.`prezime` AND `rezultati`.`ok1`<5", $sql);	
	while($row = mysql_fetch_assoc($result)){
		if (!is_null($row['idSavez']))
			{
				$result1 = mysql_query("UPDATE `rezultati` SET `reg`=".$row['idSavez']." WHERE `rezultati`.`ime` = '".$row['ime']."' AND `rezultati`.`prezime`='".$row['prezime']."' ;" ,$sql);
			}
		else
			{
				$result1 = mysql_query("UPDATE `rezultati` SET `ok1`=2,`ok2`=2 WHERE `rezultati`.`ime` = '".$row['ime']."' AND `rezultati`.`prezime`='".$row['prezime']."' ;" ,$sql);
			}
	}
	
}


?>
