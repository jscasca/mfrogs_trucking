<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//session_start();

//p_array($_REQUEST);

//p_array($_SESSION);
$testExisting="select count(*) as existing from ticket where ticketId=".$_REQUEST['i'];
$tests=mysql_query($testExisting,$conexion);
$test=mysql_fetch_assoc($tests);

if($test['existing']==1)
{
	$queryTicket="
	UPDATE
		ticket
	SET
			truckId='".mysql_real_escape_string($_REQUEST['truckId'])."',
			ticketNumber='".mysql_real_escape_string($_REQUEST['ticketNumber'])."',
			driverId='".mysql_real_escape_string($_REQUEST['driverId'])."'
	WHERE
		ticketId=".$_GET['i'];

	//echo $queryTicket;
	mysql_query($queryTicket,$conexion);

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['ticket'])." into tickets');";
	/*
	For Log Actions:
	1 -> New (insert into)
	2 -> Edit (update from)
	3 -> Delete (delete from)
	*/
	mysql_query($queryLog,$conexion);
}

echo $queryTicket;
print_r($_REQUEST);
mysql_close($conexion);

header ("Location:/trucking/php/view/viewTicket");

?>
