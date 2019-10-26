<?PHP



error_reporting(E_ERROR | E_PARSE);

require_once( 'functions.php' ); 



session();



include "hedder.php";



echo "<table align=center><tr><td>|<a href=takmicenja.php>Aktuelna godina</a>| |<a href=takmicenja.php?godina=2018>2018</a>| |<a href=takmicenja.php?godina=2017>2017</a>| |<a href=takmicenja.php?godina=2016>2016</a>| |<a href=takmicenja.php?godina=2015>2015</a>|</td></tr></table><br><br>";

if (IsSet($_GET['godina']))

$godina=$_GET['godina'];

else

$godina="";



$sql=mysqlConnect($godina);

$result = mysql_query("SELECT * FROM `takmicenja` ORDER BY  `datum` ASC ", $sql);





echo "<table border=1 cellspacing=0 align=center width=830><tr bgcolor=olive><td align=center><b>Datum</b><td align=center width=200	><b>Naziv</b></td><td align=center><b>Mesto</b></td><td align=center><b>Organizator</b></td></td><td align=center><b>Distanca</b></td><td align=center><b>Br. dana</b></td><td align=center><b>Bodovanje</b></td><td align=center><b>Akcije</b></td></tr>";

while ($row = mysql_fetch_assoc($result)) {

	if($row['status']=='0')

	{

		if (IsSet($_SESSION['klubId']) && strlen($godina)<1) 

		{

		$status="<a href='prijavaTakmicara.php?takmicenje=".$row['id']."'>Prijava-Odjava</a>";

	

		if ($_SESSION['klubId']>1000 && strlen($godina)<1) $status='<a href=admin.php>Admin panel</a>';	



		if (IsSet($_SESSION['klubId']) && (($_SESSION['klubId']==$row['klub']) || ($_SESSION['klubId']>1000)) && strlen($godina)<1) 

		{

		$status.="<br> <a href=\"organizator.php?id=".$row['id']."\">Organizator</a>";

		}



		}else

		$status="Molim Vas logujte se";	

	}

	elseif ($row['status']=='1')

	{

		$status="Zavrsena prijava";

	

	   if (IsSet($_SESSION['klubId']) && ($_SESSION['klubId']==$row['klub']) && strlen($godina)<1) 

  	  {

	  $status.="<br> <a href=\"organizator.php?id=".$row['id']."\">Organizator</a>";

	  }

	}elseif ($row['status']=='2')

		$status="Takmičenje je kompletirano";

	elseif ($row['status']=='5')

		{

			$status="Nije omogućeno prijavljivanje";

			 if (IsSet($_SESSION['klubId']) && ($_SESSION['klubId']==$row['klub']) && strlen($godina)<1) 

  	  {

	  	$status.="<br> <a href=\"organizator.php?id=".$row['id']."\">Organizator</a>";

			}

		}







	echo "<tr><td align=center>".sqludatum($row['datum'])."</td><td align=center>";

	if ($row['status']=='2') echo "<a href=\"rezultati.php?id=".$row['id']."&godina=".$godina."\">".$row['naziv']."</a>";

	elseif ($row['status']!='5') echo "<a href=\"listaTakmicenja.php?id=".$row['id']."&godina=".$godina."\">".$row['naziv']."</a>";

	else echo $row['naziv'];



	echo "</td><td align=center>".$row['mesto']."</td><td align=center>".$row['organizator']."</td><td align=center>".$row['notes']."</td><td align=center>".$row['dana']."</td><td align=center>".$row['bodovanje']."</td><td align=center>".$status."</td></tr>";

}

echo "</table>";









include "footer.php";



?>

