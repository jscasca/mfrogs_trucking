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

$querySupplier="
UPDATE
	supplier
SET
		supplierName='".mysql_real_escape_string($_REQUEST['supplierName'])."',
		supplierTel='".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['supplierTel']))."',
		supplierFax='".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['supplierFax']))."',
		supplierInfo='".mysql_real_escape_string($_REQUEST['supplierInfo'])."',
		supplierDumptime='".mysql_real_escape_string($_REQUEST['supplierDumptime'])."',
		vendorId ='".mysql_real_escape_string($_REQUEST['vendorId'])."'
WHERE
		supplierId=".$_REQUEST['i']."
		";

//echo $queryContact;
mysql_query($querySupplier,$conexion);

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

header ("Location:/trucking/php/view/viewSupplier.php?i=".$_REQUEST['i']);

?>
