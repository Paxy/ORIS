<?php

error_reporting(E_ERROR | E_PARSE);

require_once( 'functions.php' ); 



session();



include "hedder.php";

if (IsSet($_GET['godina']))

$godina=$_GET['godina'];

else

$godina="";



$sql=mysqlConnect($godina);

$result = mysql_query("SELECT srr.datum,`srr`.`si`,ime,prezime,kategorije.naziv as kat,klubovi.naziv as klub FROM `srr`,`prijave`,`takmicari`,`kategorije`,`klubovi` where srr.tak=prijave.takmicenje and srr.si=prijave.si and takmicari.id=prijave.takmicar and `klubovi`.`id`=takmicari.klub and kategorije.id=prijave.kategorija ORDER by datum DESC", $sql);

echo "<table border=0 cellspacing=0 align=center width=830><tr><td><b>Datum</td><td><b>SI</td><td><b>Ime</td><td><b>Prezime</td><td><b>Kategorija</td><td><b>Klub</td></tr>";
while ($row = mysql_fetch_assoc($result)) {
    echo "<tr><td>".$row["datum"]."</td><td>".$row["si"]."</td><td>".$row["ime"]."</td><td>".$row["prezime"]."</td><td>".$row["kat"]."</td><td>".$row["klub"]."</td></tr>";
}
echo "</table>";

include "footer.php";

?>
