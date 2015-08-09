<?php
include("../commons.php");

session_start();

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

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

$queryCustomer="
UPDATE
	customer
SET
		customerName='".mysql_real_escape_string($_REQUEST['customerName'])."',
		customerTel='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['customerTel']))."',
		customerFax='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['customerFax']))."',
		customerWebsite='".mysql_real_escape_string($_REQUEST['customerWebsite'])."',
		termId ='".mysql_real_escape_string($_REQUEST['termId'])."'
WHERE
		customerId=".$_REQUEST['i']."
		";

//echo $queryCustomer;
mysql_query($queryCustomer,$conexion);

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['customerName'])." into customers');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewCustomer.php?i=".$_REQUEST['i']);

?>
