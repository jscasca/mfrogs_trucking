<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$reportId = $_REQUEST['reportId'];

$brokerId = $_GET['brokerId'];
$driverId = $_GET['driverId'];
$afterDate = $_GET['afterDate'];
$beforeDate = $_GET['beforeDate'];
$beforeEndDate = $_GET['beforeEndDate'];
$week = $_GET['week'];
$paid = $_GET['paid'];

$additionalParams = "?brokerId=$brokerId&driverId=$driverId&afterDate=$afterDate&beforeDate=$beforeDate&beforeEndDate=$beforeEndDate&week$week=&paid=$paid";

$date='now()';

$queryCustomer="
insert into
	paidcheques
	(
		paidchequesDate,
		reportId,
		paidchequeNumber,
		paidchequesAmount
	)
	values
	(
		'".mysql_real_escape_string(to_YMD($_REQUEST['paidchequesDate']))."',
		'".mysql_real_escape_string($reportId)."',
		'".mysql_real_escape_string($_REQUEST['paidchequeNumber'])."',
		'".mysql_real_escape_string($_REQUEST['paidchequesAmount'])."'
	)";

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

header ("Location:reportBroker [Invoices].php".$additionalParams);
?>
