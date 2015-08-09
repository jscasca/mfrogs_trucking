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
	broker
SET
		brokerPid='".mysql_real_escape_string($_REQUEST['brokerPid'])."',
		brokerName='".mysql_real_escape_string($_REQUEST['brokerName'])."',
		brokerContactName='".mysql_real_escape_string($_REQUEST['brokerContactName'])."',
		brokerRadio='".mysql_real_escape_string($_REQUEST['brokerRadio'])."',
		brokerMobile='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['brokerMobile']))."',
		carrierId='".mysql_real_escape_string($_REQUEST['carrierId'])."',
		brokerEmail='".mysql_real_escape_string($_REQUEST['brokerMail'])."',
		brokerTax='".mysql_real_escape_string($_REQUEST['brokerTax'])."',
		brokerIccCert='".mysql_real_escape_string($_REQUEST['brokerIccCert'])."',
		brokerInsuranceWc='".mysql_real_escape_string($_REQUEST['brokerInsWc'])."',
		brokerWcExpire='".to_YMD(mysql_real_escape_string($_REQUEST['brokerWcExpire']))."',
		brokerInsuranceLiability='".mysql_real_escape_string($_REQUEST['brokerInsLiability'])."',
		brokerLbExpire='".to_YMD(mysql_real_escape_string($_REQUEST['brokerLbExpire']))."',
		brokerGeneralLiability='".mysql_real_escape_string($_REQUEST['brokerGeneralLiability'])."',
		brokerGlExp='".to_YMD(mysql_real_escape_string($_REQUEST['brokerGlExp']))."',
		brokerPercentage='".mysql_real_escape_string($_REQUEST['brokerPercentage'])."',
		brokerStartDate='".to_YMD(mysql_real_escape_string($_REQUEST['startupDate']))."',
		brokerStatus='".mysql_real_escape_string($_REQUEST['brokerStatus'])."',
		brokerTel='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['brokerTel']))."',
		brokerFax='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['brokerFax']))."',
		brokerGender='".mysql_real_escape_string($_REQUEST['brokerGender'])."',
		ethnicId='".mysql_real_escape_string($_REQUEST['ethnicId'])."',
		termId ='".mysql_real_escape_string($_REQUEST['termId'])."'
WHERE
		brokerId=".$_REQUEST['i']."
		";

//echo $queryBroker;
mysql_query($queryBroker,$conexion);

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['brokerName'])." into brokers');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewBroker.php?i=".$_REQUEST['i']);

?>
