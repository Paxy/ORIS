<?PHP
error_reporting(E_ALL ^ E_DEPRECATED); 
function mysqlConnect($godina="")
{
$sql = mysql_connect('123', '321', '123');

if (!$sql ) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("orijenti_oris".$godina,$sql);
mysql_set_charset('utf8',$sql);
return $sql;
}
 
 
function logged()
{
 
if (!IsSet($_SESSION['klubId'])) 
    echo header("Location: login.php");
else
    return $_SESSION['klubId'];
 
}
 
    function sqludatum($datum) 
    {
        if ($datum==null) return "";
        $dat = explode("-", $datum);
        //list($godina, $mesec, $dan) = explode('[/.-]', $datum);
        $datum1 = date("d.m.Y",mktime(0,0,0,$dat[1], $dat[2], $dat[0])); 
        return $datum1;
    }
 
    function datumusql($datum) 
    {
        $dat = explode(".", $datum);
        //list($dan, $mesec, $godina) = explode('[/.-]', $datum);
        $datum1 = date("Y-m-d",mktime(0,0,0,$dat[1], $dat[0], $dat[2])); 
        return $datum1;
    }
 
function logout()
{
setcookie(session_name(), '', time()-42000, '/');
}
 
function rstoarray($res)
{
$someArr = array();
while( list($val) = mysql_fetch_row($res) ) {
$someArr[] = $val;
}
return $someArr;
}


function getKlubovi() 
{
	$klubovi=array();
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `id`,`naziv` FROM `klubovi` WHERE `id`<500 ORDER BY id ASC", $sql);
	while ($row = mysql_fetch_assoc($result)) {
	$klubovi[$row['id']]=$row['naziv'];
	}
	
	return $klubovi;

}
 
function getkategorije() // odbaciti ovu funkciju, pravi bug sa rednim brojevima kategorije
{
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `naziv` FROM `kategorije`", $sql);
    return rstoarray($result);
}
 
function getkategorijenew($eng=false) 
{
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `id`,`naziv` FROM `kategorije`", $sql);
    $kat=array();
    while ($row = mysql_fetch_assoc($result)) 
    {
        if ($eng)
            $kat[$row['id']]=str_replace("Ž", "W", $row['naziv']);
        else
            $kat[$row['id']]=$row['naziv'];
    }
    return $kat;    
}
 
 
function getprijavljeni($takmicenje)
{
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `takmicar` FROM `prijave` WHERE `takmicenje` LIKE ".$takmicenje, $sql);
    return rstoarray($result);
}
 
function getptakmicartakinfo($takmicenje, $takmicar)
{
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `kategorija`,`si` FROM `prijave` WHERE `takmicenje` LIKE ".$takmicenje." AND `takmicar` LIKE ".$takmicar, $sql);
    $row = mysql_fetch_assoc($result);
    return array($row['kategorija'], $row['si']);
}
 
function session_started(){
    if(isset($_SESSION)){ return true; }else{ return false; }
}
 
function session(){
if (!session_started())
{
session_start();
ob_start();
}
}
 
function gettdani($takmicenje, $takmicar)
{
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `dana` FROM `prijave` WHERE `takmicenje` = ".$takmicenje." AND `takmicar` = ".$takmicar, $sql);
    $row = mysql_fetch_assoc($result);
    return $row['dana'];
}
 
function getdana($takmicenje){
    $sql=mysqlConnect();
    $result = mysql_query("SELECT `dana` FROM `takmicenja` WHERE `id` = ".$takmicenje, $sql);
    $row = mysql_fetch_assoc($result);
    return $row['dana'];
}

function getOFParams($tak,$dan)
{
	$result = mysql_query("SELECT * FROM `of_params` WHERE `tak` = ".$tak." AND `dan` = ".$dan);
	$params=array();
	while($row = mysql_fetch_assoc($result))
				$params[$row['param']]=$row['vrednost'];
	return $params;
}
 
 
?>
