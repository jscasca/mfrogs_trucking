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

$queryBroker="
insert into
	driver
	(
		driverFirstName,
		driverLastName,
		addressId,
		driverSSN,
		driverTel,
		driverMobile,
		carrierId,
		driverEmail,
		driverUnion,
		driverStartDate,
		driverStatus,
		driverPercentage,
		brokerId,
		termId,
		driverGender,
		driverClass,
		ethnicId,
		workId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['driverFirst'])."',
		'".mysql_real_escape_string($_REQUEST['driverLast'])."',
		'".mysql_real_escape_string($addressId)."',
		'".mysql_real_escape_string($_REQUEST['driverSSN'])."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['driverTel']))."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['driverMobile']))."',
		'".mysql_real_escape_string($_REQUEST['carrierId'])."',
		'".mysql_real_escape_string($_REQUEST['driverMail'])."',
		'".mysql_real_escape_string($_REQUEST['driverUnion'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['startupDate']))."',
		'1',
		'".mysql_real_escape_string($_REQUEST['driverPercentage'])."',
		'".mysql_real_escape_string($_REQUEST['brokerId'])."',
		'".mysql_real_escape_string($_REQUEST['termId'])."',
		'".mysql_real_escape_string($_REQUEST['driverGender'])."',
		'".mysql_real_escape_string($_REQUEST['driverClass'])."',
		'".mysql_real_escape_string($_REQUEST['ethnicId'])."',
		'".mysql_real_escape_string($_REQUEST['workId'])."'
	)";

//echo $queryBroker;
mysql_query($queryBroker,$conexion);
$brokerId = mysql_insert_id();	
mysql_close($conexion);

header ("Location:newDriver.php");

?>
