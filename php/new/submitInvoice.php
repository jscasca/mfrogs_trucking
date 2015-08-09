<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);
$optionalClause = "";
if($_REQUEST['materialId'] != 0 ){$optionalClause = "AND materialId = ".$_REQUEST['materialId'];}
if($_REQUEST['itemId'] != 0 ){$optionalClause = "AND itemId = ".$_REQUEST['itemId'];}

$queryInvoice="
insert into
	invoice
	(
		invoiceDate,
		projectId,
		invoiceStartDate,
		invoiceEndDate,
		invoiceComment
	)
	values
	(
		CURDATE(),
		'".mysql_real_escape_string($_REQUEST['projectId'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['invoiceStartDate']))."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['invoiceEndDate']))."',
		'".mysql_real_escape_string($_REQUEST['invoiceComment'])."'
	)";

//echo $queryBroker;
mysql_query($queryInvoice,$conexion);
$invoice = mysql_insert_id();

$queryTickets="
INSERT INTO
	invoiceticket
(
	ticketId,
	invoiceId
)
	SELECT 
	ticketId,
	".$invoice."
FROM
	ticket
JOIN item using (itemId)
WHERE
	ticketId NOT IN (select ticketId from invoiceticket) 
	$optionalClause
	AND ticketDate BETWEEN '".mysql_real_escape_string(to_YMD($_REQUEST['invoiceStartDate']))."' and '".mysql_real_escape_string(to_YMD($_REQUEST['invoiceEndDate']))."' and
	projectId=".$_REQUEST['projectId']."

";

mysql_query($queryTickets,$conexion);

mysql_close($conexion);

header ("Location:newInvoice.php");

?>
