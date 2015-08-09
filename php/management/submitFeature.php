<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryTruckFeature="
insert into 
	feature 
		(featureName, featureDescription) 
	values 
		('".mysql_real_escape_string($_REQUEST['truckFeatureName'])."','".mysql_real_escape_string($_REQUEST['truckFeatureDescription'])."')";

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,'".mysql_real_escape_string($_REQUEST['truckFeatureName'])." into features');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $querytruckFeature;
//echo $queryLog;
mysql_query($queryTruckFeature,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementFeatures.php");

?>

