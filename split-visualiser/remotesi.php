<?php

print_r($_POST);

$si="2800632";
$split="{'finish': datetime.datetime(2016, 8, 28, 12, 34, 43), 'clear': None, 'check': datetime.datetime(2016, 8, 28, 11, 27, 1), 'start': datetime.datetime(2016, 8, 28, 11, 30, 5), 'punches': [(62, datetime.datetime(2016, 8, 28, 11, 31, 34)), (35, datetime.datetime(2016, 8, 28, 11, 35, 9)), (44, datetime.datetime(2016, 8, 28, 11, 37, 47)), (47, datetime.datetime(2016, 8, 28, 11, 48, 33)), (66, datetime.datetime(2016, 8, 28, 11, 55, 34)), (49, datetime.datetime(2016, 8, 28, 12, 3, 31)), (59, datetime.datetime(2016, 8, 28, 12, 6, 11)), (51, datetime.datetime(2016, 8, 28, 12, 7, 30)), (50, datetime.datetime(2016, 8, 28, 12, 8, 40)), (60, datetime.datetime(2016, 8, 28, 12, 13, 59)), (45, datetime.datetime(2016, 8, 28, 12, 25, 36)), (61, datetime.datetime(2016, 8, 28, 12, 32, 9)), (39, datetime.datetime(2016, 8, 28, 12, 33, 33)), (58, datetime.datetime(2016, 8, 28, 12, 34, 2)), (100, datetime.datetime(2016, 8, 28, 12, 34, 32)), (57, datetime.datetime(2016, 8, 28, 14, 23, 42)), (34, datetime.datetime(2016, 8, 28, 14, 26, 28)), (58, datetime.datetime(2016, 8, 28, 14, 26, 44)), (36, datetime.datetime(2016, 8, 28, 14, 26, 49)), (37, datetime.datetime(2016, 8, 28, 14, 27, 4)), (55, datetime.datetime(2016, 8, 28, 14, 27, 58)), (39, datetime.datetime(2016, 8, 28, 14, 28, 7)), (57, datetime.datetime(2016, 8, 28, 14, 29, 53)), (41, datetime.datetime(2016, 8, 28, 14, 29, 59)), (68, datetime.datetime(2016, 8, 28, 14, 30, 7)), (56, datetime.datetime(2016, 8, 28, 14, 31, 27)), (44, datetime.datetime(2016, 8, 28, 14, 32, 24)), (45, datetime.datetime(2016, 8, 28, 14, 32, 31)), (45, datetime.datetime(2016, 8, 28, 14, 32, 47))], 'card_number': 2800632}";

$parts=explode("),",$split);
print_r($parts);

$start=parseDate("'start': datetime.datetime(2016, 8, 28, 11, 30, 5");
$ctrl=parseDate("(35, datetime.datetime(2016, 8, 28, 11, 35, 9)");
print $start->format('Y/m/d H:i:s'); 

$dif=$start->diff($ctrl);;
print_r($dif);

function parseDate($date){
	$pos=strpos($date,"time(");
	$pos+=5;
	$end=strpos($date,")");
	if ($end===false) $end=strlen($date);
	$extracted=substr($date, $pos, $end-$pos);
	$parts=explode(", ",$extracted);
	$datetime = new DateTime(); 
    $datetime->setDate($parts[0], $parts[1], $parts[2]); 
    $datetime->setTime($parts[3], $parts[4], $parts[5]); 
	return $datetime;
}


?>