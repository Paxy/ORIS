<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
session_start();
ob_start();

$sql=mysqlConnect();

if (IsSet($_POST['naziv']))
{

include "hedder.php";

if (strlen($_POST['pass'])<4) die('Password should be longer ther 3 chars !');
if ($_POST['pass']!=$_POST['pass1']) die('Passwords do not matches !');

$sqlq="INSERT INTO `noviklubovi` (`naziv`, `skraceno`, `kontakt`, `mail`,`notes`, `pass` ,`drzava`) VALUES ('".$_POST['naziv']."', '".$_POST['skraceno']."', '".$_POST['kontakt']."', '".$_POST['mail']."', '".$_POST['notes']."', '".md5($_POST['pass'])."','".$_POST['drzava']."');";
$result = mysql_query($sqlq, $sql);

//echo $sqlq;

$poruka="Klub pod nazivom \"".$_POST['naziv']."\" zeli da dobije pristup ORIS portalu.\n\nLink za dozvolu registracije kluba: http://oris.orijentiring.rs/admin.php?noviKlub=".urlencode($_POST['naziv'])."&nkl=Dozvli+novi+klub";
mail('petar.bojovic@paxy.in.rs', 'ORIS Novi klub', $poruka);




echo "<br><br><br><div align=center>Please wait for administrator registration approval.<br>After approval you can login and register competitors.<br>You will receive mail when you got approval.<br><br>";
include "footer.php";
}
else
{

include "hedder.php";

echo "<form method=\"POST\" action=\"regKlub.php\">";
echo "<div align=center>For club registration, please fill form and wait for administrator registration approval.<br>After approval you can login and register competitors.<br><br>";
echo "<table align=center>";
echo "<tr><td>Club name:</td><td><input type=text name=naziv></td></tr>";
echo "<tr><td>Club short:</td><td><input type=text name=skraceno></td></tr>";
echo "<tr><td>Contact person:</td><td><input type=text name=kontakt></td></tr>";
echo "<tr><td>Mail:</td><td><input type=text name=mail></td></tr>";
echo "<tr><td>Password:</td><td><input type=password name=pass></td></tr>";
echo "<tr><td>Confirm password:</td><td><input type=password name=pass1></td></tr>";

$result = mysql_query("SELECT * FROM `drzave` ", $sql);
echo "<tr><td>Country:</td><td><select name=drzava>";

while ($row = mysql_fetch_assoc($result)) {
echo "<option value='".$row['id']."'";
echo ">".$row['naziv']."</option>";
}
echo "</select>";
echo "</td></tr>";
echo "<tr><td>Country (if other):</td><td><input type=text name=notes></td></tr>";

echo "<tr><td></td><td><input type=submit value='Send request'></td></tr>";
echo "</table></form>";


include "footer.php";
}

?>