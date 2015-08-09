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

$queryContact="
UPDATE
	contact
SET
		contactName='".mysql_real_escape_string($_REQUEST['contactName'])."',
		contactTel='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactTel']))."',
		contactFax='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactFax']))."',
		contactMobil='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['contactMobil']))."',
		contactMail='".mysql_real_escape_string($_REQUEST['contactMail'])."',
		contactInfo='".mysql_real_escape_string($_REQUEST['contactInfo'])."',
		customerId ='".mysql_real_escape_string($_REQUEST['customerId'])."'
WHERE
		contactId=".$_REQUEST['i']."
		";

//echo $queryContact;
mysql_query($queryContact,$conexion);

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

header ("Location:/trucking/php/view/viewContact.php?i=".$_REQUEST['i']);

?>
