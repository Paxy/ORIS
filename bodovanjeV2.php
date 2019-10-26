<?PHP

$deafultBodovi=100;
$brojNajboljihRezultata=7;
$normalizator=1.8;

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 


function bodovanjeV2($takid)
{
GLOBAL $brojNajboljihRezultata;
GLOBAL $deafultBodovi;
GLOBAL $normalizator;
$sql=mysqlConnect();

$result1 = mysql_query("SELECT sum(`dana`) as kola FROM `takmicenja` WHERE `takmicenja`.`status`=2 ",$sql);
$row1 = mysql_fetch_assoc($result1);
$kola=$row1['kola'];
if (!Isset($brojNajboljihRezultata))
	$brojNajboljihRezultata=floor($kola/2)+1;

//ciscenje
$result = mysql_query("DELETE FROM `bodovanjeTakmicenjaV2` WHERE `takmicenje`=".$takid);
$result = mysql_query("DELETE FROM `bodovanjeTakmicaraV2` WHERE `takmicenje`=".$takid);

$result = mysql_query("SELECT `dana`,`datum` FROM `takmicenja` WHERE `id`=".$takid, $sql);
$row = mysql_fetch_assoc($result);

$dana=$row['dana'];
$datum=$row['datum'];

// ciscenje baze Bodovanja
$result = mysql_query("DELETE FROM `bodovanjeV2` WHERE `datum`='".$datum."'");

//postojeSvi($sql);

for ($i=1;$i<=$dana;$i++)
{
	$parametri=array();
	
	// provera da li se takmicar takmici u svojoj kategoriji, ako ne disq=10
	$result = mysql_query("SELECT `reg` FROM `rezultati` WHERE `reg` NOT IN (SELECT `reg` FROM `rezultati`,`takmicari` WHERE `takmicenje`=".$takid." AND `dan`=".$i." AND `rezultati`.`reg`=`takmicari`.`idSavez` AND `rezultati`.`kategorija`=`takmicari`.`kategorija`) ");
	while($row = mysql_fetch_assoc($result))	
		{
			if (!$row['reg']>0) continue;
			$result1 = mysql_query("UPDATE `rezultati` SET  `disq` =  '10' WHERE  `rezultati`.`takmicenje` =".$takid." AND  `rezultati`.`reg` =".$row['reg']." AND  `rezultati`.`dan` =".$i);
		}
	
	if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error();
	
	// Proracun Suma i Ukupno
	$result = mysql_query("SELECT `id`  FROM `kategorije` WHERE `tip` = 1", $sql);
	while($row = mysql_fetch_assoc($result))	
		{
			$kat=$row['id'];
			
			//select sum(`bodovi`) as suma from (SELECT `bodovi` FROM (SELECT `bodovanjeV2`.`takmicar`,`bodovi`, `bodovanjeV2`.`datum`,`bodovanjeV2`.`dan` FROM  `rezultati`,`bodovanjeV2` WHERE  `kategorija` =6 AND `takmicenje`=25 AND `rezultati`.`dan`=1 AND `reg` is not null AND `disq`=0 AND `bodovanjeV2`.`takmicar`=`rezultati`.`reg` AND `bodovi`>0 AND (`bodovanjeV2`.`datum` < '2013-09-28' OR (`bodovanjeV2`.`datum` = '2013-09-28' AND `bodovanjeV2`.`dan` < '1'))  ORDER BY `bodovanjeV2`.`datum` DESC, `bodovanjeV2`.`dan` DESC) as data GROUP BY `takmicar`) as sum
			//$result1 = mysql_query("SELECT sum(`bodovi`) as suma FROM (SELECT `bodovi` FROM (SELECT `bodovanjeV2`.`takmicar`,`bodovi`, `bodovanjeV2`.`datum`,`bodovanjeV2`.`dan` FROM  `rezultati`,`bodovanjeV2` WHERE  `kategorija` =".$kat." AND `takmicenje`=".$takid." AND `rezultati`.`dan`=".$i." AND `reg` is not null AND `disq`=0 AND `bodovanjeV2`.`takmicar`=`rezultati`.`reg` AND `bodovi`>0 AND (`bodovanjeV2`.`datum` < '".$datum."' OR (`bodovanjeV2`.`datum` = '".$datum."' AND `bodovanjeV2`.`dan` < '".$i."'))  ORDER BY `bodovanjeV2`.`datum` DESC, `bodovanjeV2`.`dan` DESC) as data GROUP BY `takmicar`) as sum");
			$result1 = mysql_query("select sum(`bodovi`) as suma from (SELECT `bodovi` FROM (SELECT `bodovanjeV2`.`takmicar`,`bodovi`, `bodovanjeV2`.`datum`,`bodovanjeV2`.`dan` FROM  `rezultati`,`bodovanjeV2` WHERE  `kategorija` =".$kat." AND `takmicenje`=".$takid." AND `rezultati`.`dan`=".$i." AND `reg` is not null AND `disq`=0 AND `bodovanjeV2`.`takmicar`=`rezultati`.`reg` AND `bodovi`>0 AND (`bodovanjeV2`.`datum` < '".$datum."' OR (`bodovanjeV2`.`datum` = '".$datum."' AND `bodovanjeV2`.`dan` < '".$i."'))  ORDER BY `bodovanjeV2`.`datum` DESC, `bodovanjeV2`.`dan` DESC) as data GROUP BY `takmicar`) as sum");
			$row1 = mysql_fetch_assoc($result1);
			$suma=$row1['suma'];
			
			
			if ($suma==0 || is_null($suma)){
				$suma=$deafultBodovi;
			}
			

			//select sum(bodovi) as ukupno from (SELECT * FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=6 ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC) as data
			$result1 = mysql_query("select sum(bodovi) as ukupno from (SELECT * FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=".$kat." ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC) as data");
			$row1 = mysql_fetch_assoc($result1);
			$ukupno=$row1['ukupno'];

			//$ukupno=0;

			//print_r($mp);
			$parametri[$kat] = array('suma'=> $suma,'ukupno' => $ukupno);
			$result1 = mysql_query("INSERT IGNORE INTO `bodovanjeTakmicenjaV2` (`takmicenje`, `dan`, `kategorija`, `suma`, `ukupno`) VALUES ('".$takid."', '".$i."', '".$kat."', '".$suma."', '".$ukupno."');");
			
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error() . "ErrorInsert bodovanjeTakmicenjaV2 za ".$takid;
			
		}
	
	// bodovanje Takmicara
	$result = mysql_query("SELECT `reg`,`kategorija`,(`minut`*60+`sekund`) as VT FROM `rezultati` WHERE `takmicenje`=".$takid." AND `dan`=".$i." AND `disq`=0 AND `reg` > 0 ORDER BY `kategorija` ASC, VT ASC");
	$pk=0;
	$vp=0;
	while($row = mysql_fetch_assoc($result))	
		{
			$kategorija=$row['kategorija'];
			$reg=$row['reg'];
			$vt=$row['VT'];
			
			$kat=$parametri[$kategorija];

			if($pk != $kategorija){
				$pk=$kategorija;
				$vp=$vt;
				if($kat["ukupno"]==0) $base=$deafultBodovi;
				else $base=$kat["suma"]*100*$normalizator/$kat["ukupno"];
			}			

			$pt=$vp * $base / $vt;

//	echo "base:".$base.";vp".$vp.";vt".$vt."<br>";
						
			$result1 = mysql_query("INSERT IGNORE INTO `bodovanjeTakmicaraV2` (`takmicenje`, `dan`, `takmicar`, `bodovi`) VALUES ('".$takid."', '".$i."', '".$reg."', '".$pt."');");
		
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error() . "ErrorInsert bodovanjeTakmicaraV2 za ".$reg;
		
			
			
		}
			
		
//die();

		// sumiranje svih rezultata	
		$result = mysql_query("SELECT distinct `takmicar` FROM `bodovanjeTakmicaraV2`");
		while($row = mysql_fetch_assoc($result))	
		{
			$reg=$row['takmicar'];
			
			// sumiranje rezultata takmicara
			
			// SELECT SUM(`bodovi`) AS ukupno FROM (SELECT `bodovi` FROM `bodovanjeTakmicaraV2`,`takmicenja` WHERE `takmicar` = 2375 AND `takmicenja`.`id`=`bodovanjeTakmicaraV2`.`takmicenje` AND `datum` > DATE_SUB('2013-12-15', INTERVAL 1 YEAR) AND (`datum` < '2013-12-15' OR (`datum` = '2013-12-15' AND `bodovanjeTakmicaraV2`.`dan`<2)) ORDER BY `bodovi` DESC LIMIT 4) as data
			$result1 = mysql_query("SELECT SUM(`bodovi`) AS ukupno FROM (SELECT `bodovi` FROM `bodovanjeTakmicaraV2`,`takmicenja` WHERE `takmicar` = ".$reg." AND `takmicenja`.`id`=`bodovanjeTakmicaraV2`.`takmicenje` AND `datum` > DATE_SUB('".$datum."', INTERVAL 1 YEAR) AND (`datum` < '".$datum."' OR (`datum` = '".$datum."' AND `bodovanjeTakmicaraV2`.`dan`<=".$i.")) ORDER BY `bodovi` DESC LIMIT ".$brojNajboljihRezultata.") as data");
			$row1 = mysql_fetch_assoc($result1);
			$ukupno=$row1['ukupno'];
			
			$result1 = mysql_query("INSERT IGNORE INTO .`bodovanjeV2` (`takmicar`, `bodovi`, `datum`, `dan`) VALUES ('".$reg."', '".$ukupno."', '".$datum."', '".$i."');");
			
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error(). " Greska kod takmicara: ".$reg;

		}
	
}

if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error();

return "OK";
	
}

function startOd($sql){
$od=$_GET['od'];
echo "Od ".$od;
$result = mysql_query("SELECT * FROM `takmicenja` WHERE `status`=2 AND `datum` >= '".$od."' ORDER BY `takmicenja`.`datum` ASC");
		while($row = mysql_fetch_assoc($result))	
		{
			echo bodovanjeV2($row['id']) . " datum ".$row['datum'];
		}

}



if (IsSet($_GET['boduj'])) 
	echo bodovanjeV2($_GET['boduj']);

if (IsSet($_GET['od'])) 
	echo startOd(mysqlConnect());


?>