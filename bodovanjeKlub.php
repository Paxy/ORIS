<?PHP


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

function zaokruzi($float) {

return sprintf("%01.2f", floatval($float));

}

if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";

echo '
<html>
	<head>
		<title>Bodovanje klubova</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	<body>

';

echo "<table align=center><tr><td>|<a href=bodovanjeKlub.php>Aktuelna godina</a>| |<a href=bodovanjeKlub.php?godina=2018>2018</a>| |<a href=bodovanjeKlub.php?godina=2017>2017</a>| |<a href=bodovanjeKlub.php?godina=2016>2016</a>| |<a href=bodovanjeKlub.php?godina=2015>2015</a>| |<a href=arhiva.php target=_parent>Arhiva</a>|</td></tr></table><br><br>";

//include "hedder.php";
header ('Content-type: text/html; charset=utf-8');
if (!IsSet($_GET['liga'])) $liga=2;
else $liga=$_GET['liga'];

echo "<table><tr><td>";
echo "<h3><a href=bodovanjeKlub.php?liga=2&godina=".$godina.">Orijentiring Liga Srbije</a>";
echo "<br><a href=bodovanjeKlub.php?liga=3&godina=".$godina.">Orijentiring Liga Vojvodine</a>";
echo "<br><a href=bodovanjeKlub.php?liga=1&godina=".$godina.">ORIS Liga - Sva takmicenja koja se vode preko ORIS-a</a>";
//echo <br><a href=bodovanjeKlub.php?liga=3&godina=".$godina.">Liga Beograda</a>
echo "</h3><br><br>";

if ($liga==1) echo "<h1>ORIS Liga - Sva takmicenja koja se vode preko ORIS-a</h1><br>";
if ($liga==2) echo "<h1>Orijentiring Liga Srbije</h1><br>";
if ($liga==3) echo "<h1>Orijentiring Liga Vojvodine</h1><br>";
//if ($liga==3) echo "<h1>Liga Beograda</h1><br>";

$sql=mysqlConnect($godina);

	echo "<br><table border=1 cellspacing=0>";
	echo "<tr><td><b>#</b></td><td align=center><b>Klub</b></td><td align=center><b>Bodovi</b></td>";
	
	$result = mysql_query("SELECT `naziv`,`koeficient` FROM `kategorije`  WHERE `koeficient`>0 ORDER BY `id` ", $sql);	
	while ($row = mysql_fetch_assoc($result))
	{
		echo "<td><b>".$row['naziv']." (".$row['koeficient'].")</b></td>";	
	}
	echo "<td>Bodovi</td></tr>";
	
	$rb=1;
	
	$result = mysql_query("SELECT `naziv`,`ukupno`,`klub` FROM `bodovanjeKlubaUkupno`,`klubovi` WHERE `klubovi`.`id`=`bodovanjeKlubaUkupno`.`klub` AND `liga`=".$liga." AND `ukupno`>0 ORDER BY `ukupno` DESC ", $sql);	
	while ($row = mysql_fetch_assoc($result))
	{
		$klub=$row['klub'];
		echo "<tr><td>".$rb++."</td><td><b>".$row['naziv']."</b></td><td align=right><b>".zaokruzi($row['ukupno'])."</b></td>";	
		
		$result1 = mysql_query("SELECT `bodovi` FROM `bodovanjeKluba` WHERE `klub`=".$klub." AND `liga`=".$liga." ORDER BY `kategorija`", $sql);	
		while ($row1 = mysql_fetch_assoc($result1))
			{
				echo "<td align=right>".zaokruzi($row1['bodovi'])."</td>";
			}
		echo "<td align=right><b>".zaokruzi($row['ukupno'])."</b></td></tr>";		
		
	}
	
	
	
	echo "</table></td></tr></table>";

//include "footer.php";


?>
