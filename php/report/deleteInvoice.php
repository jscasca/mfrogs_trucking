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
	invoiceticket
WHERE
	invoiceId=".$_GET['invoiceId']."
";
mysql_query($queryTickets,$conexion);

$queryInvoice="
DELETE
FROM
	invoice
WHERE
	invoiceId=".$_GET['invoiceId']."
		";
		

//echo $queryUpdate;
mysql_query($queryInvoice,$conexion);

mysql_close($conexion);

$response['invoiceId'] = $_GET['invoiceId'];

echo json_encode($response['invoiceId']);

?>
