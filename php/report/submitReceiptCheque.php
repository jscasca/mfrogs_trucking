<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$invoiceId = $_REQUEST['invoiceId'];

$customerId = $_GET['customerId'];
$projectId = $_GET['projectId'];
$afterDate = $_GET['afterDate'];
$beforeDate = $_GET['beforeDate'];
$week = $_GET['week'];
$paid = $_GET['paid'];
$invoiceNum = $_GET['invoiceNum'];

$additionalParams = "?customerId=$customerId&projectId=$projectId&afterDate=$afterDate&beforeDate=$beforeDate&beforeEndDate=$beforeEndDate&week$week=&paid=$paid&invoiceNum=$invoiceNum";

$date='now()';

$queryCustomer="
insert into
	receiptcheques
	(
		receiptchequesDate,
		invoiceId,
		receiptchequeNumber,
		receiptchequesAmount
	)
	values
	(
		'".mysql_real_escape_string(to_YMD($_REQUEST['receiptchequesDate']))."',
		'".mysql_real_escape_string($invoiceId)."',
		'".mysql_real_escape_string($_REQUEST['receiptchequeNumber'])."',
		'".mysql_real_escape_string($_REQUEST['receiptchequesAmount'])."'
	)";

mysql_query($queryCustomer,$conexion);
//echo$queryCustomer;
$job = mysql_insert_id();	

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:reportInvoice.php".$additionalParams);
?>
