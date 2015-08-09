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

//echo $queryAddress;
mysql_query ($queryAddress, $conexion);

$queryBroker="
UPDATE
	fakeproject
SET
		fakeprojectName='".mysql_real_escape_string($_REQUEST['fakeprojectName'])."',
		customerId='".$_REQUEST['customer']."'
WHERE
		fakeprojectId=".$_REQUEST['i']."
		";

//echo $queryBroker;
mysql_query($queryBroker,$conexion);


if(!is_dir("../../archive/customerId{$_REQUEST['customer']}/job{$job}}"))
{
	mkdir("../../archive/customerId{$_REQUEST['customer']}/job{$job}");
	chmod("../../archive/customerId{$_REQUEST['customer']}/job{$job}", 0777);
}

mysql_close($conexion);

header ("Location:/trucking/php/view/viewShellProject.php?i=".$_REQUEST['i']);

?>
