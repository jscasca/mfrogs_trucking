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
	project
SET
		projectName='".mysql_real_escape_string($_REQUEST['projectName'])."',
		projectStartup='".to_YMD(mysql_real_escape_string($_REQUEST['projectStartup']))."',
		jobLandId='". $_REQUEST['jobLand'] ."',
		jobTerrainId='". $_REQUEST['jobTerrain'] ."',
		projectCounty='".mysql_real_escape_string($_REQUEST['projectCounty'])."',
		projectTownship='".mysql_real_escape_string($_REQUEST['projectTownship'])."',
		projectIepa='".mysql_real_escape_string($_REQUEST['projectIepa'])."',
		projectBow='".mysql_real_escape_string($_REQUEST['projectBow'])."',
		projectBoa='".mysql_real_escape_string($_REQUEST['projectBoa'])."',
		projectMaterial='".mysql_real_escape_string(implode(",",$_REQUEST['typemat']))."',
		projectSw='".mysql_real_escape_string($_REQUEST['projectSw'])."',
		projectLoads='".mysql_real_escape_string($_REQUEST['projectLoads'])."',
		projectTrucks='".mysql_real_escape_string($_REQUEST['projectTrucks'])."',
		projectEnviromental='".mysql_real_escape_string($_REQUEST['projectEnvironmental'])."',
		projectPin='".mysql_real_escape_string($_REQUEST['projectPin'])."',
		customerId='".$_REQUEST['customer']."',
		contactId='". (isset($_REQUEST['contact'])?$_REQUEST['contact']:"0")."',
		projectCompany='".mysql_real_escape_string($_REQUEST['projectCompany'])."',
		projectClass1PW='".mysql_real_escape_string($_REQUEST['projectClass1PW'])."',
		projectClass2PW='".mysql_real_escape_string($_REQUEST['projectClass2PW'])."',
		projectClass3PW='".mysql_real_escape_string($_REQUEST['projectClass3PW'])."',
		projectClass4PW='".mysql_real_escape_string($_REQUEST['projectClass4PW'])."',
		projectBrokerPW='".mysql_real_escape_string($_REQUEST['projectBrokerPW'])."',
		projectUnder='".mysql_real_escape_string($_REQUEST['projectUnder'])."',
		projectApprovalNumber='".mysql_real_escape_string($_REQUEST['projectApprovalNumber'])."'
WHERE
		projectId=".$_REQUEST['i']."
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

if(!is_dir("../../archive/customerId{$_REQUEST['customer']}/job{$job}}"))
{
	mkdir("../../archive/customerId{$_REQUEST['customer']}/job{$job}");
	chmod("../../archive/customerId{$_REQUEST['customer']}/job{$job}", 0777);
}

mysql_close($conexion);

header ("Location:/trucking/php/view/viewJob.php?i=".$_REQUEST['i']);

?>
