<?PHP

/*
function bodovanjeUkupno($ignorePoziciju=0)
{

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$sql=mysqlConnect();
	
/*
$result = mysql_query("SELECT DISTINCT  `liga` FROM `lige` order by `liga`", $sql);
	$row = mysql_fetch_assoc($result); // preskoci ligu 1
while($row = mysql_fetch_assoc($result))
{
	$liga=$row['liga'];

	$result1 = mysql_query("SELECT COUNT(*) FROM `lige`,`takmicenja` WHERE `takmicenja`.`id`=`lige`.`takmicenje` AND `takmicenja`.`status`=2 AND `liga`=".$liga,$sql);
	$row1 = mysql_fetch_assoc($result1);
	$kola=$row1['COUNT(*)'];
	$bodovati=floor($kola/2)+1;
	
	//$limit=" LIMIT 100 OFFSET 400"; // ako mora deo po deo
	//$limit=" ";
	
	$result1 = mysql_query("SELECT DISTINCT `reg` FROM `rezultati`,`lige` WHERE `rezultati`.`takmicenje`=`lige`.`takmicenje` AND `rezultati`.`dan`=`lige`.`dan` AND `rezultati`.`reg` IS NOT NULL AND `lige`.`liga`=".$liga." ".$limit,$sql);
	while($row1 = mysql_fetch_assoc($result1))
	{		
		$takmicar=$row1['reg'];
		$sqlq="SELECT * FROM `rezultati`,`lige` WHERE `rezultati`.`takmicenje`=`lige`.`takmicenje` AND `rezultati`.`dan`=`lige`.`dan` AND `lige`.`liga`=".$liga." AND `rezultati`.`reg`= ".$takmicar." ORDER BY `bodovi".$liga."` DESC";
		//echo $sqlq."<br>";
		$result2 = mysql_query($sqlq,$sql);
		
		$ukupno=0;
		$brRez=0;
		while($row2 = mysql_fetch_assoc($result2))
				if ($brRez++ < $bodovati)
					{
						$ukupno+=$row2["bodovi".$liga];
						$result3 = mysql_query("UPDATE `rezultati` SET `ok".$liga."`=5 WHERE `rezultati`.`takmicenje` = ".$row2['takmicenje']." AND `rezultati`.`reg` = ".$row2['reg']." AND `rezultati`.`dan` = ".$row2['dan'].";",$sql);
					}
					
				else
				{
					$result3 = mysql_query("UPDATE `rezultati` SET `ok".$liga."`=6 WHERE `rezultati`.`takmicenje` = ".$row2['takmicenje']." AND `rezultati`.`reg` = ".$row2['reg']." AND `rezultati`.`dan` = ".$row2['dan'].";",$sql);
				}
		
		//$ukupno=$row2['SUM(`bodovi'.$liga.'`)'];
		$result2 = mysql_query("UPDATE `bodovanje` SET `liga".$liga."b` = '".$ukupno."' WHERE `bodovanje`.`takmicar` = ".$takmicar." ;",$sql);
	}
}



  //Dovde pa odavde
  
  
  
-
$result = mysql_query("TRUNCATE TABLE `bodovanjeKluba`",$sql);
$result = mysql_query("TRUNCATE TABLE `bodovanjeKlubaUkupno`",$sql);

$result = mysql_query("SELECT DISTINCT  `liga` FROM `lige` order by `liga` ", $sql);
	$row = mysql_fetch_assoc($result); // preskoci ligu 1
while($row = mysql_fetch_assoc($result))
{
$liga=$row['liga'];
$result1 = mysql_query("SELECT `id` FROM `klubovi` WHERE `id`<100",$sql);
	while ($row1 = mysql_fetch_assoc($result1))
	{
		$klub=$row1['id'];
		$result2 = mysql_query("SELECT * FROM `kategorije` WHERE `koeficient`>0",$sql);
		while ($row2 = mysql_fetch_assoc($result2))
				{
					$kategorija=$row2['id'];
					$koeficient=$row2['koeficient'];
					
					$result3 = mysql_query("SELECT sum(`liga".$liga."b`) as suma FROM (SELECT `liga".$liga."b` FROM `bodovanje`,`takmicari` WHERE `bodovanje`.`takmicar`=`takmicari`.`idSavez` AND `takmicari`.`klub`=".$klub." AND `takmicari`.`kategorija`=".$kategorija." ORDER BY `liga".$liga."b` DESC LIMIT 3) as data",$sql);
					$row3 = mysql_fetch_assoc($result3);
					$suma=floatval($row3['suma'])*floatval($koeficient);
					
					$result3 = mysql_query("INSERT INTO `bodovanjeKluba` (`klub`, `kategorija`, `bodovi`, `liga`) VALUES ('".$klub."', '".$kategorija."', '".$suma."', '".$liga."');",$sql);
					
				}
	
	}
	$result1 = mysql_query("insert into bodovanjeKlubaUkupno(`klub`,`ukupno`,`liga`) SELECT `klub`, sum(`bodovi`) as ukupno,`liga` FROM `bodovanjeKluba` WHERE `liga`=".$liga." GROUP BY `klub`",$sql);
}

return "OK";
}
*/

//echo bodovanjeUkupno();

//include "hedder.php";

echo '
<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
$(document).ready(function(){
  //$("a.start").click(function() { 
    func1();
    $("div.status").append("Bodovanje 0-100 takmicara: ");
    $("a.start").hide();
  //  return false; // prevent default
  //});
});
function func1() {
  $.ajax({
  url: "bodujDeoTakmicara.php?limit=0",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func2();
  $("div.status").append("Bodovanje 100-200 takmicara: ");
});
}
function func2() {
  $.ajax({
  url: "bodujDeoTakmicara.php?limit=1",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func3();
  $("div.status").append("Bodovanje 200-300 takmicara: ");
});
}
function func3() {
  $.ajax({
  url: "bodujDeoTakmicara.php?limit=2",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func4();
  $("div.status").append("Bodovanje 300-400 takmicara: ");
});
}
function func4() {
  $.ajax({
  url: "bodujDeoTakmicara.php?limit=3",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func5();
  $("div.status").append("Bodovanje 400-500 takmicara: ");
});
}
function func5() {
  $.ajax({
  url: "bodujDeoTakmicara.php?limit=4",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func6();
  $("div.status").append("Bodovanje pozicija: ");
});
}
function func6() {
  $.ajax({
  url: "bodujPozTakmicara.php",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
  func7();
  $("div.status").append("Bodovanje klubova: ");
});
}
function func7() {
  $.ajax({
  url: "bodujKlubove.php",
  context: document.body
}).done(function(data) {
  $("div.status").append(data);
  $("div.status").append("<br>");
    $("div.status").append("Gotovo !!!");
});
}
</script>
<a href="#" id="clickingEvent" class="start" rel="example">Klikni za AJAX obracun ukupnih rezultata, i sacekaj da se pojavi rec "Gotovo".</a>
<div class=status>Status:<br></div>
';


?>