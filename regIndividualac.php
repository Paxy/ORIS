<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
session_start();
ob_start();

$sql=mysqlConnect();

if (IsSet($_POST['ime']))
{

if (strlen($_POST['pass'])<4) die('Sifra mora biti duza od 3 karaktera !');
if ($_POST['pass']!=$_POST['pass1']) die('Ponovljena sifra mora biti ista kao originalno unesena !');

$result = mysql_query("SELECT count(*) FROM `klubovi` WHERE `id`>=500 AND `id`<1000", $sql);
$row = mysql_fetch_assoc($result);
$br=intval($row['count(*)'])+500;

$sqlq="INSERT INTO `klubovi` (`id`, `naziv`, `skraceno`, `kontakt`, `adresa`, `grad`, `posta`, `telefon`, `fax`, `mail`, `www`, `notes`, `drzava`, `pass`) VALUES ('".$br."', 'Individualac', 'IND', '".$_POST['ime']." ".$_POST['prezime']."', '', '', '', '".$_POST['telefon']."', '', '".$_POST['mail']."', '', '', '".$_POST['drzava']."', '".md5($_POST['pass'])."');";
$result = mysql_query($sqlq, $sql);

$sqlq="INSERT INTO `takmicari` (`id`, `ime`, `prezime`, `idSavez`, `mail`, `notes`, `si`, `kategorija`, `klub`, `drzava`) VALUES (NULL, '".$_POST['ime']."', '".$_POST['prezime']."', '', '".$_POST['mail']."', '', NULL, '".$_POST['kategorija']."', '".$br."', '".$_POST['drzava']."');";
$result = mysql_query($sqlq, $sql);

//echo $sqlq;
$_SESSION['klubId'] = $br;
echo header("Location: index.php");

}
else
{

include "hedder.php";

echo "<form method=\"POST\" action=\"regIndividualac.php\">";
echo "<div align=center>Individualci koriste ovu formu, kada je potrebno prvi put da se registruju. <br>Svaki sledeci put treba izvrsiti samo logovanje unetom sifrom.</div>
<p align=center></p>";
echo "<table align=center>";
echo "<tr><td>Klub:</td><td><b>Individualac</b></td></tr>";
echo "<tr><td>Ime:</td><td><input type=text name=ime></td></tr>";
echo "<tr><td>Prezime:</td><td><input type=text name=prezime></td></tr>";
echo "<tr><td>Telefon:</td><td><input type=text name=telefon></td></tr>";
echo "<tr><td>Mail:</td><td><input type=text name=mail></td></tr>";
echo "<tr><td>Unesite sifru:</td><td><input type=password name=pass></td></tr>";
echo "<tr><td>Ponovite sifru:</td><td><input type=password name=pass1></td></tr>";

$result = mysql_query("SELECT * FROM `drzave` ", $sql);
echo "<tr><td>Drzava:</td><td><select name=drzava>";

while ($row = mysql_fetch_assoc($result)) {
echo "<option value='".$row['id']."'";
echo ">".$row['naziv']."</option>";
}
echo "</select>";
echo "</td></tr>";

$result = mysql_query("SELECT * FROM `kategorije` ", $sql);
echo "<tr><td>Kategorija:</td><td><select name=kategorija>";
while ($row = mysql_fetch_assoc($result)) {
echo "<option value='".$row['id']."'";
echo ">".$row['naziv']."</option>";
}
echo "</select>";
echo "</td></tr>";


echo "<tr><td></td><td><input type=submit value=Ubaci></td></tr>";
echo "</table></form>";


include "footer.php";
}

?>