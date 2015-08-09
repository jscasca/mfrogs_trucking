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
	driver
SET
		driverFirstName='".mysql_real_escape_string($_REQUEST['driverFirstName'])."',
		driverLastName='".mysql_real_escape_string($_REQUEST['driverLastName'])."',
		driverMobile='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['driverMobile']))."',
		carrierId='".mysql_real_escape_string($_REQUEST['carrierId'])."',
		driverEmail='".mysql_real_escape_string($_REQUEST['driverMail'])."',
		driverSSN='".mysql_real_escape_string($_REQUEST['driverSSN'])."',
		driverPercentage='".mysql_real_escape_string($_REQUEST['driverPercentage'])."',
		driverStartDate='".to_YMD(mysql_real_escape_string($_REQUEST['driverStartDate']))."',
		driverStatus='".mysql_real_escape_string($_REQUEST['driverStatus'])."',
		driverTel='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['driverTel']))."',
		termId ='".mysql_real_escape_string($_REQUEST['termId'])."',
		ethnicId ='".mysql_real_escape_string($_REQUEST['ethnicId'])."',
		workId ='".mysql_real_escape_string($_REQUEST['workId'])."',
		driverClass ='".mysql_real_escape_string($_REQUEST['driverClass'])."',
		driverGender ='".mysql_real_escape_string($_REQUEST['driverGender'])."',
		driverPW ='".mysql_real_escape_string($_REQUEST['driverPW'])."',
		unionId ='".mysql_real_escape_string($_REQUEST['unionId'])."'
WHERE
		driverId=".$_REQUEST['i']."
		";

//echo $queryBroker;
mysql_query($queryBroker,$conexion);

//if($_REQUEST['driverRemaining731']>0){
	$firstRemaining = mysql_fetch_assoc(mysql_query("select * from remainings_731 where driverId=".$_REQUEST['i']." and remainingStartDate='0000-00-00' limit 1",$conexion));
	if($firstRemaining!=null){
		//update
		mysql_query("update remainings_731 set remainingValue='".mysql_real_escape_string($_REQUEST['driverRemaining731'])."' where driverId=".$_REQUEST['i']." and remainingStartDate='0000-00-00' ",$conexion);
		//echo "update remainings_731 set remainingValue='".mysql_real_escape_string($_REQUEST['driverRemaining731'])."' where driverId=".$_REQUEST['i']." and remainingStartDate='0000-00-00' ";
	}else{
		//insert
		mysql_query("insert into remainings_731 (driverId,remainingValue) values (".$_REQUEST['i'].",".mysql_real_escape_string($_REQUEST['driverRemaining731']).")",$conexion);
		
	}					
//}

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' ".mysql_real_escape_string($_REQUEST['driverName'])." into drivers');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:/trucking/php/view/viewDriver.php?i=".$_REQUEST['i']);

?>
