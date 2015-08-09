<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['truckFeatureName']))
{
	$queryTruckFeature =
	"SELECT
		*
	FROM
		feature
	WHERE
		featureName = '".$_GET['truckFeatureName']."'";
	$result = mysql_query($queryTruckFeature,$conexion);
	
	$row = mysql_fetch_assoc($result);
	
	$jsondata['truckFeatureName']=$row['featureName'];
	
	echo json_encode($jsondata);
}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

