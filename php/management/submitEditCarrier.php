<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryCarrier="
UPDATE 
	carrier 
SET
		carrierName='".mysql_real_escape_string($_REQUEST['carrierName'])."', 
		carrierMail='".mysql_real_escape_string($_REQUEST['carrierMail'])."'
WHERE
	carrierId=".$_GET['i'];

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,'".mysql_real_escape_string($_REQUEST['carrierName'])." in carriers');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryCarrier;
//echo $queryLog;
mysql_query($queryCarrier,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementCarriers.php");


?>

