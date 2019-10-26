<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
require_once( 'delphi.php' );

$sql=mysqlConnect();
//mysql_set_charset('CP1250',$sql);

mysql_query("SET CHARACTER SET 'cp1250'", $sql); 
mysql_query("SET NAMES 'cp1250'", $sql); 

//$charset = mysql_client_encoding($sql);
//echo "The current character set is: $charset\n";

$id=$_GET['id'];
$file=$_GET['file'];

header('Content-Disposition: attachment;filename="'.$file.'"');

echo "OEVENT 3.0.1   \0";

$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id`=".$id, $sql);
$takmicenje = mysql_fetch_assoc($result);

writeString($takmicenje['naziv']);
writeString($takmicenje['mesto']);
writeString($takmicenje['organizator']);

$datum=$takmicenje['datum'];
list($godina, $mesec, $dan) = split('[/.-]', $datum);

writeInt($godina);
writeInt($mesec);
writeInt($dan);
writeBool(1);
echo "\0";

$dana=intval($takmicenje['dana']);
$datum1 = date("Y-m-d",mktime(0,0,0,$mesec, $dan + ($dana-1), $godina));
list($godina, $mesec, $dan) = split('[/.-]', $datum1);

writeInt($godina);
writeInt($mesec);
writeInt($dan);
writeBool(1);
echo "\0";

list($godina, $mesec, $dan) = split('[/.-]', $takmicenje['datumprijave']);

writeInt($godina);
writeInt($mesec);
writeInt($dan);
writeBool(1);
echo "\0";

writeInt($dana);
writeBool(0);

for ($i=1;$i<=$dana;$i++)
{
	writeString($takmicenje['naziv']);
	writeString($takmicenje['mesto']);

	list($godina, $mesec, $dan) = split('[/.-]', $datum);
	$datum1 = date("Y-m-d",mktime(0,0,0,$mesec, $dan + ($i-1), $godina));
	list($godina, $mesec, $dan) = split('[/.-]', $datum1);
	writeInt($godina);
	writeInt($mesec);
	writeInt($dan);
	writeBool(1);
	echo "\0";

	writeInt(11); // first start
	writeInt(0);
	writeInt(0);
	writeBool(1);
}

echo "\x01";
writeString("DIN");
writeString("Additional Fee 1");
writeString("Additional Fee 2");
writeString("Additional Fee 3");
writeString("Additional Fee 4");
echo "\x01";
writeFloat();
writeFloat();
writeFloat();
writeFloat();
writeBool(1);
writeString("Additional Fee 1");
writeString("Additional Fee 2");
writeString("Additional Fee 3");
writeString("Additional Fee 4");
echo "\x01";
writeFloat();
writeFloat();
writeFloat();
writeFloat();
writeBool(1);
//writeString($takmicenje['notes']);
writeString("");
echo "\0";
echo "\x02";
writeBool(0);
writeBool(0);
writeBool(0);

writeInt(0); // broj staza

$result = mysql_query("SELECT COUNT(DISTINCT `prijave`.`kategorija`) AS cnt FROM `kategorije`,`prijave` WHERE `prijave`.`kategorija`=`kategorije`.`id` AND `prijave`.`takmicenje`=".$id." ORDER BY `kategorija` ASC", $sql);
$row = mysql_fetch_assoc($result);
$cnt=$row['cnt'];

writeInt($cnt); // broj kategorija

$result = mysql_query("SELECT DISTINCT `id`,`naziv` FROM `kategorije`,`prijave` WHERE `prijave`.`kategorija`=`kategorije`.`id` AND `prijave`.`takmicenje`=".$id." ORDER BY `kategorija` ASC", $sql);
for ($i=1;$i<=$cnt;$i++)
{
	$row = mysql_fetch_assoc($result);
	writeString($row['naziv']);
	writeString("");
	echo "\0"; //muski pol
	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeString("");
	
	for ($j=1;$j<=$dana;$j++)
	{
		writeBool(1);
		writeInt(0);
		writeInt(0);
		writeInt(0);
		writeBool(1);
		writeInt(0);
		writeInt(0);
		writeInt(0);
		writeBool(1);
	}
	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeBool(0);
	writeInt(0);
	writeInt(1000);
	writeInt(0);
	writeBool(0);

	echo "\0";
	writeFloat();  // before last entry date  0x5 pa 10 x 0
	writeFloat();  // after
	writeString("");
	
	for ($j=1;$j<=$dana;$j++)
		writeInt(0);

	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeInt(0);
	writeBool(0);

	writeInt(0);
	writeInt(1);
	writeInt(1);
	writeInt(0);
	echo "\0";

	writeInt(intval($row['id']));	
}

$result = mysql_query("SELECT COUNT(DISTINCT `drzave`.`id`) AS cnt FROM `prijave`,`takmicari`,`drzave`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicari`.`drzava`=`drzave`.`id` AND `prijave`.`takmicenje`=`takmicenja`.`id` AND `takmicenja`.`id`=".$id, $sql);
$row = mysql_fetch_assoc($result);
$cnt=$row['cnt'];

