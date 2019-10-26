<?php
require_once( 'functions.php' ); 
session();
error_reporting(E_ALL);

$logovan=logged();
if ($logovan<1000)
	echo header("Location: takmicenja.php");

include "hedder.php";

$sql=mysqlConnect();


if (IsSet($_GET['klub']))
	$klubId=$_GET['klub'];
else
	$klubId=$_POST['klub'];

$tak=0;
if (IsSet($_GET['izmeni']))
	$tak=$_GET['tak'];


$kategorija=getkategorijenew();
$klubovi=getKlubovi();
//print_r($klubovi);


if (IsSet($_POST['izmena']))
{
	$idSavez=$_POST['idSavez'];
	if (strlen($idSavez)<1) $idSavez="NULL";
	$result = mysql_query("UPDATE  `takmicari` SET  `ime` =  '".$_POST['ime']."', `prezime` =  '".$_POST['prezime']."', `idSavez`=".$idSavez.", `kategorija`=".$_POST['kat'].", `klub`=".$_POST['klub']." WHERE  `id` =".intval($_POST['id']));
}else if (IsSet($_POST['novi']))
{
	$idSavez=$_POST['idSavez'];
	if (strlen($idSavez)<1) $idSavez="NULL";
	$result = mysql_query("INSERT INTO `takmicari` (`id`, `ime`, `prezime`, `idSavez`, `mail`, `notes`, `si`, `kategorija`, `klub`, `drzava`) VALUES (NULL, '".$_POST['ime']."', '".$_POST['prezime']."', ".$idSavez.", NULL, NULL, NULL, '".$_POST['kat']."', '".$_POST['klub']."', '1');");
}

echo "<div align=center><form method=post action=uredjivanjeKlubova.php?>";
echo "<select name=klub>";
		foreach($klubovi as $key=>$value)
		{
		echo "<option value='".$key."'";
		if (intval($key)==intval($klubId)) echo " selected";
		echo ">".$value."</option>";
		}
		echo "</select>";
echo "</select><input type=submit name=Klub value=Klub></form></div>";



$result = mysql_query("SELECT * FROM `takmicari` WHERE `klub` =  ".$klubId." ORDER BY  `kategorija` ASC", $sql);

echo "<table border=1 id=prijave cellspacing=0 align=center><tr BGCOLOR=olive><td><b>Ime</b></td><td><b>Prezime</b></td><td><b>ID Saveza</b></td><td><b>Kategorija</b></td><td><b>Klub</b></td><td><b>Akcija</b></td></tr>";
while ($row = mysql_fetch_assoc($result)) {

	if (intval($row['id'])!=$tak)
	{
		echo "<tr><td>".$row['ime']."</td><td>".$row['prezime']."</td><td>".$row['idSavez']."</td><td>";
		echo $kategorija[$row['kategorija']]."</td><td>".$klubovi[$row['klub']]."</td>";
				
		echo "<td><a href=uredjivanjeKlubova.php?izmeni&tak=".$row['id']."&klub=".$klubId.">Izmeni</a>";
		
		echo "</td></tr>";
	}
	else
	{

		echo "<form method=post action=uredjivanjeKlubova.php?klub=".$klubId."#jump>";

		echo "<tr><td><input type=text name=ime value=".$row['ime']." id=jump></td><td><input type=text name=prezime value=".$row['prezime']."></td><td><input type=text name=idSavez size=4 value=".$row['idSavez']."></td><td>";
		//echo $kategorija[$row['kategorija']]."</td>";

		echo "<select name=kat>";
		foreach($kategorija as $key=>$value)
		{
		echo "<option value='".$key."'";
		if (intval($key)==intval($row['kategorija'])) echo " selected";
		echo ">".$value."</option>";
		}
		echo "</select></td>";



		echo "<td><select name=klub>";
		foreach($klubovi as $key=>$value)
		{
		echo "<option value='".$key."'";
		if (intval($key)==intval($row['klub'])) echo " selected";
		echo ">".$value."</option>";
		}
		echo "</select></td>";
		
				
		echo "<td><input type=hidden name=id value=".$row['id']."><input type=hidden name=izmena value=1><input type=submit value='Izvrsi izmenu'>";
		
		echo "</td></tr></form>";


	}
}

//novi takmicar

		echo "<form method=post action=uredjivanjeKlubova.php?klub=".$klubId."#jump>";

		echo "<tr><td><input type=text name=ime></td><td><input type=text name=prezime ></td><td><input type=text name=idSavez size=4></td><td>";

		echo "<select name=kat>";
		foreach($kategorija as $key=>$value)
		{
		echo "<option value='".$key."'";
		echo ">".$value."</option>";
		}
		echo "</select></td>";


		echo "<td><select name=klub>";
		foreach($klubovi as $key=>$value)
		{
		echo "<option value='".$key."'";
		if (intval($key)==intval($klubId)) echo " selected";
		echo ">".$value."</option>";
		}
		echo "</select></td>";
		
				
		echo "<td><input type=hidden name=novi value=1><input type=submit value='Dodaj takmicara'>";
		
		echo "</td></tr></form>";

echo "</table>";
echo "<br><br>* ORIS ne podrzava brisanje takmicara jer to moze napraviti problem u bazi. <br>Umesto brisanja, promenite ime takmicara u Obrisano i uklonite ID Saveza<br><br>";
include "footer.php";

?>