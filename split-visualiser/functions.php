<?php
//error_reporting(E_ERROR | E_PARSE);
$dbserver='db11.cpanelhosting.rs';
$dbuser='oss_oris';
$dbpass='GHKaQx32XKa7';
$dbname='oss_oris';


function mysqlConnect()
{
global $dbserver;
global $dbuser;
global $dbpass;
global $dbname;
	
$sql = mysqli_connect($dbserver,$dbuser,$dbpass);
if (!$sql ) {
    die('Could not connect: ' . mysql_error());
}
mysqli_select_db($sql,$dbname);
mysqli_set_charset($sql,'utf8');
return $sql;
}
?>