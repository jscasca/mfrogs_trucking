<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$reportId = $_REQUEST['supplierInvoiceId'];
$vendorId = $_GET['vendorId'];
$supplierId = $_GET['supplierId'];

$additionalParams = "?vendorId=$vendorId&supplierId=$supplierId";
$date='now()';

$queryCustomer="
insert into
	suppliercheque
	(
		supplierchequeDate,
		supplierInvoiceId,
		supplierchequeNumber,
		supplierchequeAmount
	)
	values
	(
		'".mysql_real_escape_string(to_YMD($_REQUEST['paidchequesDate']))."',
		'".mysql_real_escape_string($reportId)."',
		'".mysql_real_escape_string($_REQUEST['paidchequeNumber'])."',
		'".mysql_real_escape_string($_REQUEST['paidchequesAmount'])."'
	)";
//echo $queryCustomer;
mysql_query($queryCustomer,$conexion);
//echo$queryCustomer;
//$job = mysql_insert_id();	

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:reportSupplier.php".$additionalParams);
?>
