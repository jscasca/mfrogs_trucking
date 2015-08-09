<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);


if(isset($_GET['dispatchId']) && isset($_GET['truckId']) && isset($_GET['comment'])){
	$queryUpdate="update truckdispatch set truckDispatchComment='".$_GET['comment']."' where dispatchId=".$_GET['dispatchId']." and truckId=".$_GET['truckId'];
	mysql_query($queryUpdate,$conexion);
}
mysql_close();


?>

