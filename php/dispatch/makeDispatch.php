<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['dispatchId']))
{
	if(isset($_GET['dispatchedId']) ){
	
		$queryTruckDispatched = "select * from truckdispatch JOIN dispatch using(dispatchId) where truckId = ".$_GET['dispatchedId']." and dispatchId = '".$_GET['dispatchId']."'";
		//echo $queryTruckDispatched;
		$truckDispatched = mysql_query($queryTruckDispatched,$conexion);
		$numTrucks = mysql_num_rows($truckDispatched);
		if($numTrucks>0){
			$dispatch = mysql_fetch_assoc($truckDispatched);
			mysql_query("delete from truckdispatch where dispatchId = ".$dispatch['dispatchId']." ",$conexion);
			$action = "delete";
		}else{
			$insertDispatch = "
			INSERT INTO
				truckdispatch
				(dispatchId, truckId, truckDispatchDate)
				values
				('".$_GET['dispatchId']."','".$_GET['dispatchedId']."',now())";
			mysql_query($insertDispatch,$conexion);
			$action = "insert";
		}
		
	$response['id']=$_GET['dispatchedId'];
	$response['action']=$action;
	echo json_encode($response);
	}
}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

