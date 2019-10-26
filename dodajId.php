<?PHP
require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);


if (IsSet($_SESSION['klubId']) && intval($_SESSION['klubId'])>1000)
{

$tak=$_GET["tak"];
$ime=$_GET["ime"];
$prezime=$_GET["prezime"];
$idSavez=$_POST["idsavez"];

echo $idSavez;

$sql=mysqlConnect();

$result = mysql_query("UPDATE `rezultati` SET  `reg` =  '".$idSavez."' WHERE  `rezultati`.`takmicenje` =".$tak." AND  `rezultati`.`ime` =  '".$ime."' AND  `rezultati`.`prezime` =  '".$prezime."';", $sql);
echo header("Location: rezultati.php?id=".$tak);
}








?>