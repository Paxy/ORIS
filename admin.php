<?PHP

require_once( 'functions.php' ); 
session();

$sql=mysqlConnect();


$klubId=logged();
if ($klubId<1000) echo header("Location: index.php");

include "hedder.php";

echo "<table width=600 align=center><tr><td align=center>";

if (IsSet($_POST['klubId']))
{

$klub=$_POST['klubId'];
$pass=$_POST['pass'];

$result = mysql_query("UPDATE `klubovi` SET `pass` = '".md5($pass)."' WHERE `klubovi`.`id` = ".$klub." LIMIT 1;", $sql);
echo "Sifra je uspesno postavljena !<br><br>";
}

if (IsSet($_GET['org']))
{
$tak=$_GET['takId'];
echo header("Location: organizator.php?id=".$tak);
}

if (IsSet($_GET['otvori']))
{
$tak=$_GET['takId'];
$result = mysql_query("UPDATE  `takmicenja` SET  `status` =  '0' WHERE  `takmicenja`.`id` = ".$tak, $sql);
echo "Prijava je opet otvorena !<br><br>";
}

if (IsSet($_GET['zatvori']))
{
$tak=$_GET['takId'];
$result = mysql_query("UPDATE  `takmicenja` SET  `status` =  '1' WHERE  `takmicenja`.`id` = ".$tak, $sql);
echo "Prijava je zatvorena !<br><br>";
}

if (IsSet($_GET['bdTakmicenja']))
{
$tak=$_GET['bdTakmicenja'];
require_once( 'bodovanjeTakmicenja.php' ); 
echo "Bodovanje takmicenja: ". bodovanjeTakmicenja($tak)."<br>";
}

if (IsSet($_GET['bodovanje']))
{
require_once( 'bodovanjeUkupno.php' ); 
echo "Azuriranje ukupnih bodova: ". bodovanjeUkupno(1)."<br>";
}

if (IsSet($_GET['nkl']))
{
$result = mysql_query("SELECT count(*) FROM `klubovi` WHERE `id`>=100 AND `id`<500", $sql);
$row = mysql_fetch_assoc($result);
$br=intval($row['count(*)'])+100;

$sqlq="SELECT * FROM `noviklubovi` WHERE `naziv`='".$_GET['noviKlub']."'";
$result = mysql_query($sqlq, $sql);
$row = mysql_fetch_assoc($result);

$sqlq="INSERT INTO `klubovi` (`id`, `naziv`, `skraceno`, `kontakt`, `adresa`, `grad`, `posta`, `telefon`, `fax`, `mail`, `www`, `notes`, `drzava`, `pass`) VALUES ('".$br."', '".$row['naziv']."', '".$row['skraceno']."', '".$row['kontakt']."', '', '', '', '', '', '".$row['mail']."', '', '".$row['notes']."', '".$row['drzava']."', '".$row['pass']."');";
$result = mysql_query($sqlq, $sql);
//echo $sqlq;

$sqlq="DELETE FROM `noviklubovi` WHERE `naziv`='".$_GET['noviKlub']."'";
$result = mysql_query($sqlq, $sql);

$poruka="Your club registration at ORIS is accepted by administrators.\nNow you can log in with password filled in form, and register competitors.\n\nORIS url: http://oris.orijentiring.rs \n\nBest regards,\nORIS Administration Team";
mail($row['mail'], '[ORIS] Club accepted', $poruka);


}

echo "Uredivanje klubova";
echo "<form method=\"send\" action=\"uredjivanjeKlubova.php\"><select name=\"klub\">";
	

	$result = mysql_query("SELECT `id`,`naziv` FROM `klubovi` WHERE `id`<500 ORDER BY id ASC", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=org value=\"Uredivanje klubova\"></form>";

echo "<br><br>";


echo "Administriranje takmicenja";
echo "<form method=\"send\" action=\"admin.php\"><select name=\"takId\">";
	

	$result = mysql_query("SELECT `id`,`naziv` FROM `takmicenja` WHERE `status`<5", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=org value=\"Administracija\"></form>";

echo "<br><br>";

echo "Ponistavanje sifre";
echo "<form method=\"POST\" action=\"admin.php\"><select name=\"klubId\">";
	$result = mysql_query("SELECT `id`,`naziv` FROM `klubovi` WHERE `id`<1000", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"password\" name=\"pass\" size=14><br><input type=\"submit\" value=\"Izvrsi\"></form>";

echo "<br><br>";

echo "Otvaranje prijava";
echo "<form method=\"send\" action=\"admin.php\"><select name=\"takId\">";

	$result = mysql_query("SELECT `id`,`naziv` FROM `takmicenja`", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=otvori value=\"Otvori prijavu\"></form>";


echo "<br><br>";

echo "Zatvaranje prijava";
echo "<form method=\"send\" action=\"admin.php\"><select name=\"takId\">";

	$result = mysql_query("SELECT `id`,`naziv` FROM `takmicenja`", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=zatvori value=\"Zatvori prijavu\"></form>";
	
	echo "<br><br>";

echo "Bodovanje takmicenja";
echo "<form method=\"send\" action=\"admin.php\"><select name=\"bdTakmicenja\">";

	$result = mysql_query("SELECT `id`,`naziv` FROM `takmicenja` WHERE `status`=2", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['id']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=bdtk value=\"Boduj takmicenje\"></form>";

echo "<br><br>";
echo "<form method=\"send\" action=\"admin.php\"><input type=submit name=bodovanje value=\"Bodovanje svih trka\"></form>";


echo "<br><br>";

echo "Prihvatanje novih klubova";
echo "<form method=\"send\" action=\"admin.php\"><select name=\"noviKlub\">";

	$result = mysql_query("SELECT `naziv` FROM `noviklubovi` ", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	echo "<option value=\"".$row['naziv']."\">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"submit\" name=nkl value=\"Dozvoli novi klub\"></form>";


echo "</td></tr></table>";



include "footer.php";

?>