<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);


if(isset($_GET['dispatchId']) && isset($_GET['truckId']) && isset($_GET['set'])){
	$queryUpdate="update truckdispatch set truckDispatchPapers=".$_GET['set']." where dispatchId=".$_GET['dispatchId']." and truckId=".$_GET['truckId'];
	mysql_query($queryUpdate,$conexion);
}
mysql_close();


?>

