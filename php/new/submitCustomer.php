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
		'".$coordenadas[0]."',
		'".$coordenadas[1]."'
	)";
//insert
mysql_query ($queryAddress, $conexion);
$addressId=mysql_insert_id();

$queryCustomer="
insert into
	customer
	(
		customerName,
		addressId,
		customerTel,
		customerFax,
		customerWebsite,
		termId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['customerName'])."',
		'".mysql_real_escape_string($addressId)."',
		'".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['customerTel']))."',
		'".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['customerFax']))."',
		'".mysql_real_escape_string($_REQUEST['customerWebsite'])."',
		'".mysql_real_escape_string($_REQUEST['termId'])."'
	)";

mysql_query($queryCustomer,$conexion);
$customerId = mysql_insert_id();	
if(!is_dir("../../archive/customerId{$customerId}"))
{
	mkdir("../../archive/customerId{$customerId}");
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

header ("Location:newCustomer.php");
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
