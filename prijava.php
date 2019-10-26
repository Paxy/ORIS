<?PHP

require_once( 'functions.php' ); 
session();
error_reporting(E_ERROR | E_PARSE);

//header("Cache-Control: no-cache, must-revalidate");
 // Date in the past
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


if (!IsSet($_GET['takmicenje'])) 
	echo header("Location: takmicenja.php");

$takId=$_GET['takmicenje'];
$klubId=logged();

//include "hedder.php";

echo "<script src=\"scripts.js\"></script> 
<style type=\"text/css\">
tr.prijavljen {
	background-color: lime; color: black;
}
tr.neprijavljen {
	background-color: none; color: black;
}
tr.obrada {
	background-color: yellow; color: black;
}
tr.greska {
	background-color: red; color: black;
}

</style>";

$sql=mysqlConnect();
$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id` LIKE ".$takId, $sql);
$row = mysql_fetch_assoc($result);

// Ako organizator rucno ubacuje
if ($klubId==$row['klub'] && IsSet($_GET['kid']))
$klubId=$_GET['kid'];

if (intval($klubId)>1000 && IsSet($_GET['kid']))
$klubId=$_GET['kid'];

echo "<table border=1 cellspacing=0 align=center><tr><td><b>Naziv:</b></td><td>".$row['naziv']."</td></tr>";
echo "<tr><td><b>Mesto:</b></td><td>".$row['mesto']."</td></tr>";
echo "<tr><td><b>Organizator:</b></td><td>".$row['organizator']."</td></tr>";
echo "<tr><td><b>Datum:</b></td><td>".sqludatum($row['datum'])."</td></tr>";
echo "<tr><td><b>Broj dana:</b></td><td>".$row['dana']."</td></tr>";
//echo "<tr><td><b>Poslednji dan prijave:</b></td><td>".sqludatum($row['datumprijave'])."</td></tr>";
echo "<tr><td><b>Bodovanje:</b></td><td>".$row['bodovanje']."</td></tr>";
echo "<tr><td><b>Distanca:</b></td><td>".$row['notes']."</td></tr>";
echo "</table>";

$tip=$row['tip'];

echo "<br><br><h3>Prijava takmi&#x010D;ara</h3>";

$dana=$row['dana'];

//$kategorije=getkategorije();
$prijavljeni=getprijavljeni($takId);

$result = mysql_query("SELECT * FROM `takmicari` WHERE `klub` LIKE ".$klubId." ORDER BY  `kategorija` ASC", $sql);

$br=1;
echo "<table border=1 id=prijave cellspacing=0 align=center><tr BGCOLOR=olive><td><b>Ime</b></td><td><b>Prezime</b></td><td><b>ID Saveza</b></td><td><b>Mail</b></td><td><b>SI</b></td><td><b>Kategorija</b></td><td><b>Bele&#x0161;ka</b></td><td><b>Dana</b></td><td><b>Akcija</b></td></tr>";
while ($row = mysql_fetch_assoc($result)) {
	if (in_array($row['id'], $prijavljeni)) 
	{
		echo "<tr class=\"prijavljen\" id=\"red".$br."\"><td>".$row['ime']."</td><td>".$row['prezime']."</td><td>".$row['idSavez']."</td><td>".$row['mail']."</td><td>";
		$info=getptakmicartakinfo($takId,$row['id']);
		echo "<input type=text id=\"si".$br."\" value=\"".$info[1]. "\" size=6 disabled=true></td><td>";
		echo "<select id=kat".$br." disabled=true>";
		
		$result1 = mysql_query("SELECT * FROM `kategorije` WHERE `tip`=".$tip." ORDER BY  `id` ASC", $sql);
		//foreach($kategorije as $key=>$value) // potencijalni bug, ako nisu redni brojevi kategorija redom
		while ($row1 = mysql_fetch_assoc($result1)) 
		{
		echo "<option value='".$row1['id']."'";
		if (intval($row1['id'])==intval($info[0])) echo " selected";
		echo ">".$row1['naziv']."</option>";
		}
	
		echo "</td><td>".$row['notes']."</td>";
		echo "<td>";
		$tdani=intval(gettdani($takId,$row['id']));
		for ($i = 0; $i < $dana; $i++) {
			echo intval($i+1)."<input type=\"checkbox\" id=\"d".$br.intval($i+1)."\" value=".pow(2,$i)." ";
			if (intval($tdani& pow(2,$i))>0) echo "checked";
			echo " disabled>";
		}
		echo "</td>";
		echo "<td><input type=\"button\" value=\"Odjavi\" id=\"dugme".$br."\" onclick=\"akcija(".$br.",".$takId.",".$row['id'].",".$dana.")\" /><span id=\"akcija".$br."\"></span>";
			
		if (intval($row['idSavez'])<1) 
			echo  " <a href=obrisiTakmicara.php?id=".$row['id']."&ime=".urlencode($row['ime']." ".$row['prezime'])."&tak=".$takId.">|X|</a> ";
		
		echo "</td></tr>";
		 
	}
	else
	{
		echo "<tr class=\"neprijavljen\" id=\"red".$br."\"><td>".$row['ime']."</td><td>".$row['prezime']."</td><td>".$row['idSavez']."</td><td>".$row['mail']."</td><td>";
		echo "<input type=text id=\"si".$br."\" value=\"".$row['si']. "\" size=6></td><td>";
		echo "<select id=kat".$br.">";
		$result1 = mysql_query("SELECT * FROM `kategorije` WHERE `tip`=".$tip." ORDER BY  `id` ASC", $sql);
		//foreach($kategorije as $key=>$value) // potencijalni bug, ako nisu redni brojevi kategorija redom
		while ($row1 = mysql_fetch_assoc($result1)) 
		{
		echo "<option value='".$row1['id']."'";
		if (intval($row1['id'])==intval($row['kategorija'])) echo " selected";
		echo ">".$row1['naziv']."</option>";
		}
		
		echo "</td><td>".$row['notes']."</td>";
		echo "<td>";
		for ($i = 0; $i < $dana; $i++) {
		    echo intval($i+1)."<input type=\"checkbox\" id=\"d".$br.intval($i+1)."\" value=".pow(2,$i)." checked>";
		}
		echo "</td>";
		
		echo "<td><input type=\"button\" value=\"Prijavi\" id=\"dugme".$br."\" onclick=\"akcija(".$br.",".$takId.",".$row['id'].",".$dana.")\" /><span id=\"akcija".$br."\"></span>";
			if (intval($row['idSavez'])<1) 
				echo  " <a href=obrisiTakmicara.php?id=".$row['id']."&ime=".urlencode($row['ime']." ".$row['prezime'])."&tak=".$takId.">|X|</a> ";
		
		echo "</td></tr>";
	}
	
$br=$br+1;
}
echo "</table>";

echo "<br><br><div style=\"display: none;\" id=\"novo\">Ime:<input type=\"text\" size=15 id=\"nime\" >Prezime: <input type=\"text\" size=15 id=\"nprezime\" >SI:<input type=\"text\" size=6 id=\"nsi\">Bele&#x0161;ka:<input type=\"text\" id=\"nbeleska\" >Kategorija:";
echo "<select id=nkategorija>";
$result1 = mysql_query("SELECT * FROM `kategorije` WHERE `tip`=".$tip." ORDER BY  `id` ASC", $sql);
		//foreach($kategorije as $key=>$value) // potencijalni bug, ako nisu redni brojevi kategorija redom
		while ($row1 = mysql_fetch_assoc($result1)) 
{
echo "<option value='".intval($row1['id'])."'";
echo ">".$row1['naziv']."</option>";
}

echo "</select><input type=\"button\" value=\"Ubaci\" id=\"ubaciNovi\" onclick=\"novitakmicar(".$klubId.",".$takId.")\"></div>";
echo "<input type=\"button\" value=\"Novi takmicar\" id=\"novi\" onclick=\"shownovi()\">";



//include "footer.php";

?>