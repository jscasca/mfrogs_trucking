<?php
include("../commons.php");
include("../conexion.php");

session_start();

//p_array($_GET);

//p_array($_SESSION);

//$newPrice = decimalPad($_GET['price']);
$queryTickets="
DELETE
FROM
	supplierinvoiceticket
WHERE
	supplierInvoiceId=".$_GET['reportId']."
";
mysql_query($queryTickets,$conexion);

$queryInvoice="
DELETE
FROM
	supplierinvoice
WHERE
	supplierInvoiceId=".$_GET['reportId']."
		";
		
//echo $queryUpdate;
mysql_query($queryInvoice,$conexion);

mysql_close($conexion);
header ("Location: reportSupplier.php");

?>
