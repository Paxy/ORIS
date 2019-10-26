<?PHP

//$takmicenje=$_GET;
//$filename="Rupcine.oev";

function oeventRead($takmicenje,$filename)
{

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
require_once( 'delphi.php' ); 

$sql=mysqlConnect();


$f = fopen($filename, "r");

readByte($f ,16);
readString($f);
readString($f);
readString($f);

readInt($f);
readInt($f);
readInt($f);
readBool($f);
readByte($f ,1);

readInt($f);
readInt($f);
readInt($f);
readBool($f);
readByte($f ,1);

readInt($f);
readInt($f);
readInt($f);
readBool($f);
readByte($f ,1);

$dana=readInt($f);
$dana;
readBool($f);

for ($i = 1; $i <= $dana; $i++) {
	readString($f);
	readString($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);
	readByte($f ,1);
	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);
}

readByte($f ,1);
readString($f);
readString($f);
readString($f);
readString($f);
readString($f);
readByte($f ,1);
readFloat($f);
readFloat($f);
readFloat($f);
readFloat($f);
readBool($f);

readString($f);
readString($f);
readString($f);
readString($f);
readByte($f ,1);
readFloat($f);
readFloat($f);
readFloat($f);
readFloat($f);
readBool($f);

readString($f);
readByte($f ,1);
readByte($f ,1);
readBool($f);
readBool($f);
readBool($f);

$staze=readInt($f);
$staze;

for ($i = 1; $i <= $staze; $i++) {
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);
	readInt($f);
	
	$varijnat=readInt($f);
	$varijnat;
	for ($j = 1; $j <= $varijnat; $j++) 
		readString($f);

	$brkontrola=readInt($f);
	$brkontrola;

	for ($j = 1; $j <= $brkontrola; $j++) {
		readString($f);
		readInt($f);
		readInt($f);
		readBool($f);
	}

	readInt($f);
	readInt($f);
}

$kategorije=readInt($f);
"Kategorija: ".$kategorije;

for ($i = 1; $i <= $kategorije; $i++) {
	readString($f);
	readString($f);
	readByte($f ,1);
	readInt($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readString($f);

	for ($j = 1; $j <= $dana; $j++) {
		readBool($f);
		readInt($f);
		readInt($f);
		readInt($f);
		readBool($f);
		readInt($f);
		readInt($f);
		readInt($f);
		readBool($f);
	}

	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);

	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);

	readByte($f ,1);
	readFloat($f);
	readFloat($f);
	readString($f);

	for ($j = 1; $j <= $dana; $j++) {
		readInt($f);
	}

	readInt($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readBool($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readInt($f);
	readByte($f ,1);

	readInt($f);

}

for ($i = 1; $i <= $staze; $i++) {
	$komb=readInt($f);
	$komb;
	for ($j = 1; $j <= $komb; $j++) {
		readInt($f);
	}
}

$drzave=readInt($f);
$drzave;
for ($i = 1; $i <= $drzave; $i++) {
	readString($f);
	readString($f);
	readInt($f);
}

$klubovi=readInt($f);
$klubovi;
for ($i = 1; $i <= $klubovi; $i++) {
	readString($f);
	readString($f);
	readInt($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readFloat($f);
	readFloat($f);
	readFloat($f);
	readFloat($f);
	readFloat($f);
	readByte($f ,1);
	readInt($f);
	readInt($f);
}

$takmicari=readInt($f);
$takmicari;
for ($i = 1; $i <= $takmicari ; $i++) {
	$ime=readString($f);
	$prezime=readString($f);

	$id=readString($f);

	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	readString($f);
	
	readByte($f ,9);
	readByte($f ,2);
	readByte($f ,7);
	readByte($f ,6);
	readByte($f ,24);
	readByte($f ,22);

	$minuti=array();
	$sekunde=array();
	$disq=array();

	for ($j = 1; $j <= $dana; $j++) {
		readByte($f ,1);
		$disq[$j]=hexdec(bin2hex(readByte($f ,1)));

		readLong($f); //dana	
		$stminuti=readLong($f);
		$stminuti;
		$stsekunde=readLong($f);
		$stsekunde;
		readLong($f); //datum
		
		readLong($f); //dana
		$fnminuti=readLong($f);
		$fnminuti;
		$fnsekunde=readLong($f);
		$fnsekunde;	
		readLong($f); //datum

		readLong($f); //dana
		$minuti[$j]=readLong($f);
		$sekunde[$j]=readLong($f);
		readLong($f); //datum		


		readBool($f);
		readBool($f);
		readBool($f);

		//if ($id>0)
		//{
		//print "ID Saveza: ".$id.", Ime i prezime: ".$ime." ".$prezime.", Dan: ".$j."., Minuti: ".$minuti.", Sekunde: ".$sekunde.", DISQ: ".$disq."<br>";
		//$sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`) VALUES ('".$takmicenje."', '".$id."', '".$j."', '".$minuti."', '".$sekunde."', '".$disq."', NULL )";
		//echo $sqlq;
		//$result = mysql_query($sqlq);
		//}
	}

	readByte($f ,1);

	$kontrole=readInt($f);
	$kontrole;
	
	for ($j = 1; $j <= $dana; $j++) {
		$k1=readInt($f);
		for ($k = 1; $k <= $k1; $k++) {		
			readString($f);
			readString($f);
		}
	}

	readInt($f);
	readInt($f);
	readInt($f);
	$kategorija=readInt($f);
	//$result = mysql_query("UPDATE `rezultati` SET `kategorija` = '".$kategorija."' WHERE `rezultati`.`reg` = ".$id." AND `rezultati`.`takmicenje` = ".$takmicenje);
	
	//$sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`) VALUES ('".$takmicenje."', '".$id."', '".$j."', '".$minuti."', '".$sekunde."', '".$disq."', NULL )";
		//echo $sqlq;
		//$result = mysql_query($sqlq);
	
	if (strlen($id)==0) 
	{
		$sqlq="SELECT `id`  FROM `takmicari` WHERE (`ime` LIKE '".$ime."' AND `prezime` LIKE '".$prezime."') OR (`ime` LIKE '".$prezime."' AND `prezime` LIKE '".$ime."')";
		$result = mysql_query($sqlq);
		if ($row = mysql_fetch_assoc($result))
		$id=$row['id'];
		else
		$id="NULL";
	}
	for ($j = 1; $j <= $dana; $j++) {
		$sqlq="INSERT INTO `rezultati` (`takmicenje`, `reg`, `dan`, `minut`, `sekund`, `disq`, `kategorija`, `prezime`, `ime`, `klub`) VALUES ('".$takmicenje."', '".$id."', '".$j."', '".$minuti[$j]."', '".$sekunde[$j]."', '".$disq[$j]."', '".$kategorija."', '".$prezime."', '".$ime."', '' )";
		//echo $sqlq;
		$result = mysql_query($sqlq);
	}
	
	for ($j = 1; $j <= $dana; $j++) {
		readInt($f);
	}

	for ($j = 1; $j <= $dana; $j++) {
		readBool($f);
	}

	for ($j = 1; $j <= $dana; $j++) {
		readInt($f);
		readInt($f);
		readBool($f);
	}


	for ($j = 1; $j <= $dana; $j++) {
		readBool($f);
	}

	readString($f);
	readInt($f);
}


fclose($f);
return "OK";
}
?>