<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$querymaterial="
UPDATE 
	material 
SET
		materialName='".mysql_real_escape_string($_REQUEST['materialName'])."', 
		materialDescription='".mysql_real_escape_string($_REQUEST['materialDescription'])."'
WHERE
	materialId=".$_GET['i'];

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,'".mysql_real_escape_string($_REQUEST['materialName'])." in materials');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $querymaterial;
//echo $queryLog;
mysql_query($querymaterial,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementMaterials.php");


?>

