<?PHP

error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

$takid=$_GET['id'];


$sql=mysqlConnect();

include ('spajanjeKategorija.php');

if (!IsSet($_GET['inline']))
{
$file=$_GET['file'];
header('Content-Disposition: attachment;filename="'.$file.'"');
}

$eng=0;
if (IsSet($_GET['eng']))
	$eng=1;

$st=0;
if (IsSet($_GET['st']) && !isset($_GET['oenewsts']))
{
	$st=1;
	include ('oris_fairtime.php');
}
if (IsSet($_GET['sts']) || isset($_GET['oenewsts']))
{
	$st=1;
	include ('oris_fairtime_staze.php');
}

$utf8=1;
if (!IsSet($_GET['utf8']))
{
$utf8=0;
mysql_query("SET CHARACTER SET 'cp1250'", $sql); 
mysql_query("SET NAMES 'cp1250'", $sql); 
}

$result = mysql_query("SELECT * FROM `takmicenja` WHERE `id`=".$takid, $sql);
$takmicenje = mysql_fetch_assoc($result);
$dani=intval($takmicenje['dana']);

if (Isset($_GET['class'])) $class=1;
else $class=0;

if (Isset($_GET['oenew']) || isset($_GET['oenewsts'])) $oenew=1;
else $oenew=0;
if (Isset($_GET['oe'])) $oe=1;
else $oe=0;

if($class)
	echo "OE0008a;Cl. no.;Short;Long;Start fee;Classified;S;Age from;Age to;Start fee2;Type 1;Type 2;Class additional text;Start fee/Stage;S1 Max. competitors;S2 Max. competitors;S3 Max. competitors;S4 Max. competitors;S5 Max. competitors;S6 Max. competitors
";
else if ($oenew)
	if ($dani>1)
		echo "OE0002;Stno;XStno;Chipno1;Chipno2;Chipno3;Chipno4;Chipno5;Chipno6;Database Id;Surname;First name;YB;S;Block1;Block2;Block3;Block4;Block5;Block6;E1;E2;E3;E4;E5;E6;nc1;Start1;Finish1;Time1;Classifier1;Credit -1;Penalty +1;Comment1;nc2;Start2;Finish2;Time2;Classifier2;Credit -2;Penalty +2;Comment2;nc3;Start3;Finish3;Time3;Classifier3;Credit -3;Penalty +3;Comment3;nc4;Start4;Finish4;Time4;Classifier4;Credit -4;Penalty +4;Comment4;nc5;Start5;Finish5;Time5;Classifier5;Credit -5;Penalty +5;Comment5;nc6;Start6;Finish6;Time6;Classifier6;Credit -6;Penalty +6;Comment6;Club no.;Cl.name;City;Nat;Location;Region;Cl. no.;Short;Long;Entry cl. No;Entry class (short);Entry class (long);Rank;Ranking points;Num1;Num2;Num3;Text1;Text2;Text3;Addr. surname;Addr. first name;Street;Line2;Zip;Addr. city;Phone;Mobile;Fax;EMail;Rented;Start fee;Paid;Team;
";
	else
		echo "OE0001;Stno;XStno;Chipno;Database Id;Surname;First name;YB;S;Block;nc;Start;Finish;Time;Classifier;Credit -;Penalty +;Comment;Club no.;Cl.name;City;Nat;Location;Region;Cl. no.;Short;Long;Entry cl. No;Entry class (short);Entry class (long);Rank;Ranking points;Num1;Num2;Num3;Text1;Text2;Text3;Addr. surname;Addr. first name;Street;Line2;Zip;Addr. city;Phone;Mobile;Fax;EMail;Rented;Start fee;Paid;Team;Course no.;Course;km;m;Course controls;
