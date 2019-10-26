<?PHP
header("Cache-Control: no-cache, must-revalidate");
 // Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include "hedder.php";
 

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$id=$_GET["id"];
$ime=$_GET["ime"];
$tak=$_GET["tak"];

if (!IsSet($_GET['siguran']))
{
	echo "<div align=center>Da li ste sigurni da zelite da obrisete takmicara ".$ime.", cime brisete i sve njegove prijave i rezultate iz ORIS baze !<br><a href=obrisiTakmicara.php?id=".$id."&siguran=1&tak=".$tak.">Da</a> | <a href='javascript:history.go(-1)'>Ne</a></div><br><br><br><br>";
	
}
else
{

$sql=mysqlConnect();

$result = mysql_query("SELECT * FROM `takmicari` WHERE `id` LIKE ".$id, $sql);
$row = mysql_fetch_assoc($result);
if (IsSet($_SESSION['klubId']) && intval($row['idSavez'])<1) 
if ($_SESSION['klubId']==$row['klub'] || intval($_SESSION['klubId'])>1000)
{
		
		$result = mysql_query("DELETE FROM `prijave` WHERE `takmicar` = ".$id, $sql); 
		$result = mysql_query("DELETE FROM `takmicari` WHERE `id` = ".$id, $sql); 
		
		
		echo "<div align=center>Takmicar je izbrisan iz ORIS baze<br><a href='prijavaTakmicara.php?takmicenje=".$tak."'>Nazad</a></div><br><br><br>";
		
}else
die("Nemate prava da izvrsite ovu operaciju !");
else
die("Nemate prava da izvrsite ovu operaciju !");


}

include "footer.php";


?>