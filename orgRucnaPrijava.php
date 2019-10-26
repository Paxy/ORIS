<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

session();

include "hedder.php";
echo "<script src=\"scripts.js\"></script>";

$sql=mysqlConnect();
$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id` LIKE ".$_GET['id'], $sql);
$row = mysql_fetch_assoc($result);

$takId=$row['id'];

if ($_SESSION['klubId']==$row['klub'] || intval($_SESSION['klubId'])>1000)
{

if (IsSet($_POST['status'])) echo $_POST['status'];

$result = mysql_query("SELECT DISTINCT `id`, `naziv` FROM `klubovi` WHERE `id`<1000 ORDER BY `naziv`", $sql);
//$row = mysql_fetch_assoc($result);
echo "Izaberite klub: <select id=klubPrijava onchange=\"prijavaTakmicara(this,".$takId.")\" onfocus=\"prijavaTakmicara(this,".$takId.")\">";

echo "<option value=''></option>";

while ($row = mysql_fetch_assoc($result)) {
echo "<option value='".$row['id']."'";
echo ">".$row['naziv']."</option>";
}
echo "</select>";
echo "<div id=prijava></div>";

}
else echo "Nemate prava za modifikaciju podataka nad izabranim takmicenjem !";



include "footer.php";




?>