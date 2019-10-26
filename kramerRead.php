<?PHP

//$takmicenje=8;
//$filename="upload/res.csv";

//echo kramerRead(8,"upload/res.csv");

function kramerRead($takmicenje,$filename,$bodujDan)
{

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once( 'functions.php' ); 

$sql=mysqlConnect();
mysql_query("SET CHARACTER SET 'cp1250'", $sql); 
mysql_query("SET NAMES 'cp1250'", $sql);

$kategorije=getkategorijenew(true);
if($bodujDan!=0)
    $dan=$bodujDan;
else $dan=1;
$result = mysql_query("SELECT * FROM  `lige` WHERE  `takmicenje` =".$takmicenje." AND  `liga` =1 and `dan`=".$dan,$sql);
if ($row = mysql_fetch_assoc($result)) return "REZULTATI SU VEC UCITANI";
				
$result = mysql_query("SELECT `dana` FROM `takmicenja` WHERE `id` = ".$takmicenje,$sql);
$row = mysql_fetch_assoc($result);
$dana=intval($row['dana']);

$nationalId=false;
				
if (($handle = fopen($filename, "r")) !== FALSE) {
		$data = fgetcsv($handle,0, ";");
		$head = implode(",", $data);
    
		if (strpos($head,"Chip1")) // MT
		 while (($data = fgetcsv($handle,0, ";")) !== FALSE) 
		 {
    	//if (strlen($data[7])==0) continue;
    	$id=$data[2]; //DBId
    	$prezime=$data[8];
    	$ime=$data[9];
    	if (strlen($id)==0) 
		$id="NULL";
			//{
			//	$sqlq="SELECT `id`  FROM `takmicari` WHERE (`ime` LIKE '".$ime."' AND `prezime` LIKE '".$prezime."') OR (`ime` LIKE '".$prezime."' AND `prezime` LIKE '".$ime."')";
			//	$result = mysql_query($sqlq);
			//	if ($row = mysql_fetch_assoc($result))
			//	$id=$row['id'];
			//	else
				$id="NULL";
			//}
    	$dan=0;
    	while ($dan < $dana )
    	{
    	$vreme=$data[($dan*5)+27];
    	if (strlen($vreme)==0){
    		 $dan++;
    		 continue;
    		}
    	//list($minut, $sekund) = split(':', $vreme);
			$split=split(':', $vreme);
				
			if (count($split)>2)
			{
			$minut=intval($split[0])*60+intval($split[1]);
			$sekund=intval($split[2]);
			}
			else
			{
			$minut=intval($split[0]);
			$sekund=intval($split[1]);
			}
    	$disq=$data[($dan*5)+28];
      //echo "ID Saveza: ".$data[7].", Ime i prezime: ".$data[8]." ".$data[9].", Dan: ".($dan+1)." Minut: ".$minut." Sekund: ".$sekund."<br>"; 
      $sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`, `prezime`, `ime`, `klub`) VALUES ('".$takmicenje."', ".$id.", '".($dan+1)."', '".$minut."', '".$sekund."', '".$disq."', '".$data[58]."', '".$prezime."', '".$ime."', '".$data[56]."' )";
      //echo $sqlq;
     
      $result = mysql_query($sqlq);
      $dan++;
    	}
		}	
		else if (strpos($head,"Chipno1")) //MT2013
			while (($data = fgetcsv($handle,0, ";")) !== FALSE) 
		 {
    	//if (strlen($data[7])==0) continue;
			$id=$data[9];
			$prezime=$data[10];
			$ime=$data[11];
			if (strlen($id)==0) 
			$id="NULL";
			$dan=0;
			while ($dan < $dana )
				{
				$vreme=$data[($dan*8)+29];
				if (strlen($vreme)==0){
				$dan++;
				continue;
				}
			$split=split(':', $vreme);
				
			if (count($split)>2)
			{
			$minut=intval($split[0])*60+intval($split[1]);
			$sekund=intval($split[2]);
			}
			else
			{
			$minut=intval($split[0]);
			$sekund=intval($split[1]);
			}		
			$disq=$data[($dan*8)+30];
		//ech	 "ID Saveza: ".$data[7].", Ime i prezime: ".$data[8]." ".$data[9].", Dan: ".($dan+1)." Minut: ".$minut." Sekund: ".$sekund."<br>"; 
			$sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`, `prezime`, `ime`, `klub`) VALUES ('".$takmicenje."', ".$id.", '".($dan+1)."', '".$minut."', '".$sekund."', '".$disq."', '".$data[80]."', '".$prezime."', '".$ime."', '".$data[75]."' )";
			//echo $sqlq;
     
			$result = mysql_query($sqlq);
			$dan++;
			}
		}
		else if (strpos($head,"Chipno")) //OE2013
			while (($data = fgetcsv($handle,0, ";")) !== FALSE) {
    	//if (strlen($data[7])==0) continue;
			$id=$data[4];
			$prezime=$data[5];
			$ime=$data[6];
			if (strlen($id)==0) 
			$id="NULL";
			if($bodujDan!=0)
		       $dan=$bodujDan;
		    else
    	       $dan=1;

			$vreme=$data[13];
                echo $vreme;
			if (strlen($vreme)==0) continue;
    
			//list($minut, $sekund) = split(':', $vreme);
			$split=split(':', $vreme);
				
			if (count($split)>2)
			{
			$minut=intval($split[0])*60+intval($split[1]);
			$sekund=intval($split[2]);
			}
			else
			{
			$minut=intval($split[0]);
			$sekund=intval($split[1]);
			}		
			
			$disq=$data[14];
			//print "ID Saveza: ".$id.", Ime i prezime: ".$ime." ".$prezime.", Dan: ".$dan." Minut: ".$minut." Sekund: ".$sekund."<br>"; 
			$sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`, `prezime`, `ime`, `klub`) VALUES ('".$takmicenje."', ".$id.", '".$dan."', '".$minut."', '".$sekund."', '".$disq."', '".$data[24]."', '".$prezime."', '".$ime."', '".$data[19]."' )";
			echo $sqlq;
			$result = mysql_query($sqlq);
		}
		else //OE - OEvent
        {  
        
		while (($data = fgetcsv($handle,0, ";")) !== FALSE) {
    	//if (strlen($data[7])==0) continue;

        if(strlen($data[38])>0) $nationalId=true;

        if($nationalId)
    	    $id=$data[38]; //Nat. Id
        else
    	    $id=$data[2]; //DB Id

    	$prezime=$data[3];
    	$ime=$data[4];
    	if (strlen($id)==0) 
		$id="NULL";
		//	{
		//		$sqlq="SELECT `id`  FROM `takmicari` WHERE (`ime` LIKE '".$ime."' AND `prezime` LIKE '".$prezime."') OR (`ime` LIKE '".$prezime."' AND `prezime` LIKE '".$ime."')";
		//		//echo $sqlq;
		//		$result = mysql_query($sqlq);
		//		if ($row = mysql_fetch_assoc($result))
		//		$id=$row['id'];
		//		else
		//		$id="NULL";
		//	}
		if($bodujDan!=0)
		 $dan=$bodujDan;
		else
    	 $dan=1;
    	$vreme=$data[11];
    	if (strlen($vreme)==0) continue;
    
    	//list($minut, $sekund) = split(':', $vreme);
			$split=split(':', $vreme);
				
			if (count($split)>2)
			{
			$minut=intval($split[0])*60+intval($split[1]);
			$sekund=intval($split[2]);
			}
			else
			{
			$minut=intval($split[0]);
			$sekund=intval($split[1]);
			}
			$disq=$data[12];
      //print "ID Saveza: ".$id.", Ime i prezime: ".$ime." ".$prezime.", Dan: ".$dan." Minut: ".$minut." Sekund: ".$sekund."<br>"; 
      $kat=$data[17];
      // ako je ime aktegorije iz spiska onda overide id kategorije
      $katime=$data[18];
      if($katime[0]!='M')
      $katime='W'.substr($katime,1);
      $katid = array_search($katime, $kategorije);
      if($katid !== false) 
        $kat=$katid;

      $sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`, `prezime`, `ime`, `klub`) VALUES ('".$takmicenje."', ".$id.", '".$dan."', '".$minut."', '".$sekund."', '".$disq."', '".$kat."', '".$prezime."', '".$ime."', '".$data[14]."' )";
      //echo $sqlq;
      $result = mysql_query($sqlq);
      
        }    
    }
   
    	
  	
    fclose($handle);
}

		//print "ID Saveza: ".$id.", Ime i prezime: ".$ime." ".$prezime.", Dan: ".$j."., Minuti: ".$minuti.", Sekunde: ".$sekunde.", DISQ: ".$disq."<br>";
//		
		//echo $sqlq;
//		$result = mysql_query($sqlq);
		

ubaciLigu($takmicenje,$bodujDan);

return "OK";
}

function ubaciLigu($takid,$bodujDan){
$sql=mysqlConnect();	

if($bodujDan!=0){
    $result = mysql_query("INSERT INTO `lige` (`takmicenje`, `dan`, `liga`) VALUES ('".$takid."', '".$bodujDan."', '1');", $sql);
    return;
}

$result = mysql_query("SELECT  `dana` FROM `takmicenja` WHERE `id`=".$takid, $sql);
$row =  mysql_fetch_assoc($result);
$dana=intval($row['dana']);
for ($i=0;$i<$dana;$i++)
	$result = mysql_query("INSERT INTO `lige` (`takmicenje`, `dan`, `liga`) VALUES ('".$takid."', '".($i+1)."', '1');", $sql);
}

?>
