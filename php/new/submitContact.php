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
//insert
mysql_query ($queryAddress, $conexion);
$addressId=mysql_insert_id();
//echo $queryAddress;

$queryContact="
insert into
	contact
	(
		contactName,
		addressId,
		contactTel,
		contactFax,
		contactMobil,
		contactMail,
		contactInfo,
		customerId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['contactName'])."',
		'".mysql_real_escape_string($addressId)."',
		'".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactTel']))."',
		'".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactFax']))."',
		'".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactMobil']))."',
		'".mysql_real_escape_string($_REQUEST['contactMail'])."',
		'".mysql_real_escape_string($_REQUEST['contactInfo'])."',
		'".mysql_real_escape_string($_REQUEST['customerId'])."'
	)";

mysql_query($queryContact,$conexion);
$contactId = mysql_insert_id();	
//echo $queryContact;

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['contactName'])." into contacts');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:newContact.php");

?>
