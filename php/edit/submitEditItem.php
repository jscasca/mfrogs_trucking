<?php
include("../commons.php");

session_start();

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

/*
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
*/
$queryItem="
UPDATE
	item
SET
		itemMaterialPrice='".mysql_real_escape_string($_REQUEST['materialPrice'])."',
		itemBrokerCost='".mysql_real_escape_string(decimalPad($_REQUEST['itemBrokerCost']))."',
		itemCustomerCost='".mysql_real_escape_string(decimalPad($_REQUEST['itemCustomerCost']))."',
		itemType='".mysql_real_escape_string($_REQUEST['itemType'])."',
		itemDescription='".mysql_real_escape_string($_REQUEST['itemDescription'])."'
WHERE
		itemId=".$_REQUEST['i']."
		";

//echo $queryItem;
mysql_query($queryItem,$conexion);

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['i'])." into items');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewItem.php?i=".$_REQUEST['i']);

?>
