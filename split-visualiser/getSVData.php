<?php
include_once("functions.php");
$mysql=mysqlConnect();

if(isset($_GET['class'])) $class=$_GET['class'];
   else
$class="NEXT";
$interval=10;

$ce=0;
if(isset($_GET['course']) && strcmp($_GET['course'],"true")==0) $ce=1;

$class=html_entity_decode($class);

if(strpos($class,"NEXT")!==FALSE){
	$k=explode("~",$class);
	$last=$k[1];
	if ($ce==0)
	{
		$result=mysqli_query($mysql,"SELECT distinct(class) FROM `sv_splits` ORDER BY `class` ASC");
	while($row=mysqli_fetch_assoc($result)){
		if($row['class']==$last) 
			if ($row=mysqli_fetch_assoc($result)) $class=$row['class'];
			else {
			mysqli_data_seek($result, 0);
			$row=mysqli_fetch_assoc($result);
			$class=$row['class'];
			break;
		}
	}
	if(strpos($class,"NEXT")!==FALSE){
			mysqli_data_seek($result, 0);
                        $row=mysqli_fetch_assoc($result);
                        $class=$row['class'];

	}
	}else
	{
		
		$class=intval($last)+1;
		$result=mysqli_query($mysql,"select count(*) as cnt from (SELECT distinct(controls), length(controls) as size FROM `sv_splits` order by size asc) as data");
        $row=mysqli_fetch_assoc($result);
		if($class>intval($row['cnt'])) $class=1;
		
	}
}

if(isset($_GET["getdata"])){
	$controlls=getControlls($mysql);
	if ($ce==0)
	{
		$sql="SELECT *  FROM `sv_splits` WHERE `class` LIKE '".$class."' ORDER BY `sv_splits`.`time` ASC";
		$result=mysqli_query($mysql,$sql);
	}else{
		$result=mysqli_query($mysql,"SELECT distinct(controls), length(controls) as size FROM `sv_splits` order by size asc");
		
		$row=array();
		for ($i=0;$i<intval($class);$i++)
			$row=mysqli_fetch_assoc($result);
				
		$sql="SELECT *  FROM `sv_splits` WHERE `controls` LIKE '".$row['controls']."' ORDER BY `sv_splits`.`time` ASC";
		$result=mysqli_query($mysql,$sql);
		
	}
	$row=mysqli_fetch_assoc($result);
	$coursedb=$row['controls'];
	$cntrs=explode("-", $coursedb);
	$course=array();
	$cnt=0;
	$course[$cnt++]=array("x"=>intval($controlls['S1']['x']),"y"=>intval($controlls['S1']['y']),"nr"=>"S");
	foreach ($cntrs as $control) {
		$ctr=$controlls[$control];
		$course[$cnt++]=array("x"=>intval($ctr['x']),"y"=>intval($ctr['y']),"nr"=>($control));
	}	
	//$course=normalize($course,1200,768);
	$splits=array();
	$competitors=array();
	$cnt=0;
	$cmptime=0;
	mysqli_data_seek($result, 0);
	while($row=mysqli_fetch_assoc($result))
	{
		$name=$row['firstname']." ".$row['lastname'];
		$initials=mb_substr($row['firstname'], 0, 1,'UTF-8').mb_substr($row['lastname'], 0, 1,'UTF-8');
		$split=array();
		$splitstr=$row['splits'];
		$splite=explode("-",$splitstr);
		$spcnt=1;
		foreach ($splite as $sp) {
			$split[$spcnt++]=round($sp/$interval);
		}
		//print_r($split);
		$splits[$cnt]=$split;
		$competitors[$cnt]=array("name"=>$name,"init"=>$initials,"time"=>formatTime($row['time']));
		$cnt++;
		//$cmptime=($cmptime+intval($row['time']))/2;
		$cmptime=intval($row['time']);
	}
	
	$data=array("course"=>$course,"competitors"=>$competitors,"splits"=>$splits,"class"=>$class,"last"=>round($cmptime/$interval));
	//print_r($data);
	print json_encode($data);
}else if(isset($_GET["getclasses"])){
	$classes=array();
	if($ce==0)
	{
		$result=mysqli_query($mysql,"SELECT distinct(class) FROM `sv_splits` ORDER BY `class` ASC");
	}else{
		$result=mysqli_query($mysql,"SELECT distinct(controls), length(controls) as size FROM `sv_splits` order by size asc");
	}
	$cnt=1;
	while($row=mysqli_fetch_assoc($result)){
		if ($ce==0)
			$classes[]=$row['class'];
		else
			$classes[]=$cnt++;
	}
	print json_encode($classes);
}

function getControlls($mysql)
{
	$controlls=array();
	$result=mysqli_query($mysql,"SELECT * FROM `sv_controls`");
	while($row=mysqli_fetch_assoc($result))
			$controlls[($row['control'])]=array("x"=>$row['x'],"y"=>$row['y']);

return $controlls;	
}

function formatTime($time){
	$time=intval($time);
	$ost=$time%60;
	if (strlen($ost)<2) $ost='0'.$ost;
	return floor($time/60).":".$ost;
}

function normalize($course,$x,$y){
	$minx=1000000000;
	$maxx=0;
	$miny=1000000000;
	$maxy=0;
	
	foreach ($course as $control) {
		if($control['x']>$maxx) $maxx=$control['x'];
		if($control['x']<$minx) $minx=$control['x'];
		if($control['y']>$maxy) $maxy=$control['y'];
		if($control['y']<$miny) $miny=$control['y'];		
	}
	
	$difx=$maxx-$minx;
	$dify=$maxy-$miny;
	$zx=($x-100)/$difx;
	$zy=($y-100)/$dify;
	if ($zx<$zy) $z=$zx;
	else $z=$zy;
	
	//echo "dx:".$difx." dy:".$dify." zx:".$zx." zy:".$zy;
	//echo "xmin:".$minx." ymin:".$miny;
	
	//print_r($course);
	$newcourse=array();
	foreach ($course as $nr => $control) {
		$newcourse[$nr]=array('x'=>(($control['x'])-$minx)*$z+50,'y'=>($control['y']-$miny)*$z+50,'nr'=>$control['nr']);
		//echo ($control['x'])-$xmin;
	}
	//print_r($newcourse);
	return $newcourse;
}


?>