";
else if ($oe)
echo "Stno;Chip;Database Id;Surname;First name;YB;S;Block;nc;Start;Finish;Time;Classifier;Club no.;Cl.name;City;Nat;Cl. no.;Short;Long;Num1;Num2;Num3;Text1;Text2;Text3;Name;Street;Line2;Zip;City;Phone;Fax;EMail;Id/Club;Rented;Start fee;Paid
";
else
echo "Stno;SI card1;SI card2;SI card3;SI card4;SI card5;SI card6;Database Id;Surname;First name;YB;S;Block1;Block2;Block3;Block4;Block5;Block6;E1;E2;E3;E4;E5;E6;nc1;Start1;Finish1;Time1;Classifier1;nc2;Start2;Finish2;Time2;Classifier2;nc3;Start3;Finish3;Time3;Classifier3;nc4;Start4;Finish4;Time4;Classifier4;nc5;Start5;Finish5;Time5;Classifier5;nc6;Start6;Finish6;Time6;Classifier6;Club no.;Cl.name;City;Nat;Cl. no.;Short;Long;Num1;Num2;Num3;Text1;Text2;Text3;Name;Street;Line2;Zip;City;Phone;Fax;EMail;Id/Club;Rented;Start fee;Paid
";


if ($class)
{
$result = mysql_query("SELECT DISTINCT(temp_prijave.kategorija) as id,kategorije.naziv as kat, takmicenja.cenaj FROM `temp_prijave`,`takmicenja`,`takmicari`,`kategorije`,`klubovi` WHERE `temp_prijave`.`takmicenje`=`takmicenja`.`id` AND `temp_prijave`.`takmicar`=`takmicari`.`id` AND `temp_prijave`.`kategorija`=`kategorije`.`id` AND `takmicari`.`klub`=`klubovi`.`id` AND `temp_prijave`.`takmicenje`=".$takid." ORDER BY id ASC",$sql);
while ($row = mysql_fetch_assoc($result)) 
{
	echo ';'.$row['id'].';'.$row['kat'].';'.$row['kat'].';'.$row['cenaj'].';X;M;;;;0;0;;'.$row['cenaj'].';;;;;;
';		
}
	
return;	
}	
$result = mysql_query("SELECT `temp_prijave`.`si`, `takmicari`.`idSavez`, `ime`, `prezime`, `klubovi`.`naziv` AS klub,`kategorije`.`naziv` AS kat,`klubovi`.`id` AS idklub, `skraceno`, `kategorije`.`id` AS idkat, `temp_prijave`.`dana` AS `dani`,`takmicari`.`id` FROM `temp_prijave`,`takmicenja`,`takmicari`,`kategorije`,`klubovi` WHERE `temp_prijave`.`takmicenje`=`takmicenja`.`id` AND `temp_prijave`.`takmicar`=`takmicari`.`id` AND `temp_prijave`.`kategorija`=`kategorije`.`id` AND `takmicari`.`klub`=`klubovi`.`id` AND `temp_prijave`.`takmicenje`=".$takid." ORDER BY klubovi.id ASC",$sql);
while ($row = mysql_fetch_assoc($result)) 
{

	if ($row['klub']==="Individualac")
		$klub=999;
			else
		$klub=$row['idklub'];
	
	$kat=$row['kat'];
	if ($eng) 
		if ($utf8)
			$kat=str_replace("Å½", "W", $kat);
		else
			$kat=str_replace("Ž", "W", $kat);
			
	if ($oenew)
	if ($dani>1)
	{	
		
		echo ';;;'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';"'.$row['idSavez'].'";"'.$row['prezime'].'";"'.$row['ime'].'";;;;;;;;;';
	
		$dana=$row['dani'];
		if (intval($dana&1)>0) echo "X;";
		else echo "0;";
		if (intval($dana&2)>0) echo "X;";
		else echo "0;";
		if (intval($dana&4)>0) echo "X;";
		else echo "0;";
		if (intval($dana&8)>0) echo "X;";
		else echo "0;";
		if (intval($dana&16)>0) echo "X;";
		else echo "0;";
		if (intval($dana&32)>0) echo "X;";
		else echo "0;";
		
		$stdani=array();
		if ($st)
		{
		$resultst = mysql_query("SELECT *  FROM `oris_fairtime` WHERE `tak` = ".$takid." AND `id` = ".$row['id']." ORDER BY dan ASC",$sql);
		while($rowst = mysql_fetch_assoc($resultst)){
			$stdani[$rowst['dan']]=$rowst['st'];
		}			
		}
		
		
		echo '0;';
		if ($st && isset($stdani[1])) echo $stdani[1].":00";
		echo ';;;0;;;;0;';
		if ($st && isset($stdani[2])) echo $stdani[2].":00";
		echo ';;;0;;;;0;';
		if ($st && isset($stdani[3])) echo $stdani[3].":00";
		echo ';;;0;;;;0;';
		if ($st && isset($stdani[4])) echo $stdani[4].":00";
		echo ';;;0;;;;0;';
		if ($st && isset($stdani[5])) echo $stdani[5].":00";
		echo ';;;0;;;;0;';
		if ($st && isset($stdani[6])) echo $stdani[6].":00";
		
		echo ';;;0;;;;"'.$klub.'";"'.$row['klub'].'";"'.$row['skraceno'].'";;;;'.$row['idkat'].';'.$kat.';"'.$kat.'";;;;;;;;;;;;;;;;;;;;;;;0;';
		
		$brd=0;
			for ($j=1;$j<=$dani;$j++)
			if ((intval($row['dani']) & $j)>0)	
			$brd++;
		
			if ($brd==$dani)	
			echo intval($takmicenje['cenau']); //cena
			else echo intval($takmicenje['cenaj'])*$brd; //cena
		
		echo ';X;
';	
	}
	else
	{
		echo ';;;'.$row['si'].';"'.$row['idSavez'].'";"'.$row['prezime'].'";"'.$row['ime'].'";;;;';
	
		$stdani=array();
		if ($st)
		{
		$resultst = mysql_query("SELECT *  FROM `oris_fairtime` WHERE `tak` = ".$takid." AND `id` = ".$row['id']." ORDER BY dan ASC",$sql);
		while($rowst = mysql_fetch_assoc($resultst)){
			$stdani[$rowst['dan']]=$rowst['st'];
		}			
		}
		
		
		echo '0;';
		if ($st && isset($stdani[1])) echo $stdani[1].":00";
		echo ';;;0';
		
		echo ';;;;"'.$klub.'";"'.$row['klub'].'";"'.$row['skraceno'].'";;;;'.$row['idkat'].';'.$kat.';"'.$kat.'";;;;;;;;;;;;;;;;;;;;;;;0;';
		
		$brd=0;
			for ($j=1;$j<=$dani;$j++)
			if ((intval($row['dani']) & $j)>0)	
			$brd++;
		
			if ($brd==$dani)	
			echo intval($takmicenje['cenau']); //cena
			else echo intval($takmicenje['cenaj'])*$brd; //cena
		
		echo ';X;
';
	}
	else if ($oe)
	{
		
			if ($st)
			{
			$resultst = mysql_query("SELECT *  FROM `oris_fairtime` WHERE `tak` = ".$takid." AND `id` = ".$row['id']." ORDER BY dan ASC",$sql);
			$rowst = mysql_fetch_assoc($resultst);
			$stt=$rowst['st'];			
			}
	
		
			echo ';'.$row['si'].';'.$row['idSavez'].';'.$row['prezime'].';'.$row['ime'].';;;;;';
			if ($st) echo $stt.":00";
			echo ';;;0;'.$klub.';'.$row['klub'].';'.$row['skraceno'].';;'.$row['idkat'].';'.$kat.';'.$kat.";;;;;;;;;;;;;;;;0;";
			
			
			
	
			$brd=0;
			for ($j=0;$j<$dani;$j++)
			if ((intval($row['dani']) & pow(2,$j))>0)	
			$brd++;
		
			if ($brd==$dani)	
			echo intval($takmicenje['cenau']); //cena
			else echo intval($takmicenje['cenaj'])*$brd; //cena
			
			echo ";X	
";
	
	}
	else
	{
		
		echo ';'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';'.$row['si'].';"'.$row['idSavez'].'";"'.$row['prezime'].'";"'.$row['ime'].'";;;;;;;;;';
		$dana=$row['dani'];
		if (intval($dana&1)>0) echo "X;";
		else echo "0;";
		if (intval($dana&2)>0) echo "X;";
		else echo "0;";
		if (intval($dana&4)>0) echo "X;";
		else echo "0;";
		if (intval($dana&8)>0) echo "X;";
		else echo "0;";
		if (intval($dana&16)>0) echo "X;";
		else echo "0;";
		if (intval($dana&32)>0) echo "X;";
		else echo "0;";
		
		$stdani=array();
		if ($st)
		{
		$resultst = mysql_query("SELECT *  FROM `oris_fairtime` WHERE `tak` = ".$takid." AND `id` = ".$row['id']." ORDER BY dan ASC",$sql);
		while($rowst = mysql_fetch_assoc($resultst)){
			$stdani[$rowst['dan']]=$rowst['st'];
		}			
		}
		
		echo ';';
		if ($st && isset($stdani[1])) echo $stdani[1].":00";
		echo ';;;0;;';
		if ($st && isset($stdani[2])) echo $stdani[2].":00";
		echo ';;;0;;';
		if ($st && isset($stdani[3])) echo $stdani[3].":00";
		echo ';;;0;;';
		if ($st && isset($stdani[4])) echo $stdani[4].":00";
		echo ';;;0;;';
		if ($st && isset($stdani[5])) echo $stdani[5].":00";
		echo ';;;0;;';
		if ($st && isset($stdani[6])) echo $stdani[6].":00";
		echo ';;;0;"'.$klub.'";"'.$row['klub'].'";"'.$row['skraceno'].'";"";'.$row['idkat'].';'.$kat.';"'.$kat.'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;';
		
		$brd=0;
			for ($j=1;$j<=$dani;$j++)
			if ((intval($row['dani']) & $j)>0)	
			$brd++;
		
			if ($brd==$dani)	
			echo intval($takmicenje['cenau']); //cena
			else echo intval($takmicenje['cenaj'])*$brd; //cena
		
		echo ';X;
';
	}

}

