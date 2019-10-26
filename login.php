<?PHP

require_once( 'functions.php' ); 
session();


   if (IsSet($_GET['logout'])) 
	{
	logout();
	echo header("Location: index.php");
	}


   if (!IsSet($_SESSION['klubId'])) 
	 if (!IsSet($_POST['klubId']))
	{


	echo "<form method=\"POST\" action=\"https://oris.orijentiring.rs/login.php\">";
	if (IsSet($_GET['err']))
	{ 
			echo "<b><font color=red>Neispravna &#x0161;ifra</font></b>";
	}
	
	$cookie=-1;
	if (isset($_COOKIE["cookieKlub"]))
		$cookie=$_COOKIE["cookieKlub"];
	echo "<br><select name=\"klubId\">";
	$sql=mysqlConnect();

	$result = mysql_query("SELECT `id`,`naziv` FROM `klubovi` ORDER BY `id` ASC", $sql);

	while ($row = mysql_fetch_assoc($result)) {
	if (intval($row['id'])>500 && intval($row['id'])<1000) continue;
	echo "<option value=\"".$row['id']."\"";
	if (intval($row['id'])==$cookie) echo " selected ";
	echo ">".$row['naziv']."</option>";
	}

	echo "</select><br><input type=\"password\" name=\"pass\" size=14><br><input type=\"submit\" value=\"Loguj se\"></form>
	<a href=regIndividualac.php>Registracija Individualaca</a><br>
	<a href=regKlub.php>Registracija Kluba</a>";
	}       
  	else {
	$sql=mysqlConnect();

	if ($_POST['klubId']==500)
	{
	$result = mysql_query("SELECT `id` FROM `klubovi` WHERE `id`>=500 AND `pass`='".md5($_POST['pass'])."'", $sql);
	if ($row = mysql_fetch_assoc($result)) $_SESSION['klubId'] = $row['id'];
	echo header("Location: index.php");
	}
	else
	{
	$result = mysql_query("SELECT `pass` FROM `klubovi` WHERE `id`=".$_POST['klubId'], $sql);
	$row = mysql_fetch_assoc($result);
	$dbpass=$row['pass'];

	if ($dbpass=="")
	{
	$_SESSION['klubId1'] = $_POST['klubId'];
		setcookie("cookieKlub",$_POST['klubId'],time() + (10 * 365 * 24 * 60 * 60));
       echo header("Location: setpass.php");
	return;
	}

	if (md5($_POST['pass'])==$dbpass)
	{
       $_SESSION['klubId'] = $_POST['klubId'];
	   setcookie("cookieKlub",$_POST['klubId'],time() + (10 * 365 * 24 * 60 * 60));
       echo header("Location: takmicenja.php");
	}
	else
	{
	echo header("Location: takmicenja.php?err=1");
	}
	}
	
  	}else {
	$sql=mysqlConnect();
	$result = mysql_query("SELECT `naziv`,`kontakt` FROM `klubovi` WHERE `id`=".$_SESSION['klubId'], $sql);
	$row = mysql_fetch_assoc($result);
	echo "<b>".$row['naziv']."</b><br>(".$row['kontakt'].")<br>";
	echo "<br><a href='login.php?logout'>Log Out</a>";
	}
	
	
  
?>