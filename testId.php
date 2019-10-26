<?PHP


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 


function fixId($tak)
{
	
$sql=mysqlConnect();
$result = mysql_query("Select `idSavez`,`reg` from `rezultati`,`takmicari` WHERE `rezultati`.`ime`=`takmicari`.`ime` AND `rezultati`.`prezime`=`takmicari`.`prezime` AND `rezultati`.`reg`!= `takmicari`.`idSavez` AND `takmicari`.`kategorija`=`rezultati`.`kategorija` AND `rezultati`.`takmicenje`=".$tak, $sql);
while($row = mysql_fetch_assoc($result))
	{
		$idSavez=$row['idSavez'];
		$reg=$row['reg'];	
		$result = mysql_query("UPDATE `rezultati` SET  `reg` =  '".$idSavez."' WHERE  `rezultati`.`takmicenje` =".$tak." AND `reg`='".$reg."';", $sql);
		echo $idSavez . " -> " . $reg;
	}

}

include 'bodovanjeTakmicenja.php';
fixByName();

?>