<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

echo "<table border=0 cellpadding=0 cellspacing=0><tr align=center bgcolor=white>";
$sql=mysqlConnect();

$result = mysql_query("select * from dx");
$max = mysql_num_rows($result );

$t=array();
for ($i=0; $i<5; $i++)
 {

	while(1)
	{
	$rand = rand(1,$max-1);
	if (!in_array($rand,$t))
	{
	mysql_data_seek($result, $rand);
	$row = mysql_fetch_assoc($result);
	echo "<td width=80 height=150><a href=\"".$row['url']."\"><img src=\"".$row['slika']."\" width=\"80\" height=\"80\"></a><br><font size=\"-5\">".$row['naziv']."</font><br><font color=red><b>".$row['cena']."</b></font></td>";
	$t[$i]=$rand;
	break;
	}
	}

 }

echo "</tr></table>";



?>