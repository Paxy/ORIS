<?php
//SELECT `takmicari`.`idSavez`,`takmicari`.`id`, `rezultati`.`minut`, `rezultati`.`sekund` FROM `rezultati`,`takmicari`,`bodovanje` WHERE `rezultati`.`reg`=`takmicari`.`idSavez` AND `rezultati`.`kategorija`=`takmicari`.`kategorija` AND `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `rezultati`.`disq`=0 AND `rezultati`.`takmicenje`=3 AND `rezultati`.`dan`=1 AND `bodovanje`.`liga1v`=1 AND `takmicari`.`kategorija`=4 ORDER BY `minut` ASC, `sekund` ASC

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
$result = mysql_query("SELECT `idSavez` FROM `takmicari` ", $sql);
while($row = mysql_fetch_assoc($result))
	{
		$id=$row['idSavez'];
		if (strlen($id)==0) $id="NULL";
		$result1 = mysql_query("INSERT INTO `bodovanje` (`takmicar`) VALUES ('".$id."');");
}

?>