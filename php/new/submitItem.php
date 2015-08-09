<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);


$queryItem="
insert into
	item
	(
		itemNumber,
		projectId,
		supplierId,
		materialId,
		fromAddressId,
		toAddressId,
		itemDisplayTo,
		itemDisplayFrom,
		itemMaterialPrice,
		itemBrokerCost,
		itemCustomerCost,
		itemType,
		itemDescription
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['itemNumber'])."',
		'".mysql_real_escape_string($_REQUEST['projectId'])."',
		'".mysql_real_escape_string($_REQUEST['supplierId'])."',
		'".mysql_real_escape_string($_REQUEST['material'])."',
		'".mysql_real_escape_string($_REQUEST['fromAddressId'])."',
		'".mysql_real_escape_string($_REQUEST['toAddressId'])."',
		'".mysql_real_escape_string($_REQUEST['toAddress'])."',
		'".mysql_real_escape_string($_REQUEST['fromAddress'])."',
		'".mysql_real_escape_string($_REQUEST['materialPrice'])."',
		'".mysql_real_escape_string(decimalPad($_REQUEST['itemClientCost']))."',
		'".mysql_real_escape_string(decimalPad($_REQUEST['itemCustomerCost']))."',
		'".mysql_real_escape_string($_REQUEST['itemType'])."',
		'".mysql_real_escape_string($_REQUEST['itemDescription'])."'
	)";

//echo $queryItem;
mysql_query($queryItem,$conexion);

mysql_close($conexion);

header ("Location:newItem.php");

?>
