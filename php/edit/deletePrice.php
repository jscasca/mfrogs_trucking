<?php
include("../commons.php");
include("../conexion.php");

session_start();

//p_array($_GET);

//p_array($_SESSION);

//$newPrice = decimalPad($_GET['price']);
$queryUpdate="
DELETE
FROM
	supplierMaterial
WHERE
		supplierId=".$_GET['supplierId']." and
		materialId=".$_GET['materialId']."
		";

//echo $queryUpdate;
mysql_query($queryUpdate,$conexion);

mysql_close($conexion);


?>