if ($st)
{
	if ($oe)
	{
			$result = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 1 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
			while($row=mysql_fetch_assoc($result))
			{
				echo ';;"";"Vacant";"";;;;;'.$row['st'].':00;;;0;0;"";"Vacant";"";'.$row['kat'].';"'.$row['naziv'].'";"'.$row['naziv'].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}
	}
	else if ($oenew)
		if ($dani>1)
	{
		$naziv=array();
		$st1=array();
		$st2=array();
		$st3=array();
		$st4=array();
		$st5=array();
		$st6=array();
		$result1 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 1 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result1))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st1[$row['kat']][]=$row['st'];
		}
		$result2 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 2 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result2))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st2[$row['kat']][]=$row['st'];
		}
		$result3 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 3 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result3))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st3[$row['kat']][]=$row['st'];
		}
		$result4 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 4 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result4))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st4[$row['kat']][]=$row['st'];
		}
		$result5 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 5 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result5))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st5[$row['kat']][]=$row['st'];
		}
		$result6 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 6 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result6))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st6[$row['kat']][]=$row['st'];
		}

			foreach($st1 as $kat=>$vac) 
				foreach($vac as $val)
			{
				
				echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;'.array_pop($st1[$kat]).':00;;;0;;;"";0;';
				if ($st=array_pop($st2[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st3[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;;0;"";0;;
';
			}	
					foreach($st2 as $kat=>$vac) 
					foreach($vac as $val)
			{
				if (empty($st2[$kat]))continue;
					echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;;;;0;;;"";0;'.array_pop($st2[$kat]).':00;;;0;;;"";0;';
				if ($st=array_pop($st3[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;0;"";0;;
';
			}
		foreach($st3 as $kat=>$vac)
				foreach($vac as $val)		
			{
				if (empty($st3[$kat]))continue;
				echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;;;;0;;;"";0;;;;0;;;"";0;'.array_pop($st3[$kat]).':00;;;0;;;"";0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;0;"";0;;
';
			}
foreach($st4 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st4[$kat]))continue;
				echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;'.array_pop($st4[$kat]).':00;;;0;;;"";0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;0;"";0;;
';
			}
foreach($st5 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st5[$kat]))continue;
				echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;'.array_pop($st5[$kat]).':00;;;0;;;"";0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;0;"";0;;
';
			}			
foreach($st6 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st6[$kat]))continue;
				echo ';;;;;;;;;"";"Vacant";"";;M;;;;;;;X;X;X;X;X;X;0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;;;;0;;;"";0;'.array_pop($st6[$kat]).':00;;;0;;;"";0;"";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;0;"";0;;
';
			}			
	
	}
	else
	{	
		$naziv=array();
		$st1=array();
		$result1 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 1 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result1))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st1[$row['kat']][]=$row['st'];
		}
		foreach($st1 as $kat=>$vac) 
				foreach($vac as $val)
			{
				
				echo ';;;;"";"Vacant";"";;M;;0;'.array_pop($st1[$kat]).':00;;;0';
				
				echo ';;;"";0;"Vacant";"Vacant";"";"";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;"";"";;;;;;"";"";"";;;;;;;;;;;;0;"";0;;
';
			}	
		
	}
	else
	{
		$naziv=array();
		$st1=array();
		$st2=array();
		$st3=array();
		$st4=array();
		$st5=array();
		$st6=array();
		$result1 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 1 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result1))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st1[$row['kat']][]=$row['st'];
		}
		$result2 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 2 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result2))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st2[$row['kat']][]=$row['st'];
		}
		$result3 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 3 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result3))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st3[$row['kat']][]=$row['st'];
		}
		$result4 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 4 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result4))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st4[$row['kat']][]=$row['st'];
		}
		$result5 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 5 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result5))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st5[$row['kat']][]=$row['st'];
		}
		$result6 = mysql_query("SELECT st,kat,naziv  FROM `oris_fairtime`,`kategorije` WHERE `dan` = 6 AND `tak` = ".$takid." AND oris_fairtime.`id` = -1 AND `kategorije`.`id`=oris_fairtime.kat", $sql);
		while($row=mysql_fetch_assoc($result6))
		{
			$naziv[$row['kat']]=$row['naziv'];
			$st6[$row['kat']][]=$row['st'];
		}

			foreach($st1 as $kat=>$vac) 
				foreach($vac as $val)
			{
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;'.array_pop($st1[$kat]).':00;;;0;0;';
				if ($st=array_pop($st2[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st3[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}	
					foreach($st2 as $kat=>$vac) 
					foreach($vac as $val)
			{
				if (empty($st2[$kat]))continue;
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;;;;0;0;'.array_pop($st2[$kat]).":00";
				echo ';;;0;0;';
				if ($st=array_pop($st3[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}
		foreach($st3 as $kat=>$vac)
				foreach($vac as $val)		
			{
				if (empty($st3[$kat]))continue;
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;;;;0;0;;;;0;0;'.array_pop($st3[$kat]).":00";
				echo ';;;0;0;';
				if ($st=array_pop($st4[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}
foreach($st4 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st4[$kat]))continue;
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;;;;0;0;;;;0;0;;;;0;0;'.array_pop($st4[$kat]).":00";
				echo ';;;0;0;';
				if ($st=array_pop($st5[$kat])) echo $st.":00";
				echo ';;;0;0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}
foreach($st5 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st5[$kat]))continue;
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;;;;0;0;;;;0;0;;;;0;0;;;;0;0;'.array_pop($st5[$kat]).":00";
				echo ';;;0;0;';
				if ($st=array_pop($st6[$kat])) echo $st.":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}			
foreach($st6 as $kat=>$vac) 
foreach($vac as $val)
			{
				if (empty($st6[$kat]))continue;
				echo ';;;;;;;"";"Vacant";"";;;;;;;;;X;X;X;X;X;X;0;;;;0;0;;;;0;0;;;;0;0;;;;0;0;;;;0;0;'.array_pop($st6[$kat]).":00";
				echo ';;;0;0;"";"Vacant";"";'.$kat.';"'.$naziv[$kat].'";"'.$naziv[$kat].'";;;;"";"";"";"";"";"";"";"";"";"";"";;0;"0,00";0;
';
			}			
			
	}
}	
	
	
if(IsSet($_POST['ooid']))
{
include "oo.php";
$ooid=$_POST['ooid'];
$oouser=$_POST['oouser'];
$oopass=$_POST['oopass'];
generateOOData($oouser,$oopass,$ooid);


}	
	

?>