<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

session();

include "hedder.php";

echo "<div align=center>";

$target_path = "upload/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if(isset($_POST["dan"])) $bodujDan=$_POST["dan"];
else $bodujDan=0;

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
	if (strpos($_FILES['uploadedfile']['name'],".oev"))
	oeventObrada($target_path);
	else if (strpos($_FILES['uploadedfile']['name'],".csv"))
	kramerObrada($target_path,$bodujDan);
	else echo "Ne podrzan fajl. Kontaktirajte paxy@paxy.in.rs";
} else{
    echo "There was an error uploading the file, please try again!";
}

echo "</div>";

include "footer.php";

function oeventObrada($path)
{
require_once( 'oeventRead.php' ); 
require_once( 'bodovanjeTakmicenja.php' ); 
require_once( 'bodovanjeUkupno.php' ); 

$sql=mysqlConnect();
echo "Obrada OEvent fajla: ". oeventRead($_POST['id'],$path)."<br>";
echo "Bodovanje takmicenja: ". bodovanjeTakmicenja($_POST['id'])."<br>";
echo "Prepravka naknadno ubacenih ". fixByName()."<br>";
echo "Bodovanje takmicenja: ". bodovanjeTakmicenja($_POST['id'])."<br>";
ubaciLigu($_POST['id']);
echo "Azuriranje ukupnih bodova: ". bodovanjeUkupno()."<br>";
echo "<br>Takmicenje je zatvoreno !";

$result = mysql_query("UPDATE  `takmicenja` SET  `status` =  '2' WHERE  `takmicenja`.`id` = ".$_POST['id'], $sql);

}

function kramerObrada($path,$bodujDan)
{
require_once( 'kramerRead.php' ); 
require_once( 'bodovanjeTakmicenja.php' ); 
//require_once( 'bodovanjeUkupno.php' ); 
//require_once( 'bodovanjeV2.php' ); 


echo "Obrada CSV fajla: ". kramerRead($_POST['id'],$path,$bodujDan)."<br>";
echo "Bodovanje takmicenja: ". bodovanjeTakmicenja($_POST['id'])."<br>";
echo "Prepravka naknadno ubacenih ". fixByName()."<br>";
echo "Bodovanje takmicenja: ". bodovanjeTakmicenja($_POST['id'])."<br>";
//ubaciLigu($_POST['id']);

//echo "Azuriranje ukupnih bodova: ". bodovanjeUkupno()."<br>";

	//echo "Azuriranje novog bodovnog sistema: ". bodovanjeV2($_POST['id'])."<br>";
echo "<br>Takmicenje je zatvoreno !";
 
  echo "Sacekajte na ovoj strani dok se ne zavrsi bodovanje plasmana i ne pojavi rec 'Gotovo !'".
  include('bodovanjeUkupno.php');
  
$sql=mysqlConnect();
$result = mysql_query("UPDATE  `takmicenja` SET  `status` =  '2' WHERE  `takmicenja`.`id` = ".$_POST['id'], $sql);

}



?>