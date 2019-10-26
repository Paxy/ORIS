<?PHP

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$takId=$_GET['id'];
if (IsSet($_GET['dan'])) $dan=$_GET['dan'];
else $dan=1;

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
echo "<tr><td><b>Bele&#x0161;ke:</b></td><td>".$row['notes']."</td></tr>";
echo "</table>";


echo "<div align=center><br><br><h3>Rezultati</h3></div>";

$udana=$row['dana'];
if (intval($udana)>1)
{
	echo "<div align=center>";
	for ($i = 1; $i < intval($udana)+1; $i++) 
		echo "|<a href=rezultati.php?id=".$takId."&dan=".$i."&godina=".$godina.">".$i.". dan</a>|  ";
	echo "</div>";	
}	


$sqlq="SELECT * FROM `rezultati`,`kategorije` WHERE `kategorije`.`id`=`rezultati`.`kategorija` AND `takmicenje` = ".$takId." AND `dan` = ".$dan." ORDER BY `kategorija` ASC, `disq` ASC, `minut` ASC, `sekund` ASC";
$result = mysql_query($sqlq, $sql);

$posl="";
echo "<table align=center>";

$br=0;

while ($row =  mysql_fetch_assoc($result)) {
	$br++;

	if ($row['naziv']!=$posl)
	{
	echo "<tr><td></td><td>&nbsp;</td></tr><tr><td width=30></td><td><h2>".$row['naziv']."</h3></td></tr>";
	$posl=$row['naziv'];	
	$rb=1;	
	}
	
	if (intval($row['disq']>0))
	echo "<tr><td width=30></td><td width=50>".$row['ime']."</td><td width=150>".$row['prezime']."</td><td width=150>".$row['klub']."</td><td>DISQ</td>";
	else 	echo "<tr><td width=30>".$rb.".</td><td width=50>".$row['ime']."</td><td width=150>".$row['prezime']."</td><td width=150>".$row['klub']."</td><td>".$row['minut'].":".sprintf("%02d",$row['sekund'])."</td>";
	
	if ($row['reg']==NULL)
		if (IsSet($_SESSION['klubId']) && intval($_SESSION['klubId'])>1000)
		{
			$result1 = mysql_query("select `idSavez` from `takmicari` WHERE `takmicari`.`ime`='".$row['ime']."' AND `takmicari`.`prezime`='".$row['prezime']."' AND `idSavez` > 0;", $sql);
			if ($row1 =  mysql_fetch_assoc($result1))
			echo "<td><form method=post action=\"dodajId.php?tak=".$takId."&ime=".$row['ime']."&prezime=".$row['prezime']."\"><input type=\"text\" name=\"idsavez\" size=5 value=\"".$row1['idSavez']."\"/><input type=submit value=OK></form></td></tr>";
			else
			echo "<td><form method=post action=\"dodajId.php?tak=".$takId."&ime=".$row['ime']."&prezime=".$row['prezime']."\"><input type=\"text\" name=\"idsavez\" size=5 /><input type=submit value=OK></form></td></tr>";
		}
			else
		echo "<td></td></tr>";
	else
	echo "<td align=right size=20>&#176;</td></tr>";
	
	$rb++;
	
}
echo "</table>";
if (intval($udana)>1)
{
	echo "<br><br><div align=center>";
	for ($i = 1; $i < intval($udana)+1; $i++) 
		echo "|<a href=rezultati.php?id=".$takId."&dan=".$i."&godina=".$godina.">".$i.". dan</a>|  ";
	echo "</div>";	
}	
echo "<br><div align=center>Ukupno takmicara sa rezultatom: ".$br."</div>";
echo "<br><div align=center>Takmicari sa &#176; mogu biti bodovani na ORIS-u</div>";


include "footer.php";

?>