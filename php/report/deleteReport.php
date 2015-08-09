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
	reportticket
WHERE
	reportId=".$_GET['reportId']."
";
mysql_query($queryTickets,$conexion);

$queryInvoice="
DELETE
FROM
	report
WHERE
	reportId=".$_GET['reportId']."
		";
		

//echo $queryUpdate;
mysql_query($queryInvoice,$conexion);

mysql_close($conexion);
header ("Location: reportBroker [Invoices].php");

?>
