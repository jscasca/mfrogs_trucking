<?php
include("../commons.php");
include("../conexion.php");

session_start();

//p_array($_GET);

//p_array($_SESSION);

//$newPrice = decimalPad($_GET['price']);
$queryInsert="
INSERT into
	suppliermaterial
(
	supplierId,
	materialId,
	supplierMaterialLastModified,
	supplierMaterialPrice
)
values
(
	".$_GET['supplierId'].",
	".$_GET['materialId'].",
	now(),
	0.00
)
		";

//echo $queryUpdate;
mysql_query($queryInsert,$conexion);

mysql_close($conexion);

$jsondata['lastModified']="Today";
$jsondata['newPrice']="0.00";
	
echo json_encode($jsondata);

?>
