<?PHP
require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

$id=$_GET["id"];

$sql=mysqlConnect();

$result = mysql_query("SELECT `klub`,`mesto` FROM `takmicenja` WHERE `id` = ".$id, $sql);
$row = mysql_fetch_assoc($result);

if (IsSet($_SESSION['klubId'])) 
if ($_SESSION['klubId']==$row['klub'] || intval($_SESSION['klubId'])>1000)
	$result = mysql_query("UPDATE  `takmicenja` SET  `status` =  '1' WHERE  `takmicenja`.`id` = ".$id, $sql);

//$file="oeventGenerator.php?id=".$id."&file=".trim($row['mesto']).".oev";
$file="organizator.php?id=".$id;
echo header("Location: ".$file);




?>