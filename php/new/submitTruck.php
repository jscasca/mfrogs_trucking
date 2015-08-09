<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

$coordinates=getCoordinates("{$_REQUEST['addressLine1']} {$_REQUEST['addressZip']} {$_REQUEST['addressCity']} {$_REQUEST['addressState']}");

mysql_query("begin",$conexion);
$queryAddress="
insert into
	address 
	(
		addressLine1,
		addressLine2,
		addressCity,
		addressState,
		addressZip,
		addressPOBox,
		addressLat,
		addressLong
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['addressLine1'])."',
		'".mysql_real_escape_string($_REQUEST['addressLine2'])."',
		'".mysql_real_escape_string($_REQUEST['addressCity'])."',
		'".mysql_real_escape_string($_REQUEST['addressState'])."',
		'".mysql_real_escape_string($_REQUEST['addressZip'])."',
		'".mysql_real_escape_string($_REQUEST['addressPOBox'])."',
		'".$coordinates[0]."',
		'".$coordinates[1]."'
	)";
//insert
mysql_query ($queryAddress, $conexion);
$addressId=mysql_insert_id();
//echo $queryAddress;

$queryTruck="
insert into
	truck
	(
		brokerId,
		truckNumber,
		truckDriver,
		truckPlates,
		addressId,
		truckInfo
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['brokerId'])."',
		'".mysql_real_escape_string($_REQUEST['truckNumber'])."',
		'".mysql_real_escape_string($_REQUEST['truckDriver'])."',
		'".mysql_real_escape_string($_REQUEST['truckPlates'])."',
		'".mysql_real_escape_string($addressId)."',
		'".mysql_real_escape_string($_REQUEST['contactInfo'])."'
	)";
	


mysql_query($queryTruck,$conexion);
$truckId = mysql_insert_id();	
//echo $queryTruck;

foreach($_REQUEST['truckFeatures'] as $feature){
	$queryTruckFeature="insert into truckFeature (truckId,featureId) values (".$truckId.",".$feature.")";
	//echo $queryTruckFeature."<br/>";
	mysql_query($queryTruckFeature,$conexion);
}

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['truckNumber'])." into trucks');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);
mysql_query("commit",$conexion);

mysql_close($conexion);

header ("Location:newTruck.php");

?>
