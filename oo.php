<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 
require_once( 'HttpClient1.0.php' ); 

function generateOOData($user,$pass,$id)
{
	
$client = new HttpClient('www.orienteeringonline.net',80);
//$client->setDebug(true); 
$client->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.3a) Gecko/20021207');

if (!$client->get('/API/GetCompetitionData.aspx?Email='.$user.'&password='.$pass.'&CompetitionID='.$id)) {
    die('An error occurred: '.$client->getError());
}


$pageContents =$client->getContent();

$kategorije=getkategorijenew(true);
//print_r($kategorije);


//$pageContents=file_get_contents("t.xml");
$oe= new SimpleXMLElement($pageContents);

//print_R($oe);
//echo "Stno;SI card1;SI card2;SI card3;SI card4;SI card5;SI card6;Database Id;Surname;First name;YB;S;Block1;Block2;Block3;Block4;Block5;Block6;E1;E2;E3;E4;E5;E6;nc1;Start1;Finish1;Time1;Classifier1;nc2;Start2;Finish2;Time2;Classifier2;nc3;Start3;Finish3;Time3;Classifier3;nc4;Start4;Finish4;Time4;Classifier4;nc5;Start5;Finish5;Time5;Classifier5;nc6;Start6;Finish6;Time6;Classifier6;Club no.;Cl.name;City;Nat;Cl. no.;Short;Long;Num1;Num2;Num3;Text1;Text2;Text3;Name;Street;Line2;Zip;City;Phone;Fax;EMail;Id/Club;Rented;Start fee;Paid
//";

//print_r($oe);
// print_R (nadji($oe->countries->country,33));


header('Content-Disposition: attachment;filename="out.csv"');

foreach ($oe->entries->entry as $entry) {
    echo ";".$entry->chipNumber.";".$entry->chipNumber.";".$entry->chipNumber.";".$entry->chipNumber.";".$entry->chipNumber.";".$entry->chipNumber.";";
    echo $entry->entryId.";".fix($entry->lastName).";".fix($entry->firstName).";;;;;;;;;";
    if ($entry->day1==1) echo "X;";
		else echo "0;";
		if ($entry->day2==1) echo "X;";
		else echo "0;";
		if ($entry->day3==1) echo "X;";
		else echo "0;";
		if ($entry->day4==1) echo "X;";
		else echo "0;";
		if ($entry->day5==1) echo "X;";
		else echo "0;";
		if ($entry->day6==1) echo "X;";
		else echo "0;";
		echo ";;;;0;;;;;0;;;;;0;;;;;0;;;;;0;;;;;0;";
		$klub=nadji($oe->clubs->club,$entry->clubId);
		if (IsSet($klub))	echo $klub->id.";".fix($klub->longName).";".fix($klub->shortName).";";
		else echo ";;";
		$country=nadji($oe->countries->country,$entry->countryId);
		if (IsSet($country))	echo fix($country->countryShortName).";";
		else echo ";";
		$kat=nadji($oe->classes->class,$entry->classId);
		if (IsSet($kat)){
				
				$key = array_search($kat->name, $kategorije); 
				if ($key===false)
					$katid=$kat->id; 
				else
					$katid=$key;
				echo $katid.";".fix($kat->name).";".fix($kat->name).";";
			}
		else echo ";;;";
		echo ";;;;;;;;;;;;;;;0;0;X;";
	echo "\n";	
}
}

function nadji($oe,$id)
{
	foreach ($oe as $slog) {
		if (intval($slog->id)==intval($id))
		return $slog;
}
}

function fix($text){
//return iconv("UTF-8", "CP1250//TRANSLIT", $text);
return $text;
}

?>