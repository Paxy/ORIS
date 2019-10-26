<?PHP

$normalizator=1.8;

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

function zaokruzi($float) {

return sprintf("%01.2f", $float);

}

//include "hedder.php";
header ('Content-type: text/html; charset=utf-8');


if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";

$sql=mysqlConnect($godina);

$takmicenje=$_GET['takmicenje'];
$dan=$_GET['dan'];

if (!IsSet($_GET['kategorija'])){
	$kategorija=1;
}
else
	$kategorija=$_GET['kategorija'];

	$result = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0 ORDER BY  `id` ASC ", $sql);
$br=1;
while ($row = mysql_fetch_assoc($result)) 
{	
	if ($br++==12) echo "<br>";
	echo "|<a href=bodovanjeTakmicenjeV2.php?takmicenje=".$takmicenje."&dan=".$dan."&kategorija=".$row['id'].">".$row['naziv']."</a>|  ";
}

$result = mysql_query("SELECT * FROM `rangiraniV2`", $sql);	
	$rangirani=rstoarray($result);

$result = mysql_query("SELECT `naziv` FROM `kategorije` WHERE `id`=".$kategorija, $sql);	
	$row = mysql_fetch_assoc($result);
	$kat=$row['naziv'];

$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id` = ".$takmicenje);
$row = mysql_fetch_assoc($result);

echo "<br><br>";
$datum=$row['datum'];
for ($i=0;$i<$row['dana'];$i++){
	echo "|<a href=bodovanjeTakmicenjeV2.php?takmicenje=".$takmicenje."&dan=".($i+1)."&kategorija=".$kategorija.">".($i+1).". dan</a>|  ";
}

echo "<h2>".$row['naziv']."<h2><h3>".$row['mesto']." (".$row['organizator'].")<br>".$kat."</h3>";
if ($row['dana']>1) echo "<h4>Dan: ".$dan."</h4>";

echo "<table border=1 cellspacing=0><tr><td><b>#</b></td><td><b>Reg. br.</b></td><td align=center><b>Ime i prezime</b></td><td align=center><b>Klub</b></td><td align=center><b>Vreme</b></td><td align=center><b>Bodovi</b></td><td><b>Bodovi u tom trenutku</b></td></tr>";

//SELECT `idSavez`,`takmicari`.`ime`,`takmicari`.`prezime`,`bodovi`,`minut`,`sekund`,`naziv`,`disq` FROM `bodovanjeTakmicaraV2`,`rezultati`,`takmicari`,`klubovi` WHERE `takmicari`.`idSavez`=  `rezultati`.`reg` AND `klubovi`.`id`= `takmicari`.`klub` AND `rezultati`.`reg`=`bodovanjeTakmicaraV2`.`takmicar` AND `bodovanjeTakmicaraV2`.`takmicenje`=32 AND `bodovanjeTakmicaraV2`.`takmicenje`=`rezultati`.`takmicenje` AND `bodovanjeTakmicaraV2`.`dan`=1 AND `rezultati`.`dan`=1 AND `rezultati`.`kategorija`=16 ORDER BY `disq` ASC, `bodovi` DESC
$result = mysql_query("SELECT `idSavez`,`takmicari`.`ime`,`takmicari`.`prezime`,`bodovi`,`minut`,`sekund`,`naziv`,`disq` FROM `bodovanjeTakmicaraV2`,`rezultati`,`takmicari`,`klubovi` WHERE `takmicari`.`idSavez`=  `rezultati`.`reg` AND `klubovi`.`id`= `takmicari`.`klub` AND `rezultati`.`reg`=`bodovanjeTakmicaraV2`.`takmicar` AND `bodovanjeTakmicaraV2`.`takmicenje`=".$takmicenje." AND `bodovanjeTakmicaraV2`.`takmicenje`=`rezultati`.`takmicenje` AND `bodovanjeTakmicaraV2`.`dan`=".$dan." AND `rezultati`.`dan`=".$dan." AND `rezultati`.`kategorija`=".$kategorija." ORDER BY `disq` ASC, `bodovi` DESC");
$br=1;
while($row = mysql_fetch_assoc($result))
{
	echo "<tr><td>".($br++)."</td><td>".$row['idSavez']."</td><td>".$row['ime']." ".$row['prezime']."</td><td>".$row['naziv']."</td>";
	if ($row['disq']==0)
		{
			echo "<td>".$row['minut'].":";
			if ($row['sekund']<10)
				echo "0";
			
			echo $row['sekund']."</td>";
			
		}
	else
		echo "<td>DISQ</td>";
	
	echo "<td>".zaokruzi($row['bodovi'])."</td>";
	
	//SELECT `bodovi` FROM `bodovanjeV2` WHERE `takmicar` = 1921 AND (`bodovanjeV2`.`datum` < '2013-06-29' OR (`bodovanjeV2`.`datum` = '2013-06-29' AND `bodovanjeV2`.`dan` < '1')) AND `bodovi`>0 ORDER BY `datum` DESC, `dan` DESC LIMIT 1
	//	$result1 = mysql_query("SELECT sum(`bodovi`) as bodovi  FROM `bodovanjeV2` WHERE `takmicar` = ".$row['idSavez']." AND (`bodovanjeV2`.`datum` < '".$datum."' OR (`bodovanjeV2`.`datum` = '".$datum."' AND `bodovanjeV2`.`dan` < '".$dan."')) AND `bodovi`>0");
	$result1 = mysql_query("SELECT `bodovi` FROM `bodovanjeV2` WHERE `takmicar` = ".$row['idSavez']." AND (`bodovanjeV2`.`datum` < '".$datum."' OR (`bodovanjeV2`.`datum` = '".$datum."' AND `bodovanjeV2`.`dan` < '".$dan."')) ORDER BY `datum` DESC, `dan` DESC LIMIT 1");
	$row1 = mysql_fetch_assoc($result1);
	$pros=$row1['bodovi'];
	//if ($pros==0) $pros=100;
	echo "<td>".zaokruzi($pros)."</td>";
	
	echo "</tr>";
	
}
echo "</table><br><br>";

echo "<table border=1 cellspacing=0><tr><td><b>Parametri bodovanja kategorije</b></td></b></td></tr>";
$result = mysql_query("SELECT * FROM `bodovanjeTakmicenjaV2` WHERE `takmicenje` = ".$takmicenje." AND `dan` = ".$dan." AND `kategorija` = ".$kategorija);
$row = mysql_fetch_assoc($result);
echo "<tr><td><b>";
echo "Suma: ".zaokruzi($row['suma'])."<br>";
//echo "SP: ".zaokruzi($row['sp'])."<br>";
echo "Ukupno: ".zaokruzi($row['ukupno'])."<br>";
$baza=zaokruzi($row['suma']*100*$normalizator/$row['ukupno']);
if ($baza==0) $baza=zaokruzi($row['suma']);
echo "Baza: ".$baza."<br>";
echo "</b></td></tr>";
echo "</table>";

/*
echo "<br><br><br>
Formula bodovanja:<br>
RP=MP+SP*(MT-RT)/ST<br>
RP - bodovi takmucara<br>
MP - prosecna vrednost bodova ucesnika na takmicenju u kategoriji<br>
SP - standardna devijacija bodova ucesnika na takmicenju u kategoriji<br>
MT - prosecno vreme prelaska staze u kategoriji<br>
RT - vreme prelaska staze svakog takmicara<br>
ST - standardna devijacija vremena prelaska staze u kategoriji<br><br>

RP=2600-RT*(2600-MP)/MT<br>
RP - bodovi takmucara<br>
MP - prosecna vrednost bodova ucesnika na takmicenju u kategoriji<br>
MT - prosecno vreme prelaska staze u kategoriji<br>
RT - vreme prelaska staze svakog takmicara<br>

*/

$proc=zaokruzi($row['suma']/$row['ukupno']*100);

echo "<br><br><br>
Formula bodovanja:<br>
Bodovi = VremePrvog * Baza / VremeTakmicara<br>
Baza = Suma bodova ucesnika u kategoriji * 100 * normalizator / Suma bodova svih takmicara u kategoriji<br>
Inicijalno, Baza je 100<br>
Normalizator je ".$normalizator." (".(($normalizator-1)*100)."%).<br>
Opis formule baze: koliko procentualno takmicara (u bodovima) ucestvuje na takmicenju uvecano za ".(($normalizator-1)*100)."%<br><br>
";
if ($row['ukupno']!=0) echo"
Na ovom takmicenju kalkulacija bodova je sledeca:<br>(".zaokruzi($row['suma'])."/".zaokruzi($row['ukupno'])."*100)=".$proc."% od svih bodova takmicara u ovoj kategoriji ucestvuje na takmicenju,<br>Baza je onda: ".$proc."+".(($normalizator-1)*100)."%=".zaokruzi($proc*$normalizator).", koliko dobija pobednik trke<br>
<br><a href=bodovanjePrikazV2.php>Nazad na BodovanjaV2</a>";

?>
