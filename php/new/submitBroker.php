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
	broker
	(
		brokerPid,
		brokerName,
		brokerContactName,
		addressId,
		brokerTax,
		brokerTel,
		brokerFax,
		brokerRadio,
		brokerMobile,
		carrierId,
		brokerEmail,
		brokerIccCert,
		brokerInsuranceWc,
		brokerWcExpire,
		brokerInsuranceLiability,
		brokerLbExpire,
		brokerGeneralLiability,
		brokerGlExp,
		brokerStartDate,
		brokerStatus,
		brokerPercentage,
		brokerGender,
		ethnicId,
		termId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['brokerPid'])."',
		'".mysql_real_escape_string($_REQUEST['brokerName'])."',
		'".mysql_real_escape_string($_REQUEST['brokerContactName'])."',
		'".mysql_real_escape_string($addressId)."',
		'".mysql_real_escape_string($_REQUEST['brokerTax'])."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['brokerTel']))."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['brokerFax']))."',
		'".mysql_real_escape_string($_REQUEST['brokerRadio'])."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['brokerMobile']))."',
		'".mysql_real_escape_string($_REQUEST['carrierId'])."',
		'".mysql_real_escape_string($_REQUEST['brokerMail'])."',
		'".mysql_real_escape_string($_REQUEST['brokerIccCert'])."',
		'".mysql_real_escape_string($_REQUEST['brokerInsWc'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['brokerWcExpire']))."',
		'".mysql_real_escape_string($_REQUEST['brokerInsLiability'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['brokerLbExpire']))."',
		'".mysql_real_escape_string($_REQUEST['brokerGeneralLiability'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['brokerGlExp']))."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['startupDate']))."',
		'1',
		'".mysql_real_escape_string($_REQUEST['brokerPercentage'])."',
		'".mysql_real_escape_string($_REQUEST['brokerGender'])."',
		'".mysql_real_escape_string($_REQUEST['ethnicId'])."',
		'".mysql_real_escape_string($_REQUEST['termId'])."'
	)";

//echo $queryBroker;
mysql_query($queryBroker,$conexion);
$brokerId = mysql_insert_id();	

if(!is_dir("../../archive/brokerId{$brokerId}"))
{
	mkdir("../../archive/brokerId{$brokerId}");
}


$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['customerName'])." into customers');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:newBroker.php");

?>
