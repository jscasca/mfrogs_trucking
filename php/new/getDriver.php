<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['truckId']))
{
	$queryTrucks =
	"SELECT
		*
	FROM
		lastdrivingrelation
	WHERE
		truckId = '".$_GET['truckId']."'";
	$result = mysql_query($queryTrucks,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata['driverId'] = $row['driverId'];
	}
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

