<?php

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

if (!Isset($sql))
$sql=mysqlConnect();

$f=$_POST['f'];
$tak=$_POST['tak'];
$params=$_POST['params'];

if ($f=="spojene")
{
	$spojene=array();
	$result = mysql_query("SELECT * FROM `spajanje` WHERE `tak` =".$tak);
	while($row = mysql_fetch_assoc($result))
	{
				$spojene[$row['id']]=array("kat"=>$row['kat'],"ukat"=>$row['ukat']);
	}
	echo json_encode($spojene, JSON_NUMERIC_CHECK);
	
}elseif ($f=="spoji"){
	
	$result = mysql_query("INSERT INTO `spajanje` (`id`, `tak`, `kat`, `ukat`) VALUES (NULL, '".$tak."', '".$params['skat']."', '".$params['ukat']."');");
	$result = mysql_query("SELECT * FROM `spajanje` WHERE `tak` =".$tak);
	while($row = mysql_fetch_assoc($result))
	{
				$spojene[$row['id']]=array("kat"=>$row['kat'],"ukat"=>$row['ukat']);
	}
	echo json_encode($spojene, JSON_NUMERIC_CHECK);
}elseif ($f=="obspojene"){
	
	$result = mysql_query("DELETE FROM `spajanje` WHERE `spajanje`.`id` = ".$params['id']);
	$result = mysql_query("SELECT * FROM `spajanje` WHERE `tak` =".$tak);
	while($row = mysql_fetch_assoc($result))
	{
				$spojene[$row['id']]=array("kat"=>$row['kat'],"ukat"=>$row['ukat']);
	}
	echo json_encode($spojene, JSON_NUMERIC_CHECK);
}elseif ($f=="staze"){
	$staze=array();
	$result = mysql_query("SELECT distinct(staza) FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." order by staza");
	while($row = mysql_fetch_assoc($result))
	{
				$staze[]=$row['staza'];
	}
	echo json_encode($staze, JSON_NUMERIC_CHECK);
}elseif ($f=="st_kat"){
	$kat=array();
	$result = mysql_query("SELECT kategorija  FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." AND `staza` LIKE '".$params['staza']."' AND kategorija IS NOT NULL ORDER BY `of_staze`.`kategorija` ASC");
	while($row = mysql_fetch_assoc($result))
	{
				$kat[]=$row['kategorija'];
	}
	echo json_encode($kat, JSON_NUMERIC_CHECK);	
}elseif ($f=="dodajstazu"){
	if(strlen($params['staza'])<1) return;
	$result = mysql_query("INSERT INTO `of_staze` (`takmicenje`, `dan`, `staza`, `kategorija`) VALUES ('".$tak."', '".$params['dan']."', '".$params['staza']."', null);");
	$staze=array();
	$result = mysql_query("SELECT distinct(staza) FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." order by staza");
	while($row = mysql_fetch_assoc($result))
	{
				$staze[]=$row['staza'];
	}
	echo json_encode($staze, JSON_NUMERIC_CHECK);
}elseif ($f=="nedodeljenekat"){
	$result = mysql_query("SELECT id FROM `kategorije` where id not in (select distinct(of_staze.kategorija) from of_staze where of_staze.takmicenje=".$tak." and of_staze.dan=".$params['dan']." and of_staze.kategorija IS NOT NULL)");
	$kat=array();
	while($row = mysql_fetch_assoc($result))
	{
				$kat[]=$row['id'];
	}
	echo json_encode($kat, JSON_NUMERIC_CHECK);
}elseif ($f=="dodajkat"){
	if(strlen($params['kat'])<1) return;
	if(strlen($params['staza'])<1) return;
	$result = mysql_query("INSERT INTO `of_staze` (`takmicenje`, `dan`, `staza`, `kategorija`) VALUES ('".$tak."', '".$params['dan']."', '".$params['staza']."', '".$params['kat']."');");
	$kat=array();
	$result = mysql_query("SELECT kategorija  FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." AND `staza` LIKE '".$params['staza']."' AND kategorija IS NOT NULL ORDER BY `of_staze`.`kategorija` ASC");
	while($row = mysql_fetch_assoc($result))
	{
				$kat[]=$row['kategorija'];
	}
	echo json_encode($kat, JSON_NUMERIC_CHECK);	
}elseif ($f=="obrstazu"){
	$result = mysql_query("DELETE  FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." AND `staza` LIKE '".$params['staza']."'");
	$staze=array();
	$result = mysql_query("SELECT distinct(staza) FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = ".$params['dan']." order by staza");
	while($row = mysql_fetch_assoc($result))
	{
				$staze[]=$row['staza'];
	}
	echo json_encode($staze, JSON_NUMERIC_CHECK);
}elseif ($f=="getparams"){
	$params=getOFParams($tak,$params['dan']);
	echo json_encode($params, JSON_NUMERIC_CHECK);
}elseif ($f=="updateparam"){
	$result = mysql_query("INSERT INTO of_params (tak,dan,param,vrednost) VALUES (".$tak.",".$params['dan'].",\"".$params['param']."\",".$params['vrednost'].") ON DUPLICATE KEY UPDATE vrednost=".$params['vrednost']);
}elseif ($f=="kopiraj"){
	$result = mysql_query("delete  FROM `of_staze` WHERE `takmicenje` = 18 AND `dan` = 2 ");
	$result = mysql_query("SELECT * FROM `of_staze` WHERE `takmicenje` = ".$tak." AND `dan` = 1 AND `kategorija` IS NOT NULL");
	$st=array();
	while($row = mysql_fetch_assoc($result))
	{
			$staza=$row['staza'];
			if (!in_array($staza, $st))
				$st[]=$staza;
			
		$result1 = mysql_query("INSERT INTO `of_staze` (`takmicenje`, `dan`, `staza`, `kategorija`) VALUES ('".$tak."', '".$params['dan']."', '".$staza."', '".$row['kategorija']."');");	
		
	}
	echo json_encode($st, JSON_NUMERIC_CHECK);
}




?>