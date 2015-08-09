<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryTerm="
insert into 
	worker_union
		(unionName, unionDescription) 
	values 
		('".mysql_real_escape_string($_REQUEST['unionName'])."','".mysql_real_escape_string($_REQUEST['unionDescription'])."')";

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,'".mysql_real_escape_string($_REQUEST['unionName'])." into Union');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryTerm;
//echo $queryLog;
mysql_query($queryTerm,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementUnions.php");


?>

