<?PHP

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$takId=$_GET['id'];

if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";

include "hedder.php";

$sql=mysqlConnect($godina);

$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id` LIKE ".$takId, $sql);

$row = mysql_fetch_assoc($result);
echo "<table border=1 align=center cellspacing=0><tr><td><b>Naziv:</b></td><td>".$row['naziv']."</td></tr>";
echo "<tr><td><b>Mesto:</b></td><td>".$row['mesto']."</td></tr>";
echo "<tr><td><b>Organizator:</b></td><td>".$row['organizator']."</td></tr>";
echo "<tr><td><b>Datum:</b></td><td>".sqludatum($row['datum'])."</td></tr>";
echo "<tr><td><b>Broj dana:</b></td><td>".$row['dana']."</td></tr>";
//echo "<tr><td><b>Poslednji dan prijave:</b></td><td>".sqludatum($row['datumprijave'])."</td></tr>";
echo "<tr><td><b>Bodovanje:</b></td><td>".$row['bodovanje']."</td></tr>";
echo "<tr><td><b>Bele&#x0161;ke:</b></td><td width=200>".nl2br($row['notes'])."</td></tr>";
echo "</table>";


echo "<br><br><h3>Prijavljeni takmi&#x010D;ari</h3>";

$udana=$row['dana'];

$sqlq="SELECT `kategorije`.`naziv` FROM `prijave`,`takmicari`,`kategorije` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `kategorije`.`id`=`prijave`.`kategorija` AND `prijave`.`takmicenje`=".$takId." GROUP BY `prijave`.`kategorija`";
$result = mysql_query($sqlq, $sql);
$kat=rstoarray($result);



$sqlq="SELECT `takmicari`.`ime`,`takmicari`.`prezime`,`klubovi`.`naziv`,`drzave`.`naziv`, `kategorije`.`naziv`, `dana`,`prijave`.`si` FROM `prijave`,`takmicari`,`klubovi`,`drzave`,`kategorije` WHERE `prijave`.`takmicenje`=".$takId." AND `takmicari`.`id`=`prijave`.`takmicar` AND `klubovi`.`id`=`takmicari`.`klub` AND `klubovi`.`drzava`=`drzave`.`id` AND `kategorije`.`id`=`prijave`.`kategorija` ORDER BY  `kategorije`.`naziv` ASC";
$result = mysql_query($sqlq, $sql);

$posl="";
echo "<table>";

$br=0;
$brd=array();
for ($i = 0; $i < $udana; $i++) 
	$brd[$i]=0;	
$svidani=0;
$kc=array();
for ($i = 0; $i < $udana; $i++) 
{
	$kc[$i]=array();
	for ($j = 0; $j < sizeof($kat); $j++) 
		$kc[$i][$j]=0;
	}
	

while ($row =  mysql_fetch_array($result)) {
	$br++;
	$ime=$row[0];
	$prezime=$row[1];
	$klub=$row[2];
	$zemlja=$row[3];
	$kategorija=$row[4];
	$dana=$row[5];
	$katarr=array_keys($kat,$kategorija);
	

	if ($kategorija==$posl)
	{
		$sd=0;
		echo "<tr><td width=30></td><td width=50>".$ime."</td><td width=150>".$prezime."</td><td width=150>".$klub."</td><td>".$zemlja."</td><td width=50></td><td width=100>".$row[6]."</td><td>";
		echo "Dan: ";
		for ($i = 0; $i < $udana; $i++) {
			if (intval($dana&pow(2,$i))>0) 
			{
				echo "|".($i+1)."| ";
				$brd[$i]++;
				if ($sd==($udana-1)) $svidani++;
				$sd++;
				$kc[$i][$katarr[0]]++;
			}
		}

		echo "</td></tr>";
	}
	else
	{
	$sd=0;
	echo "<tr><td></td><td>&nbsp;</td></tr><tr><td width=30></td><td><h2>".$kategorija."</h3></td></tr>";
	$posl=$kategorija;
	echo "<tr><td width=30></td><td>".$ime."</td><td>".$prezime."</td><td>".$klub."</td><td>".$zemlja."</td><td width=50></td><td width=100>".$row[6]."</td><td>";
	echo "Dan: ";
	for ($i = 0; $i < $udana; $i++) {
			if (intval($dana&pow(2,$i))>0) 
			{
				echo "|".($i+1)."| ";
				$brd[$i]++;
				if ($sd==($udana-1)) $svidani++;
				$sd++;
				$kc[$i][$katarr[0]]++;
			}
		}
	echo "<td><tr>";
	}

}
echo "</table>";


$sqlq="SELECT `kategorije`.`naziv`,count(*) FROM `prijave`,`takmicari`,`kategorije` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `kategorije`.`id`=`prijave`.`kategorija` AND `prijave`.`takmicenje`=".$takId." GROUP BY `prijave`.`kategorija`";
$result = mysql_query($sqlq, $sql);
echo "<br><br><table align=center>";
echo "<tr><td align=center><b>Kategorija</b></td>";
for ($i = 0; $i < $udana; $i++) 
echo "<td align=center width=100>".($i+1).".dan</td>";

echo "<td align=center width=100>Ukupno</td></tr>";

while ($row =  mysql_fetch_array($result)) {
echo "<tr><td align=center width=100>".$row['naziv']."</td>";
for ($i = 0; $i < $udana; $i++) 
{
	$katarr=array_keys($kat,$row['naziv']);
	echo "<td align=center>".$kc[$i][$katarr[0]]."</td>";
}
echo "<td align=center>".$row['count(*)']."</td></tr>";
}
echo "</table>";

echo "<br><br><table align=center><tr><td align=center><b>Ukupno prijavljenih: ".$br."</b></td></tr><tr><td align=center>";
for ($i = 0; $i < $udana; $i++) 
	echo "(".($i+1).".dan: ". $brd[$i] . ")";
	echo "(Sve dane: ".$svidani.")";
echo "</td></td></table>";

include "footer.php";

?>