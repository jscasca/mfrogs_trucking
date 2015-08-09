<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

	$queryTicket="
	update
		fuel_load
	set
			brokerId=".$_REQUEST['brokerId'].",
			truckId=".$_REQUEST['truckId'].",
			fuelLoadDate='".to_YMD($_REQUEST['fuelDate'])."',
			fuelLoadCommet='".$_REQUEST['fuelComment']."',
			fuelLoadStart=".$_REQUEST['fuelStart'].",
			fuelLoadFinish=".$_REQUEST['fuelFinish'].",
			fuelLoadRegistered=".$_REQUEST['fuelRegistered'].",
			fuelLoadMileage=".$_REQUEST['fuelMileage']."
	where
		fuelLoadId=".$_REQUEST['i'];
	//echo $queryTicket;
	mysql_query($queryTicket,$conexion);


mysql_close($conexion);

header ("Location:../new/newFuel.php");

?>
