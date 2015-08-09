<?php
include("../commons.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

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

//p_array($_REQUEST);
mysql_query ($queryAddress, $conexion);
//p_array($_SESSION);

$queryVendor="
UPDATE
	vendor
SET
		vendorName='".mysql_real_escape_string($_REQUEST['vendorName'])."',
		vendorInfo='".mysql_real_escape_string($_REQUEST['vendorInfo'])."',
		vendorTel='".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['vendorTel']))."',
		vendorFax='".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['vendorFax']))."',
		vendorComment ='".mysql_real_escape_string($_REQUEST['vendorComment'])."'
WHERE
		vendorId=".$_REQUEST['i']."
		";

//echo $queryVendor;
mysql_query($queryVendor,$conexion);

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['vendorName'])." into vendors');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewVendor.php?i=".$_REQUEST['i']);

?>
