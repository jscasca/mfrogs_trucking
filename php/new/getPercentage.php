<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['type']) && isset($_GET['id']))
{
	if($_GET['type'] == 'driver') {
		$query = "select driverPercentage as percentage from driver where driverId = ".$_GET['id'];
	} else {
		$query = "select brokerPercentage as percentage from broker where brokerId = ".$_GET['id'];
	}
	$result = mysql_query($query,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata['percentage'] = $row['percentage'];
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

