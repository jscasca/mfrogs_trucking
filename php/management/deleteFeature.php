<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_GET);

//p_array($_SESSION);

$getTerm="
select 
	*
from
	feature
where
	featureId=".$_GET['i'].";";

$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);
if($numTerms!=0)
{
	$term = mysql_fetch_assoc($terms);
	
	$queryTerm="
	delete 
		from 
			feature
		where
			featureId=".$_GET['i'].";";
			
	$queryTermTruckFeature="
	delete 
		from 
			truckFeature
		where
			featureId=".$_GET['i'].";";
			
			

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",3,'{$term['truckFeatureName']} from truckFeature');";
			
	mysql_query($queryTerm,$conexion);
	mysql_query($queryTermTruckFeature,$conexion);
	mysql_query($queryLog,$conexion);
}


/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryTerm;
//echo $queryLog;
//mysql_query($queryTerm,$conexion);
//mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementFeatures.php");


?>

