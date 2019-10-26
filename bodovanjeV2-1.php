<?PHP

$deafultBodovi=1300;
$brojNajboljihRezultata=4;
$denominator=1; // 90%
$rangirani=0.3; // 50%

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 


function bodovanjeV2($takid)
{
GLOBAL $brojNajboljihRezultata;
GLOBAL $deafultBodovi;
GLOBAL $denominator;
GLOBAL $rangirani;
$sql=mysqlConnect();

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
	
	// proracun MT, ST, MP, SP parametara svih zvanicnih kategorija u rezultatima
	$result = mysql_query("SELECT `id`  FROM `kategorije` WHERE `tip` = 1", $sql);
	while($row = mysql_fetch_assoc($result))	
		{
			$kat=$row['id'];
			
			// izvlacim niz rangiranih idSavez
			/*  
			$result1 = mysql_query("SELECT count(distinct(`idSavez`)) as cnt FROM `bodovanjeV2`,`takmicari` WHERE `takmicar`=`idSavez` AND `kategorija`=".$kat);
			$row1=mysql_fetch_assoc($result1);
			$brTak=$row1['cnt'];
			
			$limit=floor($brTak*$rangirani));
			if ($limit<2) $limit=2;
			$result1 = mysql_query("SELECT `idSavez` FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=".$kat." ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC LIMIT ".$limit;
			$rang=mysql_fetch_assoc($result1);
			*/
			
			
			$result1 = mysql_query("SELECT AVG(`minut` * 60 + `sekund`) as MT, STDDEV(`minut` * 60 + `sekund`) as ST FROM  `rezultati` WHERE  `kategorija` =".$kat." AND `takmicenje`=".$takid." AND `dan`=".$i." AND `reg` is not null AND `disq`=0 AND `reg` IN (SELECT `id` FROM `rangiraniV2`)");
			$row1 = mysql_fetch_assoc($result1);
			$mt=$row1['MT'];
			$st=$row1['ST'];
			if ($st==0) $st=1;
			
			
			
			//SELECT AVG(`bodovi`) as MP, STDDEV(`bodovi`) as SP FROM (SELECT AVG(`bodovi`) as bodovi FROM (SELECT `bodovanjeV2`.`takmicar`,`bodovi`, `bodovanjeV2`.`datum`,`bodovanjeV2`.`dan` FROM  `rezultati`,`bodovanjeV2` WHERE  `kategorija` =6 AND `takmicenje`=11 AND `rezultati`.`dan`=1 AND `reg` is not null AND `disq`=0 AND `bodovanjeV2`.`takmicar`=`rezultati`.`reg` AND `bodovi`>0 AND `rezultati`.`reg` IN (SELECT `id` FROM `rangiraniV2`) AND (`bodovanjeV2`.`datum` < '2013-03-29' OR (`bodovanjeV2`.`datum` = '2013-03-29' AND `bodovanjeV2`.`dan` < '1'))  ORDER BY `bodovanjeV2`.`datum` DESC, `bodovanjeV2`.`dan` DESC) as data GROUP BY `takmicar`) as sum
			$result1 = mysql_query("SELECT AVG(`bodovi`) as MP, STDDEV(`bodovi`) as SP FROM (SELECT AVG(`bodovi`) as bodovi FROM (SELECT `bodovanjeV2`.`takmicar`,`bodovi`, `bodovanjeV2`.`datum`,`bodovanjeV2`.`dan` FROM  `rezultati`,`bodovanjeV2` WHERE  `kategorija` =".$kat." AND `takmicenje`=".$takid." AND `rezultati`.`dan`=".$i." AND `reg` is not null AND `disq`=0 AND `bodovanjeV2`.`takmicar`=`rezultati`.`reg` AND `bodovi`>0 AND `rezultati`.`reg` IN (SELECT `id` FROM `rangiraniV2`) AND (`bodovanjeV2`.`datum` < '".$datum."' OR (`bodovanjeV2`.`datum` = '".$datum."' AND `bodovanjeV2`.`dan` < '".$i."'))  ORDER BY `bodovanjeV2`.`datum` DESC, `bodovanjeV2`.`dan` DESC) as data GROUP BY `takmicar`) as sum");
			$row1 = mysql_fetch_assoc($result1);
			$mp=$row1['MP'];
			$sp=$row1['SP'];
			if ($mp==0 || is_null($mp)){
				$mp=$deafultBodovi;
				$sp=1;
			}
			//print_r($mp);
			if ($sp==0) $sp=1;
		
			$parametri[$kat] = array('mt' => $mt,'st' => $st,'mp' => $mp,'sp' => $sp);
			$result1 = mysql_query("INSERT IGNORE INTO `bodovanjeTakmicenjaV2` (`takmicenje`, `dan`, `kategorija`, `mp`, `sp`, `mt`, `st`) VALUES ('".$takid."', '".$i."', '".$kat."', '".$mp."', '".$sp."', '".$mt."', '".$st."');");
			
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error() . "ErrorInsert bodovanjeTakmicenjaV2 za ".$takid;
			
		}
	
	// bodovanje Takmicara
	$result = mysql_query("SELECT `reg`,`kategorija`,(`minut`*60+`sekund`) as RT FROM `rezultati` WHERE `takmicenje`=".$takid." AND `dan`=".$i." AND `disq`=0 AND `reg` > 0");
	while($row = mysql_fetch_assoc($result))	
		{
			$kategorija=$row['kategorija'];
			$reg=$row['reg'];
			$rt=$row['RT'];
			
			$kat=$parametri[$kategorija];
			
			if ($kat["mt"]-$rt==0 || $kat["mt"]==0)
				$rp=0; // ako je samo jedan takmicar ne dodeljuj bodove
			else
				$rp=( 2600 - $rt * ( 2600 - $kat["mp"] ) / $kat["mt"] )*$denominator;
			//$rp=($kat["mp"]+$kat["sp"]*($kat["mt"]-$rt)/$kat["st"])*$denominator;
			
			if ($rp<0) $rp=0;
			
			$result1 = mysql_query("INSERT IGNORE INTO `bodovanjeTakmicaraV2` (`takmicenje`, `dan`, `takmicar`, `bodovi`) VALUES ('".$takid."', '".$i."', '".$reg."', '".$rp."');");
		
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error() . "ErrorInsert bodovanjeTakmicaraV2 za ".$reg;
		
			
			
		}
			
			
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
	if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error(). " PreRang !";
	rangiraj($sql);		
		if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error(). " Rang !";
	
}

