<?php

include_once("functions.php");

$xml = simplexml_load_file(dirname(__FILE__).'/upload/courses.xml');
 
  
$controlls=array();

$controlls[trim((String)$xml->{"StartPoint"}->{"StartPointCode"})]=array("x"=>intval($xml->{"StartPoint"}->{"MapPosition"}['x']),"y"=>-1*intval($xml->{"StartPoint"}->{"MapPosition"}['y']));;

$conts=$xml->{"Control"};
foreach ($conts as $control) {
	$controlls[trim((String)$control->{"ControlCode"})]=array("x"=>intval($control->{"MapPosition"}['x']),"y"=>-1*intval($control->{"MapPosition"}['y']));;
}

$controlls[trim((String)$xml->{"FinishPoint"}->{"FinishPointCode"})]=array("x"=>intval($xml->{"FinishPoint"}->{"MapPosition"}['x']),"y"=>-1*intval($xml->{"FinishPoint"}->{"MapPosition"}['y']));;

$classes=array();
/*
$crss=$xml->{"Course"};
foreach ($crss as $course) {
	$class=array();
	$classs=$course->{"ClassShortName"};
	foreach ($classs as $classn) {
		$name=strtoupper(trim((String)$classn));
		$name=str_replace(" ", "", $name);
		$name=str_replace(chr(194), "Ž", $name);
		$name=str_replace(chr(142), "", $name);
		$class[]=$name;
	}

	$courss=array();
	$css=$course->{"CourseVariation"}->{"CourseControl"};
	$courss[]=trim((String)$course->{"CourseVariation"}->{"StartPointCode"});
	foreach ($css as $cs) {
		$courss[]=trim((String)$cs->{"ControlCode"});
	}
	$courss[]=trim((String)$course->{"CourseVariation"}->{"FinishPointCode"});
	
	foreach ($class as $cs) {
	$classes[$cs]=$courss;
	}

	
}
*/
$mysql=mysqlConnect();
$result=mysqli_query($mysql,"TRUNCATE sv_controls");
$controlls=normalize($controlls,1200,768);
foreach ($controlls as $control => $cord) {
	$x=$cord["x"];
	$y=$cord["y"];
	$result=mysqli_query($mysql,"INSERT INTO `sv_controls` (`control`, `x`, `y`) VALUES ('".$control."', '".round($x)."', '".round($y)."');");
}

/*
$result=mysqli_query($mysql,"TRUNCATE sv_classes");
foreach ($classes as $class => $controlls) {
	$controls="";
	foreach ($controlls as $cont) 
		$controls.='\"'.$cont."\",";
	
	$controls=rtrim($controls, ",");
	
	$result=mysqli_query($mysql,"INSERT INTO `sv_classes` (`name`, `controls`) VALUES ('".$class."', '".$controls."');");
}


print_r($controlls);
print_r($classes);
*/
//print_r($xml);
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
		$newcourse[$nr]=array('x'=>(($control['x'])-$minx)*$z+50,'y'=>($control['y']-$miny)*$z+50);
		//echo ($control['x'])-$xmin;
	}
	//print_r($newcourse);
	return $newcourse;
}


?>
