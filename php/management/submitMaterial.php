<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryMaterial="
insert into 
	material 
		(materialName, materialDescription) 
	values 
		('".mysql_real_escape_string($_REQUEST['materialName'])."','".mysql_real_escape_string($_REQUEST['materialDescription'])."')";

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,'".mysql_real_escape_string($_REQUEST['materialName'])." into materials');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $querymaterial;
//echo $queryLog;
mysql_query($queryMaterial,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementMaterials.php");


?>

