<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_GET);

//p_array($_SESSION);

$deleteQuery = "delete from fuel_load where fuelLoadId=".$_GET['i'];

mysql_query($deleteQuery,$conexion);
	

$jsonData['deletedId'] = $_GET['i'];

echo json_encode($jsonData);

mysql_close();


?>


