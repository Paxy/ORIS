<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$max_start=120;
$default_interval=6;
$max_at_start=3;
$max_vacant_percent=30;


session();

include "hedder.php";

$sql=mysqlConnect();
$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id` LIKE ".$_GET['id'], $sql);
$row = mysql_fetch_assoc($result);
if ($_SESSION['klubId']==$row['klub'] || intval($_SESSION['klubId'])>1000)
{

if (isset($_GET['obrisiRezultate']))
{
	$result = mysql_query("DELETE FROM `lige` WHERE `takmicenje` = ".$_GET['id']." AND `liga` = 1 ORDER BY `takmicenje` ASC");
	$result = mysql_query("DELETE  FROM `rezultati` WHERE `takmicenje` = ".$_GET['id']);
	$result = mysql_query("UPDATE `takmicenja` SET `status` = '1' WHERE `takmicenja`.`id` = ".$_GET['id']);
	
	echo "Rezultati uklonjeni";
	
}
	
echo "<table border=1 cellspacing=0 align=center>";
echo "<tr><td><table>";
echo "<tr><td><a href=\"listaTakmicenja.php?id=".$row['id']."\">Pregled prijave</a></td></tr>";
echo "<tr><td><a href=\"orgRucnaPrijava.php?id=".$row['id']."\">Ru&#x010D;no prijavljivanje takmi&#x010D;ara</a></td></tr>";
echo "</table></td>";
echo "<td><table>";
echo "<tr><td><a href=\"otvoriPrijavu.php?id=".$row['id']."\">Otvori prijavu</a></td></tr>";
echo "<tr><td><a href=\"zatvoriPrijavu.php?id=".$row['id']."\">Zatvori prijavu</a></td></tr>";
//echo "<tr><td><a href=\"oeventGenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".oev\" onClick=\"(alert('Ukoliko generisete OEV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i OEV fajla !'))\">Generisi OEvent fajl</a>  <a href=\"http://a.paxy.in.rs:8080/ORIS/ServletGenerateOEV?id=".$row['id']."&file=".trim($row['naziv']).".oev\" onClick=\"(alert('Ukoliko generisete OEV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i OEV fajla !'))\">alt</a></td></tr>";
echo "<tr><td><a href=\"kramerGenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".csv&st\" onClick=\"(alert('Ukoliko generisete CSV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i CSV fajla !'))\">Generisi MT CSV fajl</a></td></tr>";
echo "<tr><td><a href=\"kramerGenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".csv&oe&st\" onClick=\"(alert('Ukoliko generisete CSV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i CSV fajla !'))\">Generisi OE CSV fajl</a></td></tr>";
echo "<tr><td><a href=\"kramerGenerator.php?id=".$row['id']."&inline&st\" target=\"_blank\" >Pregledni MT CSV</a></td></tr>";
echo "<tr><td><a href=\"kramerOOgenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".csv\" onClick=\"(alert('Ukoliko generisete CSV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i CSV fajla !'))\">Generisi MT CSV fajl sa OO integracijom</a></td></tr>";
echo "<tr><td><a href=\"kramerGenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".csv&oenew&st\" onClick=\"(alert('Ukoliko generisete CSV fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i CSV fajla !'))\">Generisi OE2010 CSV fajl</a></td></tr>";
echo "<tr><td><a href=\"kramerGenerator.php?id=".$row['id']."&file=".trim($row['naziv'])."-kategorije.csv&class\">Generisi OE2010 fajl sa kategorijama</a></td></tr>";
echo "<tr><td><a href=\"oeventNewGenerator.php?id=".$row['id']."&file=".trim($row['naziv']).".csv\" onClick=\"(alert('Ukoliko generisete fajl pre zatvaranja prijave, moze doci do razlike u prijavi izmedju baze prijavljenih i CSV fajla !'))\">Generisi OEvent fajl</a></td></tr>";
echo "</table></td></tr>";
echo "</table>";


echo "<p><p><table  border=1 cellspacing=0 align=center>";
echo '<tr><td align=center><b>Upload rezultata</b></td></tr><tr><td>
<form enctype="multipart/form-data" action="uploadFile.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<input type="hidden" name="id" value='.$row['id'].' />
Upload fajla sa rezultatima:<br> <input name="uploadedfile" type="file" /><br />
<br>Ako se uploaduju rezultati za pojedinacne dane, <br>izaberite dan za koji se rezultat salje.<br>';

echo "Dan <select name=dan size=1 id=dan><option>Svi</option>";
$dana=intval($row['dana']);
print_r($row);
for ($i=0;$i<$dana;$i++)
	echo "<option>".($i+1)."</option>
	";
	echo "</select><br><br>";
	
echo '<input type="submit" value="Upload" />

</form>
<a href=organizator.php?obrisiRezultate&id='.$row['id'].'>Obrisi rezultate</a>
</td></tr></table>';

echo "<p><p><table  border=1 cellspacing=0 align=center>";
echo '<tr><td align=center><b>Spajanje kategorija</b></td></tr><tr><td align=center>
Kategoriju <select name=skat size=1 id=skat></select> spoji sa <select name=ukat size=1 id=ukat></select><input type=button name=spoji id=spoji value=Spoji><br>
<select name=spojene size=5 id=spojene></select><input type=button name=obspajanje id=obspajanje value="Obrisi spajanje"><br>
</td></tr></table>';
echo '<script>
var tak='.$row['id'].';
var kategorije={';
$result1 = mysql_query("SELECT id,naziv FROM `kategorije` order by id;", $sql);
$row1 = mysql_fetch_assoc($result1);
echo $row1['id'].":'".$row1['naziv']."'";
while($row1 = mysql_fetch_assoc($result1))
	echo ",".$row1['id'].":'".$row1['naziv']."'";
	
echo'};

$(function() {
getSpojene();
updateKategorije();

 
});

function updateKategorije(){
	$.each(kategorije,function(id,val){
	  	 				$("#skat").append(\'<option value=\'+id+\'>\'+val+\'</option>\');
						$("#ukat").append(\'<option value=\'+id+\'>\'+val+\'</option>\');
 		});
}

function getSpojene(){
	param=[];
	var params={f:"spojene",tak: tak,params:param};
	 $.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				
 				$.each(obj,function(id,val){
	  	 				$("#spojene").append(\'<option value=\'+id+\'>\'+kategorije[val["kat"]]+\'->\'+kategorije[val["ukat"]]+\'</option>\');
 				});
 
				
			}
        });
}

$( "#spoji" ).click(function() {
		var skat=$(\'#skat option:selected\').val();
		var ukat=$(\'#ukat option:selected\').val();
  		
		 param={skat:skat,ukat:ukat};
		var params={f:"spoji",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#spojene\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#spojene").append(\'<option value=\'+id+\'>\'+kategorije[val["kat"]]+\'->\'+kategorije[val["ukat"]]+\'</option>\');
 				});
 
				
			}
        });
		
	});

$( "#obspajanje" ).click(function() {
		var spojene=$(\'#spojene option:selected\').val();
		
		param={id:spojene};
		var params={f:"obspojene",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#spojene\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#spojene").append(\'<option value=\'+id+\'>\'+kategorije[val["kat"]]+\'->\'+kategorije[val["ukat"]]+\'</option>\');
 				});
 
				
			}
        });
		
	});

</script>';	
	
echo "<br><form action=\"kramerGenerator.php\" method=send>
<table border=1 cellspacing=0 align=center><input type=hidden name=id value=".$row['id']."><input type=hidden name=file value=\"".trim($row['naziv']).".csv\"><input type=hidden name=st>";
echo "<tr><td align=center><b>Generisanje startne liste</b></td></tr>";
echo "<tr><td align=center>Dan <select name=dan size=1 id=dan>";
$result = mysql_query("SELECT dana FROM `takmicenja` WHERE `id` = ".$_GET['id'], $sql);
$row = mysql_fetch_assoc($result);	
$dana=intval($row['dana']);
for ($i=0;$i<$dana;$i++)
	echo "<option>".($i+1)."</option>
	";
	echo "</select><br></td></tr>";
echo "<tr><td>Maksimalno vreme za start <input type=text name=max_start id=max_start value=120 size=3>min <br>* za 150 takmicara po danu tj. linearno duze ili krace</td></tr>";
echo "<tr><td>Maksimalni (podrazumevani) interval <input type=text name=default_interval id=default_interval value=6 size=2	></td></tr>";
echo "<tr><td>Maksimalni broj takmicara u jednom st. boksu <input type=text name=max_at_start id=max_at_start value=3 size=2	></td></tr>";
echo "<tr><td>Procenat Vacant pozicija <input type=text name=max_vacant_percent id=max_vacant_percent value=30 size=3	>%</td></tr>";
//echo "<tr><td>Polu-dirigovano (ne registrovani ranije) <input type=checkbox name=dirigated 	></td></tr>";
echo "<tr><td align=center><input type=submit name=oenew value='Generisi OE 2010 CSV sa startnim vremenima'><br><input type=submit name=mt value='Generisi MT CSV sa startnim vremenima'><br><input type=submit name=oe value='Generisi OE CSV sa startnim vremenima'></td></tr>";
echo "<tr><td align=center>
Staze<br><select name=staza size=5 id=staza width=10px>";
echo "</select> -> <select name=st_kat size=5 id=st_kat></select> <input type=button name=obrstazu id=obrstazu value='Obrisi stazu'><br><input type=text name=dodajstazu id=dstazu size=1><input type=button id=dodajstazu value='Dodaj stazu'><br><select name=dkat size=1 id=dkat></select><input type=button id=dodajkat value='Dodaj kategoriju'><br> <input type=button name=kopiraj id=kopiraj value='Kopiraj raspored od prvog dana'><br><input type=submit name=oenewsts value='Generisi OE2010 CSV sa startnim vremenima prema stazama'></td></tr>";
echo "</table></form>";

echo '<script>

var def = {max_start: '.$max_start.', default_interval: '.$default_interval.', max_at_start: '.$max_at_start.', max_vacant_percent:'.$max_vacant_percent.'};


$(function() {
updateDanParam();
getStaze();
reloadDodajKategoriju();
});

function getStaze(){
	dan=$(\'#dan option:selected\').val();
	param={dan:dan};
	var params={f:"staze",tak: tak,params:param};
	 $.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#staza\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#staza").append(\'<option>\'+val+\'</option>\');
 				});
 
				
			}
        });
}

$(\'#dan\').on(\'change\', function() {
	getStaze();
	$(\'#st_kat\').empty();
	reloadDodajKategoriju();
	updateDanParam();
});

$(\'#staza\').on(\'change\', function() {
	var dan=$(\'#dan option:selected\').val();
	var staza=$(\'#staza option:selected\').val();
	param={dan:dan,staza:staza};
	var params={f:"st_kat",tak: tak,params:param};
	 $.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#st_kat\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#st_kat").append(\'<option value=\'+val+\'>\'+kategorije[val]+\'</option>\');
 				});
 
				
			}
        });
});

$( "#dodajstazu" ).click(function() {
		var staza=$(\'#dstazu\').val();
		var dan=$(\'#dan option:selected\').val();
		
		param={dan:dan, staza:staza};
		var params={f:"dodajstazu",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#staza\').empty();
				$(\'#dstazu\').val("");
 				$.each(obj,function(id,val){
	  	 				$("#staza").append(\'<option>\'+val+\'</option>\');
 				});
				
			}
        });
		
	});

function reloadDodajKategoriju(){
		var dan=$(\'#dan option:selected\').val();
		
		param={dan:dan};
		var params={f:"nedodeljenekat",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#dkat\').empty();
 				$.each(obj,function(id,val){
						$("#dkat").append(\'<option value=\'+val+\'>\'+kategorije[val]+\'</option>\');
 				});
 
				
			}
        });
		
}
$( "#dodajkat" ).click(function() {
		var kat=$(\'#dkat option:selected\').val();
		var staza=$(\'#staza option:selected\').val();
		var dan=$(\'#dan option:selected\').val();
		
		if (staza==null) {
			alert("Izaberite stazu za koju dodajete kategoriju !");
		}
		
		param={dan:dan, kat:kat,staza:staza};
		var params={f:"dodajkat",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#st_kat\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#st_kat").append(\'<option value=\'+val+\'>\'+kategorije[val]+\'</option>\');
 				});
			reloadDodajKategoriju();	
			}
        });
		
	});

$( "#obrstazu" ).click(function() {
		var staza=$(\'#staza option:selected\').val();
		var dan=$(\'#dan option:selected\').val();
		
		if (staza==null) {
			alert("Izaberite stazu koju zelite da obrisete !");
		}
		
		param={dan:dan,staza:staza};
		var params={f:"obrstazu",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#staza\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#staza").append(\'<option>\'+val+\'</option>\');
 				});
				$(\'#st_kat\').empty();
			reloadDodajKategoriju();	
			}
        });
		
	});

$( "#kopiraj" ).click(function() {
		var dan=$(\'#dan option:selected\').val();
		
		
		param={dan:dan};
		var params={f:"kopiraj",tak: tak,params:param};
	 	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$(\'#staza\').empty();
 				$.each(obj,function(id,val){
	  	 				$("#staza").append(\'<option>\'+val+\'</option>\');
 				});
				$(\'#st_kat\').empty();
			reloadDodajKategoriju();	
			}
        });
		
	});

function updateDanParam(){
		$.each(def,function(id,val){
	  	 	$("#"+id).val(val);
 		}); 

		var dan=$(\'#dan option:selected\').val();
				
		param={dan:dan};
		var params={f:"getparams",tak: tak,params:param};	
	$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
				 var obj = jQuery.parseJSON(data);
				$.each(obj,function(id,val){
	  	 				$("#"+id).val(val);
 				});
			}
        });
}

function inform(params)
{
		$.ajax({
            type: \'post\',
            url: \'callback.php\',
            data: params,
            success: function (data) {
			}
        });

}


$(\'#max_start\').focusout(function () { 
	var dan=$(\'#dan option:selected\').val();			
	var vrednost=$(\'#max_start\').val();	
	param={dan:dan,param:"max_start",vrednost:vrednost};
	var params={f:"updateparam",tak: tak,params:param};	
	inform(params);
});

$(\'#default_interval\').focusout(function () { 
	var dan=$(\'#dan option:selected\').val();			
	var vrednost=$(\'#default_interval\').val();	
	param={dan:dan,param:"default_interval",vrednost:vrednost};
	var params={f:"updateparam",tak: tak,params:param};	
	inform(params);
});

$(\'#max_at_start\').focusout(function () { 
	var dan=$(\'#dan option:selected\').val();			
	var vrednost=$(\'#max_at_start\').val();	
	param={dan:dan,param:"max_at_start",vrednost:vrednost};
	var params={f:"updateparam",tak: tak,params:param};	
	inform(params);
});

$(\'#max_vacant_percent\').focusout(function () { 
	var dan=$(\'#dan option:selected\').val();			
	var vrednost=$(\'#max_vacant_percent\').val();	
	param={dan:dan,param:"max_vacant_percent",vrednost:vrednost};
	var params={f:"updateparam",tak: tak,params:param};	
	inform(params);
});


</script>';
	

	
	
	
}
else echo "Nemate prava za modifikaciju podataka nad izabranim takmicenjem !";




include "footer.php";

?>
