<?php


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
require_once( 'bodovanjeV2.php' ); 

$sql=mysqlConnect();

cleanup();
die();
$result = mysql_query("SELECT `id`,`naziv` FROM `takmicenja` WHERE `status` = 2 ORDER BY `takmicenja`.`datum`  ASC");
while ($row = mysql_fetch_assoc($result)) 
	{		
		$rez=bodovanjeV2($row['id']);
	//if (intval($rez)<22) continue;
	//if (intval($rez)>1) break;
		echo $row['naziv']."(".$row['id'].") : ".$rez."<br>";
		if (strcmp($rez,"OK")!=0) break;
	}

function cleanup(){
$result = mysql_query("DELETE FROM `bodovanjeTakmicenjaV2`");
$result = mysql_query("DELETE FROM `bodovanjeTakmicaraV2`");
$result = mysql_query("DELETE FROM `bodovanjeTakmicenjaV2`");
$result = mysql_query("DELETE FROM `bodovanjeV2`");
$result = mysql_query("DELETE FROM `rangiraniV2`");

$result = mysql_query("insert into `rangiraniV2` (`id`) SELECT distinct `idSavez` from `takmicari` where `idSavez` > 0");
}




?>