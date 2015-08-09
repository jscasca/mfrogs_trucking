<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$addressId01 = $_REQUEST['addressId'];
$fakeprojectId=$_REQUEST['fakeprojectId'];
$projectId=$_REQUEST['projectId'];

//p_array($_REQUEST);

//p_array($_SESSION);
/*
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
$addressId=mysql_insert_id();*/


if($_REQUEST['projectStartup']=="")
	$date='now()';
else
	$date="'".to_YMD(mysql_real_escape_string($_REQUEST['projectStartup']))."'";
	
	
	
$queryCustomer="
insert into
	project
	(
		projectName,
		projectStartup,
		jobLandId,
		jobTerrainId,
		addressId,
		projectCounty,
		projectTownship,
		projectIepa,
		projectBow,
		projectBoa,
		projectMaterial,
		projectSw,
		projectLoads,
		projectTrucks,
		projectEnviromental,
		projectPin,
		customerId,
		contactId,
		projectCompany,
		projectClass1PW,
		projectClass2PW,
		projectClass3PW,
		projectClass4PW,
		projectBrokerPW,
		projectApprovalNumber
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['projectName'])."',
		".$date.",
		'".mysql_real_escape_string($_REQUEST['jobLand'])."',
		'".mysql_real_escape_string($_REQUEST['jobTerrain'])."',
		'".mysql_real_escape_string($addressId01)."',
		'".mysql_real_escape_string($_REQUEST['projectCounty'])."',
		'".mysql_real_escape_string($_REQUEST['projectTownship'])."',
		'".mysql_real_escape_string($_REQUEST['projectIepa'])."',
		'".mysql_real_escape_string($_REQUEST['projectBow'])."',
		'".mysql_real_escape_string($_REQUEST['projectBoa'])."',
		'".mysql_real_escape_string(implode(",",$_REQUEST['typemat']))."',
		'".mysql_real_escape_string($_REQUEST['projectSw'])."',
		'".mysql_real_escape_string($_REQUEST['projectLoads'])."',
		'".mysql_real_escape_string($_REQUEST['projectTrucks'])."',
		'".mysql_real_escape_string($_REQUEST['projectEnvironmental'])."',
		'".mysql_real_escape_string($_REQUEST['projectPin'])."',
		'".mysql_real_escape_string($_REQUEST['customerId'])."',
		'".mysql_real_escape_string($_REQUEST['contact'])."',
		'".mysql_real_escape_string($_REQUEST['projectCompany'])."',
		'".mysql_real_escape_string($_REQUEST['projectClass1PW'])."',
		'".mysql_real_escape_string($_REQUEST['projectClass2PW'])."',
		'".mysql_real_escape_string($_REQUEST['projectClass3PW'])."',
		'".mysql_real_escape_string($_REQUEST['projectClass4PW'])."',
		'".mysql_real_escape_string($_REQUEST['projectBrokerPW'])."',
		'".mysql_real_escape_string($_REQUEST['projectApprovalNumber'])."'
	)";

mysql_query($queryCustomer,$conexion);
//echo$queryCustomer;
$job = mysql_insert_id();

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

mysql_query ("insert into owner (ownerName,addressId,projectId) values ('',$addressId,$job)",$conexion);
$owner = mysql_insert_id();


if(!is_dir("../../archive/customerId{$_REQUEST['customer']}/job{$job}}"))
{
	mkdir("../../archive/customerId{$_REQUEST['customer']}/job{$job}");
	chmod("../../archive/customerId{$_REQUEST['customer']}/job{$job}", 0777);
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
//mysql_query($queryLog,$conexion);

$queryfakeitems="Select * 
	from fakeitem 
	where projectId=".$fakeprojectId;
$reg=mysql_query($queryfakeitems,$conexion);


while($projectInfo = mysql_fetch_assoc($reg))
{
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
		'".mysql_real_escape_string($projectInfo['itemNumber'])."',
		'".mysql_real_escape_string($projectId)."',
		'".mysql_real_escape_string($projectInfo['supplierId'])."',
		'".mysql_real_escape_string($projectInfo['materialId'])."',
		'".mysql_real_escape_string($projectInfo['fromAddressId'])."',
		'".mysql_real_escape_string($projectInfo['toAddressId'])."',
		'".mysql_real_escape_string($projectInfo['itemDisplayTo'])."',
		'".mysql_real_escape_string($projectInfo['itemDisplayFrom'])."',
		'".mysql_real_escape_string(decimalPad($projectInfo['itemMaterialPrice']))."',
		'".mysql_real_escape_string(decimalPad($projectInfo['itemBrokerCost']))."',
		'".mysql_real_escape_string(decimalPad($projectInfo['itemCustomerCost']))."',
		'".mysql_real_escape_string($projectInfo['itemType'])."',
		'".mysql_real_escape_string($projectInfo['itemDescription'])."'
	)";

//echo $queryItem;
mysql_query($queryItem,$conexion);

$queryTicket="
	delete
		from
			fakeitem
		where
			itemId=".$projectInfo['itemId'];

	mysql_query($queryTicket,$conexion);
}


$queryFake="
	delete
		from
			fakeproject
		where
			fakeprojectId=".$fakeprojectId;

mysql_query($queryFake,$conexion);
	
mysql_close($conexion);


header ("Location:owner.php?i=$owner");
/*


$po = "select * from cliente where id={$_REQUEST['customer']}";
$reg=mysql_query($po,$conexion);
while($lista=mysql_fetch_assoc($reg))
{
	$nombre=$lista['nombre_cliente'];
}

mysql_query($query,$conexion);
$my_error = mysql_error($conexion);

if(!empty($my_error))
{ 
echo "Ha habido un error al insertar los valores. $my_error:"."<p>"; 
} 
else {

if(!is_dir("../../archivos/{$nombre}/{$_REQUEST['nompro']}"))
{
	mkdir("../../archivos/{$nombre}/{$_REQUEST['nompro']}");
}

mysql_close($conexion);

header ("Location: jobs.php");

}
*/
?>
