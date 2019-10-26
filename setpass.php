<?PHP

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$sql=mysqlConnect();

if (IsSet($_SESSION['klubId1']) && IsSet($_POST['kontakt']))
{
if (strlen($_POST['pass'])<4) die('Sifra mora biti duza od 3 karaktera !');
if ($_POST['pass']!=$_POST['pass1']) die('Ponovljena sifra mora biti ista kao originalno unesena !');

$sqlq="UPDATE `klubovi` SET `kontakt` = '".$_POST['kontakt']."', `pass` = '".md5($_POST['pass'])."' WHERE `klubovi`.`id` = ".$_SESSION['klubId1'];
$result = mysql_query($sqlq,$sql);

$_SESSION['klubId'] = $_SESSION['klubId1'];
echo header("Location: index.php");

}
else
{

include "hedder.php";

$result = mysql_query("SELECT * FROM `klubovi` WHERE `id`=".$_SESSION['klubId1'],$sql);
$row = mysql_fetch_assoc($result);

echo "<form method=\"POST\" action=\"setpass.php\">";
echo "<div align=center>Ova forma se pojavljuje kada se korisnik prvi put prijavljuje na sistem. <br>Unesite sifru sa kojom zelite da pristupate sistemu i podatke o odgovornoj osobi (Ime i prezime).</div>
<p align=center></p>";
echo "<table align=center>";
echo "<tr><td>Klub:</td><td><b>".$row['naziv']."</b></td></tr>";
echo "<tr><td>Odgovorna osoba:</td><td><input type=text name=kontakt></td></tr>";
echo "<tr><td>Unesite sifru:</td><td><input type=password name=pass></td></tr>";
echo "<tr><td>Ponovite sifru:</td><td><input type=password name=pass1></td></tr>";
echo "<tr><td></td><td><input type=submit value=Izvrsi></td></tr>";
echo "</table></form>";

include "footer.php";
}

?>
