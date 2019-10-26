<?PHP


//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);

require_once( 'functions.php' ); 

header ('Content-type: text/html; charset=utf-8');

function zaokruzi($float) {

return sprintf("%01.2f", $float);

}



if (IsSet($_GET['godina']))
$godina=$_GET['godina'];
else
$godina="";

echo "<table align=center><tr><td>|<a href=bodovanje.php>Aktuelna godina</a>| |<a href=bodovanje.php?godina=2018>2018</a>| |<a href=bodovanje.php?godina=2017>2017</a>| |<a href=bodovanje.php?godina=2016>2016</a>| |<a href=bodovanje.php?godina=2015>2015</a>| |<a href=arhiva.php target=_parent>Arhiva</a>|</td></tr></table><br><br>";

//include "hedder.php";
if (!IsSet($_GET['liga'])) $liga=2;
else $liga=$_GET['liga'];


echo "<table><tr><td>";
echo "<h3><a href=bodovanje.php?liga=2&godina=".$godina.">Orijentiring Liga Srbije</a>";
echo "<br><a href=bodovanje.php?liga=3&godina=".$godina.">Orijentiring Liga Vojvodine</a>";
echo "<br><a href=bodovanje.php?liga=1&godina=".$godina.">ORIS Liga - Sva takmicenja koja se vode preko ORIS-a</a>";
//echo <br><a href=bodovanje.php?liga=3>Liga Beograda</a>
echo "</h3><br><br>";

if ($liga==1) echo "<h1>ORIS Liga - Sva takmicenja koja se vode preko ORIS-a</h1><br>";
if ($liga==2) echo "<h1>Orijentiring Liga Srbije</h1><br>";
if ($liga==3) echo "<h1>Orijentiring Liga Vojvodine</h1><br>";
//if ($liga==3) echo "<h1>Liga Beograda</h1><br>";

$sql=mysqlConnect($godina);

$result = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0 ORDER BY  `id` ASC ", $sql);
$br=1;
while ($row = mysql_fetch_assoc($result)) 
{	
	if ($br++==14) echo "<br>";
	echo "|<a href=bodovanje.php?liga=".$liga."&kategorija=".$row['id']."&godina=".$godina.">".$row['naziv']."</a>|  ";
}

