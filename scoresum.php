<?PHP
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

include "hedder.php";

if (IsSet($_FILES['uploadedfile']))
{
	
	if (!strpos($_FILES['uploadedfile']['name'],".csv"))
	die ("Ne podrzan fajl. Posaljite iskljucivo .csv fajl sa rezultatima");
	
$target_path = "./";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
	echo "Fajl poslat";
} else{
    echo "There was an error uploading the file, please try again!";
}

//$dana=2; // za koliko dana se racuna, ako je broj 3 onda se racuna 1. 2. i 3. dan.
$dana=$_POST['dana'];
$best=$_POST['best'];
//$best=0; // koliko najboljih rezultata se uzima za plasman, ako je 0 onda se uzimaju svi rezultati, ako 2 onda se uzimaju samo 2 najbolja rezultata
//$fajl="rez"; // fajl sa rezultatima
$fajl=$_FILES['uploadedfile']['name'];
$idx=strpos($fajl,".");
$fajl=substr($fajl,0,$idx);

$mt=1;

$sql = mysql_connect('db11.cpanelhosting.rs', 'oss_oris', 'GHKaQx32XKa7');
if (!$sql ) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("jos_2",$sql);
mysql_set_charset('cp1250',$sql);
//mysql_query("SET CHARACTER SET 'cp1250'", $sqlc); 
//mysql_query("SET NAMES 'cp1250'", $sqlc);

$sql="DROP TABLE `scoresum`";
$result = mysql_query($sql);
$sql = "CREATE TABLE `scoresum` (`si` INT NOT NULL, `ime` TEXT NOT NULL, `klub` TEXT NOT NULL, `kat` TEXT NOT NULL";
for ($i=1;$i<=$dana;$i++)
	$sql.=", `dan".$i."` TIME NOT NULL, `bod".$i."` FLOAT NOT NULL";
$sql.=", `ukupno` FLOAT NOT NULL) ENGINE = MyISAM;";
$result = mysql_query($sql);

$ob_file = fopen('./res/'.$fajl.'.html','w');
ob_start('ob_file_callback');


if (($handle = fopen($fajl.".csv", "r")) == FALSE) {
	echo  "Ne moze da se pristupi fajlu !";
}

		$data = fgetcsv($handle, 1000, ";");
		if (strcmp($data[2],"Database Id")==0)
			$mt=0;
		
		if ($mt)
		{		
			$katrb=60;
			$imerb=9;
			$prezimerb=8;
			$klubrb=55;
		}
		else
		{
			$katrb=18;
			$imerb=4;
			$prezimerb=3;
			$klubrb=14;
			$dana=1;
		}
		
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    		if (strlen($data[1])<4) continue;
    	
    		$sql= "INSERT INTO `scoresum` (`si` ,`ime` ,`kat`, `klub`";
    		for ($i=1;$i<=$dana;$i++)
    			$sql.=" ,`dan".$i."` ,`bod".$i."`";
    			
    		$sql.=	" ,`ukupno`)VALUES ('".$data[1]."', '".$data[$imerb]." ".$data[$prezimerb]."', '".$data[$katrb]."', '".$data[$klubrb]."'";
    		
    		for ($i=1;$i<=$dana;$i++)
    		{
    			if ($mt)
    			{
    				$vremerb=27+5*($i-1);
						$statusrb=28+5*($i-1);
					}
					else
					{
						$vremerb=9;
						$statusrb=12;
					}
				$vreme=$data[$vremerb];
				if (intval($data[$statusrb])!=0) $vreme="DISQ";
    		$sql.=", '".$vreme."', 0";
	    	}
	    	
	    	$sql.=", 0);";
			//	echo $sql."<br>";    	
				$result = mysql_query($sql);
    }
  
    fclose($handle);
 
for ($dan=1;$dan<=$dana;$dan++)
{
	
	$sql="SELECT DISTINCT  `kat` FROM  `scoresum`";
	$result = mysql_query($sql);
	while($row = mysql_fetch_assoc($result))
	{
			$kat=$row['kat'];
			$sql="SELECT `si`,`dan".$dan."`  FROM `scoresum` WHERE `kat` LIKE '".$kat."' AND `dan".$dan."` != 0 ORDER BY `dan".$dan."` ASC";
			$result1 = mysql_query($sql);
			
			$prvi=0;
			
			while ($row1 = mysql_fetch_assoc($result1))
			{
				$vreme=$row1['dan'.$dan];
				$vreme = explode(":", $vreme);
				$vreme=intval($vreme[0])*60+intval($vreme[1]);
				
				if ($prvi==0) 
				{
					$prvi=$vreme;
					$sql="UPDATE `scoresum` SET  `bod".$dan."` =  '100' WHERE  `si` =".$row1['si'].";";
					$result2 = mysql_query($sql);
				}
				else
				{
					$bodovi=$prvi*100/$vreme;
					$sql="UPDATE `scoresum` SET  `bod".$dan."` =  '".$bodovi."' WHERE  `si` =".$row1['si'].";";
					$result2 = mysql_query($sql);					
				}

			}
	
	}
	
}
$sql="SELECT `si`"; 
for ($i=1;$i<=$dana;$i++)
	$sql.= ", `bod".$i."`";