writeInt($cnt); // broj drzava

$result = mysql_query("SELECT DISTINCT `drzave`.* FROM `prijave`,`takmicari`,`drzave`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicari`.`drzava`=`drzave`.`id` AND `prijave`.`takmicenje`=`takmicenja`.`id` AND `takmicenja`.`id`=".$id, $sql);
for ($i=1;$i<=$cnt;$i++)
{
	
	$row = mysql_fetch_assoc($result);
	writeString($row['naziv']);
	writeString($row['skraceno']);
	writeInt(intval($row['id']));
}

$result = mysql_query("SELECT COUNT(DISTINCT `klubovi`.`id`) AS cnt FROM `prijave`,`klubovi`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`=`prijave`.`takmicenje` AND `takmicari`.`klub`=`klubovi`.`id` AND`takmicenja`.`id`=".$id, $sql);
$row = mysql_fetch_assoc($result);
$cnt=$row['cnt'];

writeInt($cnt); // broj klubova

$result = mysql_query("SELECT DISTINCT `klubovi`.* FROM `prijave`,`klubovi`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`=`prijave`.`takmicenje` AND `takmicari`.`klub`=`klubovi`.`id` AND `takmicenja`.`id`=".$id, $sql);
for ($i=1;$i<=$cnt;$i++)
{
	
	$row = mysql_fetch_assoc($result);
	writeString($row['naziv']);
	writeString($row['skraceno']);
	writeInt(0);
	writeString("");
	writeString("");
	writeString("");
	writeString("");
	writeString(""); //drzava
	writeString("");
	writeString("");
	writeString("");
	writeString("");
	writeString("");
	writeFloat();
	writeFloat();
	writeFloat();
	writeFloat();
	writeFloat();
	echo "\x01";
	writeInt(intval($row['drzava']));
	writeInt(intval($row['id']));
}

$result = mysql_query("SELECT COUNT(*) AS cnt FROM `prijave`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`= `prijave`.`takmicenje` AND `takmicenja`.`id`=".$id, $sql);
$row = mysql_fetch_assoc($result);
$cnt=$row['cnt'];

writeInt($cnt); // broj takmicara

