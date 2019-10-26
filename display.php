<?PHP
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

include "hedder.php";

	echo '
	<script>
	function go()
	{
	var f = forma.opcija.options[forma.opcija.options.selectedIndex].value;
	if (f=="rezultat")
	{
	forma.action = "display/rezultati.php";
	forma.submit();
	}
	else
	{
	forma.action = "display/stlista.php";
	forma.submit();
	}
	}
	
	function alternative()
	{
	forma.action = "display/rezultati1.php";
	forma.submit();
	}
	
	</script>
	
	<form action="display.php" method="post" name="forma"> 
  <h2>Prikaz rezultata na ekran:</h2>
  <table border=0>
  <tr><td>Dan za prikaz</td><td><input type="edit" name=dan value=1></input> </td><td></td>
  <tr><td>Duzina ekrana</td><td><input type="edit" name=height value=490></input> </td><td></td>
  <tr><td>Tip prikaza</td><td><select name=opcija><option value="rezultat">Prkaz rezultata</option><option value="stlista">Prkaz startne liste</option></select> </td><td></td>
  <tr><td></td><td><input type="button" value="Prikazi" onClick="go()"></input> 
  <input type="button" value="Alt" onClick="alternative()"></input>   </td>
	</table>
</form>CSV fajl u MT ili OE formatu uploadovati na lokaciju /public_html/oris/display/data.csv<br><br>';

	

 
 include "footer.php";
 
 
 
?>