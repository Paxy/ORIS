<?PHP
header("Cache-Control: no-cache, must-revalidate");
 // Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

require_once( 'functions.php' ); 
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);


$ime=iconv(mb_detect_encoding($_GET["ime"]), 'UTF8//TRANSLIT', $_GET["ime"]);
$prezime=iconv(mb_detect_encoding($_GET["prezime"]), 'UTF8//TRANSLIT',$_GET["prezime"]);
$kategorija=$_GET["kategorija"];
$si=$_GET["si"];
$beleska=$_GET["beleska"];
$klub=$_GET["klub"];

if ($si=="") $si="NULL";

$sql=mysqlConnect();
//mysql_set_charset('CP1250',$sql);

$sqlq="SELECT `drzava` FROM `klubovi` WHERE `id` = ".$klub;
$result = mysql_query($sqlq,$sql);
$row = mysql_fetch_assoc($result);
$drzava=$row['drzava'];

$result = mysql_query("INSERT INTO `takmicari` (`id`, `ime`, `prezime`, `idSavez`, `mail`, `notes`, `si`, `kategorija`, `klub`, `drzava`) VALUES (NULL, '".$ime."', '".$prezime."', NULL, '', '".$beleska."', ".$si.", '".$kategorija."', '".$klub."', '".$drzava."');", $sql);

$sqlq="SELECT `id` FROM `takmicari` WHERE `klub` = ".$klub." AND `ime`='".$ime."' AND `prezime`='".$prezime."'";
$result = mysql_query($sqlq,$sql);
$row = mysql_fetch_assoc($result);

$tk=$row['id'];
$takId=$_GET['takId'];
//echo $tk;

$sqlq="SELECT `dana` FROM `takmicenja` WHERE `id` = ".$takId;
$result = mysql_query($sqlq,$sql);
$row = mysql_fetch_assoc($result);
$dana=intval($row['dana']);
$ud=0;
for ($i=0;$i < $dana; $i++)
	$ud+=pow(2,$i);

$sqlq="INSERT INTO `prijave` (`takmicenje`, `takmicar`, `kategorija`, `si`, `dana`) VALUES ('".$takId."', '".$tk."', '".$kategorija."', ".$si.", ".$ud.");";
$result = mysql_query($sqlq,$sql);


echo "OK";

?>