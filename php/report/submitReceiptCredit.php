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

$superCheck = $_REQUEST['superCheckId'];
$creditAmount = $_REQUEST['creditAmount'];

$additionalParams = "?customerId=$customerId&projectId=$projectId&afterDate=$afterDate&beforeDate=$beforeDate&beforeEndDate=$beforeEndDate&week$week=&paid=$paid&invoiceNum=$invoiceNum";


$superCheckInfo = mysql_fetch_assoc(mysql_query("select * from customer_super_check where customerSuperCheckId = $superCheck",$conexion));

$queryCustomer="
insert into
	receiptcheques
	(
		receiptchequesDate,
		invoiceId,
		receiptchequeNumber,
		receiptchequesAmount,
		customerSuperCheckId
	)
	values
	(
		'".$superCheckInfo['customerSuperCheckDate']."',
		'".mysql_real_escape_string($invoiceId)."',
		'".$superCheckInfo['customerSuperCheckNumber']."',
		'".$creditAmount."',
		$superCheck
	)";

mysql_query($queryCustomer,$conexion);

updateCredit($superCheck, $conexion);
//echo$queryCustomer;

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
