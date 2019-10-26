<?php

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

if (!Isset($sql))
$sql=mysqlConnect();

$takid=$_GET['id'];


$result = mysql_query("DROP TABLE temp_prijave;");
$result = mysql_query("CREATE TABLE temp_prijave AS (SELECT * FROM prijave where prijave.takmicenje=".$takid.");");

$result = mysql_query("SELECT * FROM `spajanje` WHERE `tak` = ".$takid);
while ($row = mysql_fetch_assoc($result)) 
{
	$result1 = mysql_query("UPDATE `temp_prijave` SET `kategorija` = ".$row['ukat']." WHERE `kategorija` = ".$row['kat']);
}


?>