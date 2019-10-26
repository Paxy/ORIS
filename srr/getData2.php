<?PHP
/*
if (!session_started())
{
session_start();
ob_start();
}

$takmicari=$_SESSION["takmicari"];
if(!isset($takmicari)){
include "procesBaze.php";
$_SESSION["takmicari"]=$takmicari;
}

print_r($takmicari);
*/
$tak=6;
if(isset($_GET['tak'])) $tak=$_GET['tak'];
$last=0;
if(isset($_GET['last'])) $last=$_GET['last'];

require_once( '../functions.php' ); 
$sql=mysqlConnect("");
$result = mysql_query("SELECT srr.datum,`srr`.`si`,ime,prezime,kategorije.naziv as kat,klubovi.naziv as klub FROM `srr`,`prijave`,`takmicari`,`kategorije`,`klubovi` where srr.tak=prijave.takmicenje and srr.si=prijave.si and takmicari.id=prijave.takmicar and `klubovi`.`id`=takmicari.klub and kategorije.id=prijave.kategorija and srr.tak=".$tak." ORDER by datum ASC", $sql);
$skip=1;
if($last==0) $skip=0;
$tak=array();
while ($row = mysql_fetch_assoc($result)) {
    $si=intval($row["si"]);
    if($skip && $si!=$last) continue;
    else if($si==$last){
            $skip=0;
            continue;
    }
    $takmicar=array();
    $takmicar["datum"]=$row["datum"];
    $takmicar["si"]=$row["si"];
    $takmicar["ime"]=$row["ime"];
    $takmicar["prezime"]=$row["prezime"];
    $takmicar["kat"]=$row["kat"];
    $takmicar["klub"]=$row["klub"];
    $tak[]=$takmicar;

}
$tak = array_reverse($tak);
$json=json_encode($tak);
echo $json;

?>
