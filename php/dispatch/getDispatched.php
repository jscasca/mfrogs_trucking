<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

if(isset($_GET['dispatchId'])){

	$queryDispatched = "select truckId from truck JOIN truckdispatch using(truckId) where dispatchId = ".$_GET['dispatchId'];
	$trucks = mysql_query($queryDispatched,$conexion);
	$truckString[]="0";
	while($truck = mysql_fetch_assoc($trucks)){
		$truckString[]=$truck['truckId'];
	}
}
$jsondata['trucks']=$truckString;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

