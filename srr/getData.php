<?PHP

session_start();


$takmicari=$_SESSION["takmicari"];
if(!isset($takmicari) || isset($_GET["reload"])){
include "procesBaze.php";
$_SESSION["takmicari"]=$takmicari;
}

//print_r($takmicari);

$tak=7;
if(isset($_GET['tak'])) $tak=$_GET['tak'];
$last=0;
if(isset($_GET['last'])) $last=$_GET['last'];
$start="11:00:00";
if(isset($_GET['start'])) $start=$_GET['start'];
$slow=0;
if(isset($_GET['slow'])) $slow=1;

require_once( '../functions.php' ); 
$sql=mysqlConnect("");
$result = mysql_query("SELECT * FROM `srr` where srr.tak=".$tak." ORDER by datum ASC", $sql);
$skip=1;

$vreme = explode(":", $start);
$start=(intval($vreme[0])*60+intval($vreme[1]))*60+intval($vreme[2]);


if($last==0) $skip=0;
$tak=array();
while ($row = mysql_fetch_assoc($result)) {
    $si=intval($row["si"]);
    if($skip && $si!=$last) continue;
    else if($si==$last){
            $skip=0;
            continue;
    }
    $si=intval($row["si"]);
    $info=$takmicari[$si];
    if($info!=null)
    {
    $takmicar=array();
    $takmicar["datum"]=$row["datum"];
    $takmicar["si"]=$row["si"];
    $takmicar["ime"]=$info["ime"];
    $takmicar["prezime"]=$info["prezime"];
    $takmicar["kat"]=$info["kat"];
    $takmicar["klub"]=$info["klub"];
    $takmicar["start"]=$info["start"];

    $vr = explode(":", $info["start"]);
    $vreme=intval($vr[0])*60+intval($vr[1]);
    $vreme=$vreme+$start;
    $time=explode(" ", $row["datum"]);
    $time=$time[1];
    $time = explode(":", $time);
    $time=(intval($time[0])*60+intval($time[1]))*60+intval($time[2]);
    $vreme=$time-$vreme;
    $time="".intval($vreme / 60).":";
    if($vreme % 60 < 10) $time.="0";
    $time.=($vreme % 60);
    if($vreme<0) $time="loš start";
    $takmicar["vreme"]=$time;

    $tak[]=$takmicar;
    if($slow)
        break;
    }else
    {
    $takmicar=array();
    $takmicar["datum"]=$row["datum"];
    $takmicar["si"]=$row["si"];
    $takmicar["ime"]="";
    $takmicar["prezime"]="";
    $takmicar["kat"]="";
    $takmicar["klub"]="";
    $takmicar["start"]="";
    $takmicar["vreme"]="";
    $tak[]=$takmicar;
    if($slow)
        break;
    }


}
$tak = array_reverse($tak);
$json=json_encode($tak);
echo $json;

?>
