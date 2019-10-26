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
$kategorija=$_GET["kategorija"];
$si=$_GET["si"];
$dana=$_GET["dani"];

if ($si=="") $si="NULL";

$sql=mysqlConnect();

$sqlq="SELECT `status` FROM `takmicenja` WHERE `id`='".$takmicenje."'";
$result = mysql_query($sqlq,$sql);
$row = mysql_fetch_assoc($result);
if ($row['status']!='0') 
{
echo "Prijava zavrsena !";
return;
}

$sqlq="SELECT  * FROM `prijave` WHERE `takmicenje` = ".$takmicenje." AND `si` = ".$si;
$result = mysql_query($sqlq,$sql);


if (strcmp($si,"NULL")==0 || mysql_num_rows($result)==0)  // posoji si prijavljen 
{
    
	$sqlq="INSERT INTO .`prijave` (`takmicenje`, `takmicar`, `kategorija`, `si`, `dana`) VALUES ('".$takmicenje."', '".$takmicar."', '".$kategorija."', ".$si.", ".$dana.");";
	//echo $sqlq;
$result = mysql_query($sqlq, $sql);
    //echo $sqlq;
if (!$result) return mysql_error();
$result = mysql_query("UPDATE  `takmicari` SET  `si` =  ".$si." WHERE  `takmicari`.`id` = ".$takmicar,$sql);
if (!$result) return mysql_error();
else
echo "OK";

}else echo "Duplikat SI";
?>