$sql.= " FROM `scoresum`;";
$result = mysql_query($sql);		

while($row = mysql_fetch_assoc($result))
	{
		$ukupno=0;
		
		$bodovi=array();
		for ($i=1;$i<=$dana;$i++)		
		{
			if (floatval($row["bod".$i])>0)
			$bodovi[$i]=floatval($row["bod".$i]);
			else
			$bodovi[$i]=0;
			
			//$ukupno+=floatval($row["bod".$i]);	
		}
		array_multisort($bodovi, SORT_DESC, SORT_NUMERIC);
		
		if ($best==0)
			for ($i=0;$i< count($bodovi);$i++)
				$ukupno+=$bodovi[$i];
		else
			for ($i=0;$i< $best;$i++)
					$ukupno+=$bodovi[$i];
		
		//echo $ukupno;
		if ($ukupno!=0)
		{
			$sql="UPDATE `scoresum` SET  `ukupno` =  '".$ukupno."' WHERE  `si` =".$row['si'].";";
			$result1 = mysql_query($sql);					
		}
		
	}

 
 echo '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250" /> 
</head>
<body>';
 
 
$sql="SELECT * FROM `scoresum` ORDER BY `kat` ASC, `ukupno` DESC";
$result = mysql_query($sql);		
echo "<table border=1 CELLSPACING=0>";
$posl="";
$rb=1;
while($row = mysql_fetch_assoc($result))
{
	if (floatval($row['ukupno'])==0) continue;
	
	if (strcmp($posl,$row['kat'])!=0)
	{
		echo "</table><br><br>
		<table border=1 CELLSPACING=0><tr valign=bottom><td colspan=20><font size='6'><b>".$row['kat']."</b></font></td></tr>";
		echo "<tr><td></td><td><i><b>Takmicar</b></td><td align=center><i><b>Klub</b></td>";
		for ($i=1;$i<=$dana;$i++)
			echo "<td colspan=2 align=center><i><B>".$i.". dan</b></td>";
		echo "<td><i><B>Ukupno</b></td></tr>";
	
		$posl=$row['kat'];
		$rb=1;
	}

		echo "<tr><td width=20>".$rb++.".</td><td width=140><b>".$row['ime']."</b></td><td width=80 align=center>".$row['klub']."</td>";
		for ($i=1;$i<=$dana;$i++)
		{
			echo "<td width=50 align=center>";
			$vreme=$row['dan'.$i];
			$vreme = explode(":", $vreme);
			$vreme=$vreme[0].":".$vreme[1];
			if (strcmp($vreme,"00:00")==0) $vreme="x";
			echo $vreme."</td><td width=50>".$row['bod'.$i]."</td>";
		}
		
		echo "<td width=50><b>".$row['ukupno']."</b></td></tr>";

} 
echo "</table>";
 
 ob_end_flush();
 
 unlink($_FILES['uploadedfile']['name']);
 
 header('Location: ./res/'.$fajl.'.html');
 
}
else
{
	echo '
	<form action="scoresum.php" method="post" enctype="multipart/form-data" name="formObj" > 
  <h2>Upload rezultata:</h2><br>
  <table border=0>
  <tr><td>CSV fajl sa razultatima</td><td><input type="file" name="uploadedfile" id="file" ></input></td><td> *zakacite MT ili OE CSV fajl koji nosi naziv kao vase takmicenje</td>
  <tr><td>Broj dana za bodovanje</td><td><input type="edit" name=dana value=1></input> </td><td></td>
  <tr><td>Koliko najboljih rezultata</td><td><input type="edit" name=best value=0></input> </td><td>*ako je broj 0 onda se racunaju svi rezultati</td>
  <tr><td></td><td><input type="submit" value="Posalji" ></input>  </td>
	</table>
</form><br><br><h3>Lista generisanih rezultata:</h3>';
$rezultati= getFiles("res");
//print_r($rezultati);
foreach($rezultati as $rez) {
	echo '<a href="res/'.$rez["file"].'">'.$rez["file"].'</a><br>
	';
}
	
}
 
 include "footer.php";
 
 function ob_file_callback($buffer)
{
  global $ob_file;
  fwrite($ob_file,$buffer);
}
 
 function getFiles($dir)
  {
    
    # array to hold return value
    $retval = array();

    # add trailing slash if missing
    if(substr($dir, -1) != "/") $dir .= "/";

    # full server path to directory
    $fulldir = "{$_SERVER['DOCUMENT_ROOT']}/$dir";

    $d = @dir($fulldir) or die("getImages: Failed opening directory $dir for reading");
    while(false !== ($entry = $d->read())) {
      # skip hidden files
      if($entry[0] == ".") continue;

      # check for image files
      
        $retval[] = array("file" => "$entry");
      
    }
    $d->close();

    return $retval;
  }
 
?>