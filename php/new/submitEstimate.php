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

$queryCustomer="
insert into
	fakeproject
	(
		fakeprojectName,
		addressId,
		customerId
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['fakeprojectName'])."',
		'".mysql_real_escape_string($addressId)."',
		'".mysql_real_escape_string($_REQUEST['customer'])."'
	)";

mysql_query($queryCustomer,$conexion);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:newEstimate.php");
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
