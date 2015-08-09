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
	
mysql_query ($queryAddress, $conexion);
$addressId=mysql_insert_id();

$queryVendor="
insert into
	vendor
	(
		vendorName,
		vendorInfo,
		vendorComment,
		vendorTel,
		vendorFax,
		addressId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['vendorName'])."',
		'".mysql_real_escape_string($_REQUEST['vendorComment'])."',
		'".mysql_real_escape_string($_REQUEST['vendorInfo'])."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['vendorTel']))."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['vendorFax']))."',
		'".$addressId."'
	)";

mysql_query($queryVendor,$conexion);
$vendorId = mysql_insert_id();	
//echo $queryVendor;

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['vendorName'])." into vendors');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:newVendor.php");

?>