if (IsSet($_GET['kategorija']))
{
	$kategorija=$_GET['kategorija'];
	
	$result = mysql_query("SELECT `naziv` FROM `kategorije` WHERE `id`=".$kategorija, $sql);	
	$row = mysql_fetch_assoc($result);
	
	echo '<script>document.title = "'.$row['naziv'].'";</script>';
	echo "<h2>".$row['naziv']."</h2>";

	echo "<br><table border=1 cellspacing=0>";
	echo "<tr><td></td><td><b>#</b></td><td><b>Reg. br.</b></td><td align=center><b>Ime i prezime</b></td><td align=center><b>Klub</b></td><td align=center><b>Bodovi</b></td>";
	
	$br=1;
	$takmicenja=array();
	$takdani=array();
	//SELECT * FROM `lige`,`takmicenja` WHERE `lige`.`takmicenje`=`takmicenja`.`id` AND `liga`=1 ORDER BY `datum` ASC, `dan` ASC
	$takm = mysql_query("SELECT * FROM `lige`,`takmicenja` WHERE `lige`.`takmicenje`=`takmicenja`.`id` AND `liga`=".$liga." AND (`takmicenja`.`status`=2 OR `takmicenja`.`status`=1) ORDER BY `datum` ASC, `dan` ASC", $sql);
	while ($row = mysql_fetch_assoc($takm)) 
	{
		$takmicenja[$br]=$row['id'];
		$takdani[$br]=$row['dan'];
		echo "<td align=center colspan=2><a href=rezultati.php?id=".$row['id']." target=\"_top\"><b>".$br.". kolo (".sqludatum($row['datum']).")<br> ".$row['naziv']."</b></a></td>";
		$br++;
	}
	echo "</td><td align=center><b>Bodovi</b></td></tr>";

	
	$result = mysql_query("SELECT `ime`,`prezime`,`idSavez`,`klubovi`.`naziv`, `liga".$liga."b`, `liga".$liga."p`, `liga".$liga."n` FROM `takmicari`,`bodovanje`,`klubovi` WHERE `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND  `takmicari`.`idSavez` >  '' AND `klubovi`.`id`=`takmicari`.`klub` AND`bodovanje`.`liga".$liga."v`=1 AND `kategorija`=".$kategorija." ORDER BY `liga".$liga."p` ASC", $sql);
	while ($row = mysql_fetch_assoc($result)) 
	{
		echo "<tr>";
		
		if (intval($row['liga'.$liga.'p'])!=100)
			$pozicija=$row['liga'.$liga.'p'].".";
		else 
			$pozicija="";
			
		if (strlen($pozicija)==0) echo "<td></td>";
		elseif ($row['liga'.$liga.'n']==1) echo "<td>+</td>";
		elseif ($row['liga'.$liga.'n']==2) echo "<td>-</td>";
		else echo "<td>o</td>";
		
		echo "<td>".$pozicija."</td><td>".$row['idSavez']."</td><td><b><i>".$row['ime']." ".$row['prezime']."</i></b></td><td>".$row['naziv']."</td><td align=right><b>".zaokruzi($row['liga'.$liga.'b'])."</b></td>";
		
		$rb=1;
		
		$dn=0;
		
		
		//SELECT `disq`,`minut`,`sekund`,`bodovi1`,`plasman`, `rezultati`.`takmicenje` AS `tak`, `rezultati`.`dan` FROM `rezultati`,`bodovanje`,`takmicenja`,`lige` WHERE `rezultati`.`reg`=`bodovanje`.`takmicar` AND `takmicenja`.`id`=`rezultati`.`takmicenje` AND `lige`.`takmicenje`=`takmicenja`.`id`  AND `lige`.`dan`= `rezultati`.`dan` AND `lige`.`liga`=1 AND `rezultati`.`reg`=1000001 ORDER BY `datum` ASC, `lige`.`dan` ASC
		$result1 = mysql_query("SELECT `disq`,`minut`,`sekund`,`bodovi".$liga."`,`plasman`, `rezultati`.`takmicenje` AS `tak`, `rezultati`.`dan`, `rezultati`.`ok".$liga."` FROM `rezultati`,`bodovanje`,`takmicenja`,`lige` WHERE `rezultati`.`reg`=`bodovanje`.`takmicar` AND `takmicenja`.`id`=`rezultati`.`takmicenje` AND `lige`.`takmicenje`=`takmicenja`.`id` AND `lige`.`dan`= `rezultati`.`dan` AND `lige`.`liga`=".$liga." AND `rezultati`.`reg`=".$row['idSavez']." ORDER BY `datum` ASC, `lige`.`dan` ASC",$sql);
		while ($row1 = mysql_fetch_assoc($result1)) 
		{
			
			$tak1=array_keys($takmicenja,$row1['tak']);
			
			$dans=$row1['dan'];
			
			$dn=0;
			do{
				if (intval($takdani[$tak1[$dn]])<>intval($dans)) $dn=($dn+1)%6;
				$tak2=$tak1[$dn];
			}
			while (!isSet($tak2));
			
			//$tak3=$tak1[$takdani[$rb]];
			
			
			//print_r($takmicenja);
			//print_r($takdani);
			//echo "rb:".$rb." tak2:".$tak2." dans:".$dans." tk:".(intval($takdani[$tak1[$dn]])<>intval($dans)?0:1)." dn:".$dn;
			//print_r($tak1);
			//print_r($tak2);
			//print_r($rb);
			
			
			while($rb < $tak2) {
				echo "<td></td><td></td>";
				$rb++;
			}
				$rb++;
				
			if (strlen($row1['sekund'])<2)
				$sekundi="0".$row1['sekund'];
			else $sekundi=$row1['sekund'];
				
			if ($row1['ok'.$liga]==6)	
				$neracuna=" style=\"color:gray\"";
				else 
				$neracuna="";
				
			if ($row1['disq']>0)
			echo "<td>DISQ</td><td>0.00</td>";
			else if (intval($row1['plasman'])==100)
			echo "<td>DISQ</td><td>0.00</td>";
			else echo "<td align=center".$neracuna.">".$row1['minut'].":".$sekundi." (".$row1['plasman'].")</td><td align=right".$neracuna."><b>".zaokruzi($row1["bodovi".$liga])."</b></td>";
		
			$lastt=$row1['tak'];
				
		}	
		
		while ($rb < $br)
		{
					echo "<td></td><td></td>";
					$rb++;
		}
		
		echo "<td align=right><b>".zaokruzi($row['liga'.$liga.'b'])."</b></td>";
		echo "</tr>";

	}
	echo "</table>";
	
	if($liga==1)
	echo "<br><br><div align=center>NAPOMENA: ORIS liga boduje samo takmicenja i takmicare cija je prijava uradjena preko ORIS portala, a registrovani su u Savezu !";
}
else
{
	
$result = mysql_query("insert ignore bodovanje(takmicar) select distinct idSavez from takmicari"); // Populisi bazu Bodovanja sa novim takmicarima
	//die("stop");
}


echo "</td></tr></table>";

//include "footer.php";


?>
