<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryMfi="
UPDATE 
	stateinfo
SET
	sssec='".mysql_real_escape_string($_REQUEST['sssec'])."',
	medicare='".mysql_real_escape_string($_REQUEST['medicare'])."',
	other='".mysql_real_escape_string($_REQUEST['other'])."',
	withHoldingTax='".mysql_real_escape_string($_REQUEST['withHoldingTax'])."',
	hourlyRate='".mysql_real_escape_string($_REQUEST['hourlyRate'])."',
	fed='".mysql_real_escape_string($_REQUEST['fed'])."'";

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' mfiInfo Table');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryAddress;
//echo $queryMfi;
//echo $queryLog;
mysql_query($queryAddress,$conexion);
mysql_query($queryMfi,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementInformation.php");


?>

