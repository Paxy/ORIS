<?PHP
include "hedder.php";

$takid=$_GET['id'];
$file=$_GET['file'];

echo "<form method=\"POST\" action=\"kramerGenerator.php?id=".$takid."&file=".$file."&eng&utf8\">";
echo "<div align=center>Unesite parametre za pristup OrienteeringOnline.org preko kojih se mogu izlistati svi prijavljeni takmicari. </div>
<p align=center></p>";
echo "<table align=center>";
echo "<tr><td>Mail:</td><td><input type=text name=oouser></td></tr>";
echo "<tr><td>Sifra:</td><td><input type=password name=oopass></td></tr>";
echo "<tr><td>Identifikacioni broj takmicenja (CompetitionID):</td><td><input type=text name=ooid></td></tr>";
echo "<tr><td></td><td><input type=submit value=Izvrsi></td></tr>";
echo "</table></form>";

include "footer.php";

?>