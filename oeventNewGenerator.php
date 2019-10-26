<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$takid=$_GET['id'];

$sql=mysqlConnect();
mysql_query("SET CHARACTER SET 'cp1250'", $sql); 
mysql_query("SET NAMES 'cp1250'", $sql);

$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id`=".$takid, $sql);
$takmicenje = mysql_fetch_assoc($result);
$dani=intval($takmicenje['dana']);

if (!IsSet($_GET['inline']))
{
$file=$_GET['file'];
header('Content-Disposition: attachment;filename="'.$file.'"');
}

$eng=0;
if (IsSet($_GET['eng']))
	$eng=1;



$sqlq="SELECT `prijave`.`si`, `takmicari`.`idSavez`, `ime`, `prezime`, `klubovi`.`naziv` AS klub,`kategorije`.`naziv` AS kat,`klubovi`.`id` AS idklub, `skraceno`, `kategorije`.`id` AS idkat, `prijave`.`dana` AS `dani`,`takmicari`.`id` FROM `prijave`,`takmicenja`,`takmicari`,`kategorije`,`klubovi` WHERE `prijave`.`takmicenje`=`takmicenja`.`id` AND `prijave`.`takmicar`=`takmicari`.`id` AND `prijave`.`kategorija`=`kategorije`.`id` AND `takmicari`.`klub`=`klubovi`.`id` AND `prijave`.`takmicenje`=".$takid." ORDER BY klubovi.id ASC";
$result = mysql_query($sqlq,$sql);
while ($row = mysql_fetch_assoc($result)) 
{

	$kat=$row['kat'];
	if ($eng) 
		if ($utf8)
			$kat=str_replace("Ž", "W", $kat);
		else
			$kat=str_replace("�", "W", $kat);
	
   
	if ($dani==1)
	{
		
		    echo $row['ime'].';'.$row['prezime'].';Srbija;SRB;'.$row['klub'].';'.$row['skraceno'].';'.$kat.';'.$row['si'].';;'.$row['idSavez'].';1;;1;
';
	
	}
    else
    {
		    echo $row['ime'].';'.$row['prezime'].';Srbija;SRB;'.$row['klub'].';'.$row['skraceno'].';'.$kat.';'.$row['si'].';;'.$row['idSavez'].';';
            for($i=0;$i<$dani;$i++)
                echo '1;';
            for($i=0;$i<$dani;$i++)
                echo ';';
            for($i=0;$i<$dani;$i++)
                if (intval($dani&(2^$i))>0) echo "1;";
    		else echo "0;";
            echo'
';
    }   
}

	
if(IsSet($_POST['ooid']))
{
include "oo.php";
$ooid=$_POST['ooid'];
$oouser=$_POST['oouser'];
$oopass=$_POST['oopass'];
generateOOData($oouser,$oopass,$ooid);


}	
	

?>