$result = mysql_query("SELECT `takmicari`.`id`,`takmicari`.`ime`, `takmicari`.`prezime`,`takmicari`.`idSavez`,`takmicari`.`mail`,`takmicari`.`notes`,`prijave`.`si`,`takmicari`.`klub`,`takmicari`.`drzava`,`prijave`.`kategorija`, `prijave`.`dana` FROM `prijave`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`= `prijave`.`takmicenje` AND `takmicenja`.`id`=".$id, $sql);
for ($i=1;$i<=$cnt;$i++)
{
	$row = mysql_fetch_assoc($result);
	writeString($row['ime']);
	writeString($row['prezime']);
	writeString($row['idSavez']);
	writeString("");
	writeString("");
	writeString("");
	writeString(""); //drzava
	writeString("");
	writeString("");
	writeString($row['mail']);
	writeString($row['notes']);
	writeString("");
	writeString("");
	writeString("");
	writeString("");
	echo "\0\0\0\0\0\0\0\0\0";
	echo "\0\x18";
	echo "\0\0\0\0\0\0\x04";
	echo "\x1e\0\x03\0\xD9\x07"; // datum prijave fiksiran na 30.3.2009.
	echo "\x01\0\0\0";
	
	$brd=0;
	for ($j=0;$j<$dana;$j++)
		if ((intval($row['dana']) & pow(2,$j))>0)	
		$brd++;
		
	if ($brd==$dana)	
	echo writeDouble(intval($takmicenje['cenau'])); //cena
	else echo writeDouble(intval($takmicenje['cenaj'])*$brd); //cena
		
	echo "\0\0\0\0\0\0\0\0";
	echo "\0\0\0\0\0\0\0\0";
	echo "\0\0\0\0\0\0\0\0";
	echo "\0\0\0\0\0\0\0\0";
	
	for ($j=1;$j<=$dana;$j++)
	{
		echo "\x01\0";
		echo "\0\0\0\0\0\0\0\0\0\0\0\0\0\x60\x41\0"; //start time
		echo "\0\0\0\0\0\0\0\0\0\0\0\0\0\x60\x41\0"; //finish time
		echo "\0\0\0\0\0\0\0\0\0\0\0\0\0\x60\x41\0"; //competition time
	
		writeBool(0);
		writeBool(0);
		writeBool(0);
	}	

	echo "\0";
	writeInt(-1);
	
	for ($j=1;$j<=$dana;$j++)
		writeInt(0);

	if ($row['si']==NULL) 
		writeInt(-1);
	else	
		writeInt(intval($row['si']));

	writeInt(intval($row['drzava']));
	
	//if (intval($row['klub'])<500)
	writeInt(intval($row['klub']));
	//else 
	//writeInt(0);
	
	writeInt(intval($row['kategorija']));

	for ($j=1;$j<=$dana;$j++)
		writeInt(-1);

	for ($j=1;$j<=$dana;$j++)
		writeBool(1);

	for ($j=1;$j<=$dana;$j++)
	{
		writeInt(0);
		writeInt(0);
		writeBool(0);
	}

	for ($j=0;$j<$dana;$j++)
		if ((intval($row['dana']) & pow(2,$j))>0)	
			writeBool(1);
		else
			writeBool(0);
			
	writeString("");
	writeInt(intval($row['id']));
}

$result = mysql_query("SELECT `takmicari`.`id` FROM `prijave`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`= `prijave`.`takmicenje` AND `takmicenja`.`id`=".$id, $sql);
for ($j=1;$j<=$dana;$j++)
{
	$chb=0;
	while($row = mysql_fetch_assoc($result))
	{
		writeInt(intval($row['id']));
		$chb++;
	}
	if ($chb!=0)
	mysql_data_seek($result,0);
}	




for ($j=1;$j<=$dana;$j++)
	writeInt(0);

$result = mysql_query("SELECT DISTINCT `id`,`naziv` FROM `kategorije`,`prijave` WHERE `prijave`.`kategorija`=`kategorije`.`id` AND `prijave`.`takmicenje`=".$id." ORDER BY `kategorija` ASC", $sql);
while($row = mysql_fetch_assoc($result))
{
	writeInt(intval($row['id']));
	writeInt(1);
}

$result = mysql_query("SELECT DISTINCT `klubovi`.* FROM `prijave`,`klubovi`,`takmicari`,`takmicenja` WHERE `prijave`.`takmicar`=`takmicari`.`id` AND `takmicenja`.`id`=`prijave`.`takmicenje` AND `takmicari`.`klub`=`klubovi`.`id` AND`takmicenja`.`id`=".$id, $sql);
while($row = mysql_fetch_assoc($result))
{
	writeInt(intval($row['id']));
	writeInt(1);
}

for ($j=1;$j<=$dana;$j++)
	writeInt(0);

writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);
writeInt(0);

?>