if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error();

return "OK";
	
}

function postojeSvi($sql){
	GLOBAL $deafultBodovi;
	
$result = mysql_query("SELECT `idSavez` FROM `takmicari` WHERE `idSavez` IS NOT NULL AND `idSavez` NOT IN (SELECT `takmicar` FROM  `bodovanjeV2`) AND `idSavez` NOT LIKE '' ",$sql);

while($row = mysql_fetch_assoc($result))	
		{
			$reg=$row['idSavez'];
			$result1 = mysql_query("INSERT IGNORE INTO `bodovanjeV2` (`takmicar`, `bodovi`, `datum`) VALUES ('".$reg."', '".$deafultBodovi."', CURDATE());",$sql);
		}
		
		if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error();
		
}

function rangiraj($sql)
{
	GLOBAL $rangirani;
	$result = mysql_query("DELETE FROM `rangiraniV2`",$sql);
	
		$res = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0 ORDER BY  `id` ASC ", $sql);
	while ($row = mysql_fetch_assoc($res)) 
	{		
		$result = mysql_query("SELECT `idSavez` FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=".$row['id']." ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC");
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error(). " Rang niko !";

		$limit=ceil(mysql_num_rows($result)*$rangirani);
		if ($limit<2) $limit=2;
		if ($limit>mysql_num_rows($result)) $limit=mysql_num_rows($result);
		
		for ($i=0;$i < $limit;$i++){
			$row = mysql_fetch_assoc($result);
			$result1 = mysql_query("INSERT INTO `rangiraniV2` (`id`) VALUES (".$row['idSavez'].")",$sql);
			if (mysql_errno()) return "Greska:".mysql_errno().": ".mysql_error(). "ID: ".$row['idSavez'];
		}
	}
	return "OK";
}


if (IsSet($_GET['boduj'])) 
	echo bodovanjeV2($_GET['boduj']);

if (IsSet($_GET['rangiraj'])) 
	echo rangiraj(mysqlConnect());
	
?>