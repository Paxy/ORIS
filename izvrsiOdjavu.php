<?PHP
header("Cache-Control: no-cache, must-revalidate");
 // Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$takmicenje=$_GET["takmicenje"];
$takmicar=$_GET["takmicar"];

$sql=mysqlConnect();

$sqlq="SELECT `status` FROM `takmicenja` WHERE `id`='".$takmicenje."'";
$result = mysql_query($sqlq,$sql);
$row = mysql_fetch_assoc($result);
if ($row['status']!='0') 
{
echo "Prijava zavrsena, odjava neuspesna !";
return;
}


$result = mysql_query("DELETE FROM `prijave` WHERE `prijave`.`takmicenje` = ".$takmicenje." AND `prijave`.`takmicar` = ".$takmicar, $sql);
if (!$result) echo mysql_error();
else
echo "OK";

?>