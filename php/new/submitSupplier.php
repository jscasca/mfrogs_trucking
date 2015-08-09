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

$querySupplier="
insert into
	supplier
	(
		supplierName,
		supplierTel,
		supplierFax,
		addressId,
		vendorId,
		supplierDumptime,
		supplierInfo
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['supplierName'])."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['supplierTel']))."',
		'".mysql_real_escape_string(cleanPhoneNumber($_REQUEST['supplierFax']))."',
		'".mysql_real_escape_string($addressId)."',
		'".mysql_real_escape_string($_REQUEST['vendorId'])."',
		'".mysql_real_escape_string($_REQUEST['supplierDumptime'])."',
		'".mysql_real_escape_string($_REQUEST['supplierInfo'])."'
	)";

	//echo$querySupplier;
mysql_query($querySupplier,$conexion);
$supplierId = mysql_insert_id();	

foreach($_REQUEST['material'] as $material)
{
	foreach($material as $key=>$value)
	{
		if(preg_match("/free/i",$value))
			$value=0;
		if(!is_nan($value) && $value!="")
		{
			$query = 
			"insert into 
				supplierMaterial 
			(
			supplierId,
			materialId,
			supplierMaterialLastModified,
			supplierMaterialPrice
			) 
				values
			(
			".$supplierId.",
			".$key.",
			now(),
			".decimalPad($value)."
			)";
			//echo$query."<br/>";
			mysql_query($query,$conexion);
		}
	}
}

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['supplierName'])." into suppliers');";


mysql_query($queryLog,$conexion);

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close($conexion);

header ("Location:newSupplier.php");

?>
