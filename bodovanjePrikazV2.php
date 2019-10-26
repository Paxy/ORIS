<?PHP


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$brojNajboljihRezultata=7;

function zaokruzi($float) {

return sprintf("%01.2f", $float);

}

//include "hedder.php";
header ('Content-type: text/html; charset=utf-8');
//if (!IsSet($_GET['liga'])) $liga=2;
//else $liga=$_GET['liga'];

if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";


$sql=mysqlConnect($godina);


$result1 = mysql_query("SELECT sum(`dana`) as kola FROM `takmicenja` WHERE `takmicenja`.`status`=2 ",$sql);
$row1 = mysql_fetch_assoc($result1);
$kola=$row1['kola'];
if (!Isset($brojNajboljihRezultata))
	$brojNajboljihRezultata=floor($kola/2)+1;

$result = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0 ORDER BY  `id` ASC ", $sql);
$br=1;
while ($row = mysql_fetch_assoc($result)) 
{	
	if ($br++==18) echo "<br>";
	echo "|<a href=bodovanjePrikazV2.php?liga=".$liga."&kategorija=".$row['id']."&godina=".$godina.">".$row['naziv']."</a>|  ";
}

if (IsSet($_GET['kategorija']))
{
	$kategorija=$_GET['kategorija'];
	
	$result = mysql_query("SELECT `naziv` FROM `kategorije` WHERE `id`=".$kategorija, $sql);	
	$row = mysql_fetch_assoc($result);
	echo "<h2>".$row['naziv']."</h2>";

	echo "<br><table border=1 cellspacing=0>";
	echo "<tr><td><b>#</b></td><td><b>Reg. br.</b></td><td align=center><b>Ime i prezime</b></td><td align=center><b>Klub</b></td><td align=center><b>Bodovi</b></td>";
	for ($i=0;$i<$brojNajboljihRezultata;$i++)
		echo "<td>".($i+1)."</td>";
	echo "<tr>";
	
	//SELECT * FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=3 ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC
	$result = mysql_query("SELECT * FROM (SELECT `datum`,`bodovanjeV2`.`dan`,`bodovanjeV2`.`bodovi`,`idSavez`,`prezime`,`ime`,`naziv` FROM `bodovanjeV2`,`takmicari`,`klubovi`,`bodovanjeTakmicaraV2` WHERE `bodovanjeTakmicaraV2`.`takmicar`=`takmicari`.`idSavez` AND `klubovi`.`id`=`takmicari`.`klub` AND `takmicari`.`idSavez`=`bodovanjeV2`.`takmicar` AND `kategorija`=".$kategorija." ORDER BY `datum` DESC, `dan` DESC) as data GROUP BY `idSavez` ORDER BY `bodovi` DESC");
	$br=1;
	while($row = mysql_fetch_assoc($result))
	{
		echo "<tr><td>".($br++)."</td><td>".$row['idSavez']."</b></td><td align=center>".$row['ime']." ".$row['prezime']."</td><td align=center>".$row['naziv']."</td><td align=center><b>".zaokruzi($row['bodovi'])."</b></td>";
		
		//SELECT `bodovi`,`takmicenje`,`dan` FROM `bodovanjeTakmicaraV2`,`takmicenja` WHERE `takmicar` = 40 AND `takmicenja`.`id`=`bodovanjeTakmicaraV2`.`takmicenje` AND `datum` > DATE_SUB(CURDATE(), INTERVAL 1 YEAR) ORDER BY `bodovi` DESC LIMIT 4
		$result1 = mysql_query("SELECT `bodovi`,`takmicenje`,`dan` FROM `bodovanjeTakmicaraV2`,`takmicenja` WHERE `takmicar` = ".$row['idSavez']." AND `takmicenja`.`id`=`bodovanjeTakmicaraV2`.`takmicenje` AND `datum` > DATE_SUB(CURDATE(), INTERVAL 1 YEAR) ORDER BY `bodovi` DESC LIMIT ".$brojNajboljihRezultata);
		$top=0;
		while($row1 = mysql_fetch_assoc($result1))
		{
			echo "<td><a href=bodovanjeTakmicenjeV2.php?takmicenje=".$row1['takmicenje']."&dan=".$row1['dan']."&kategorija=".$kategorija.">".zaokruzi($row1['bodovi'])."</a></td>";
			$top++;
		}
		while($top<$brojNajboljihRezultata){
			echo "<td></td>";
			$top++;
		}
		
		echo "</tr>";
		
	}
	
	echo "</table>";
	
}



echo "</td></tr></table><br><br>";

$result = mysql_query("SELECT DISTINCT `takmicenje`,`dan`,`naziv`,`mesto`,dana FROM `bodovanjeTakmicenjaV2`,`takmicenja` WHERE `bodovanjeTakmicenjaV2`.`takmicenje`=`takmicenja`.`id` ORDER BY `takmicenja`.`datum` ASC, `bodovanjeTakmicenjaV2`.`dan` ASC");
while($row = mysql_fetch_assoc($result))
	{
		echo "<a href=bodovanjeTakmicenjeV2.php?takmicenje=".$row['takmicenje']."&dan=".$row['dan'].">".$row['naziv'];
		if ($row['dana']>1)
		echo " - ".$row['dan'].". dan";
		
		echo " (".$row['mesto'].")</a><br>";
	}

echo "<br><br>";
/*
echo "Novi sistem bodovanja ima za cilj da poboljsa nacin bodovanja koristeci metode bodovanja koje se koriste za WRE.<br>
Trenutno je aktuelno pravilo koje predvidja sabiranje 4 najbolja rezultata kao ukupni rezultat takmicara u kategoriji.<br>Svako takmicenje se boduje, a broj bodova koji mozete osvojiti direktno zavisi od toga kolika je konkurencija u kategoriji.<br>
Od pocetka sezone bice prikazani realni rezultati bodovani na ovaj nacin kao eksperiment obodovanja na ovaj nacin.<br><br>
Klikom na bodove, dobijate stranicu u kojoj su prikazani rezultati takmicenja na kom su ostvareni ti bodovi, kao i parametri preko kojih su proracunati bodovi.<br>
<br>Pravilo bodovanja WRE trka mozete naci na adresi <a href='http://iof.6prog.org/IOF_Documents/FootO/spec2013.htm'>http://iof.6prog.org/IOF_Documents/FootO/spec2013.htm</a><br>
izuzetci pravila su sledeci:<br>
- najboljih 50% takmicara u svojoj kategoriji su rangirani takmicari<br>
- boduje se kategorija ako je bar 2 takmicara zavrsilo stazu<br>
- IP vrednost je 1 <br><br><br><br>
Formula za kalkukaciju bodova koristi prethodne bodove kao nacin ocenjivanja znacaja takmicarske kategorije na takmicenju. Pocetna pretpostvaka je zato da takmicar koji nije imao nikakve bodove ucestvuje sa 100 bodova u odredjivanju ranga takmicenja.<br>
To znaci da pocetna takmicenja u spisku ce imati nesto vise ne pravilnosti u proracunu dok se ne formira realna lista i razlika. Ocekujem da ce verodostojnost rezultata doci do izrazaja nakon 5-6 rezultata.";
*/
//include "footer.php";


?>