<?php
include("../commons.php");
include("../conexion.php");

session_start();

//p_array($_GET);

//p_array($_SESSION);

$newPrice = decimalPad($_GET['price']);
$queryUpdate="
UPDATE
	suppliermaterial
SET
		supplierMaterialLastModified=now(),
		supplierMaterialPrice='".mysql_real_escape_string($newPrice)."'
WHERE
		supplierId=".$_GET['supplierId']." and
		materialId=".$_GET['materialId']."
		";

//echo $queryUpdate;
$jsondata['query'] = $queryUpdate;
mysql_query($queryUpdate,$conexion);

mysql_close($conexion);

$jsondata['lastModified']="Today";
$jsondata['newPrice']=$newPrice;
	
echo json_encode($jsondata);

?>
