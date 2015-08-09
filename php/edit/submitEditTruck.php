<?php

include("../commons.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

$coordinates=getCoordinates("{$_REQUEST['addressLine1']} {$_REQUEST['addressZip']} {$_REQUEST['addressCity']} {$_REQUEST['addressState']}");

$queryAddress="
UPDATE
	address 
SET
		addressLine1='".mysql_real_escape_string($_REQUEST['addressLine1'])."',
		addressLine2='".mysql_real_escape_string($_REQUEST['addressLine2'])."',
		addressCity='".mysql_real_escape_string($_REQUEST['addressCity'])."',
		addressState='".mysql_real_escape_string($_REQUEST['addressState'])."',
		addressZip='".mysql_real_escape_string($_REQUEST['addressZip'])."',
		addressPOBox='".mysql_real_escape_string($_REQUEST['addressPOBox'])."',
		addressLat='".$coordinates[0]."',
		addressLong='".$coordinates[1]."'
WHERE
		addressId=".$_REQUEST['a']."
		";
//insert
//echo $queryAddress;
mysql_query ($queryAddress, $conexion);

$queryContact="
UPDATE
	truck
SET
		truckNumber='".mysql_real_escape_string($_REQUEST['truckNumber'])."',
		truckDriver='".mysql_real_escape_string($_REQUEST['truckDriver'])."',
		truckPlates='".mysql_real_escape_string($_REQUEST['truckPlates'])."',
		truckInfo='".mysql_real_escape_string($_REQUEST['truckInfo'])."',
		brokerId ='".mysql_real_escape_string($_REQUEST['brokerId'])."'
WHERE
		truckId=".$_REQUEST['i']."
		";

//echo $queryContact;
mysql_query($queryContact,$conexion);

$queryDeleteFeature="DELETE FROM truckFeature where truckId=".$_REQUEST['i'];

mysql_query($queryDeleteFeature,$conexion);

foreach($_REQUEST['truckFeatures'] as $feature){
	$queryTruckFeature="insert into truckFeature (truckId,featureId) values (".$_REQUEST['i'].",".$feature.")";
	//echo $queryTruckFeature."<br/>";
	mysql_query($queryTruckFeature,$conexion);
}

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['contactName'])." into contacts');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/


mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewTruck.php?i=".$_REQUEST['i']);

?>
