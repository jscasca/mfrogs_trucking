<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

$insertSupplierInvoice = "
INSERT INTO
	supplierinvoice (
		supplierId,
		supplierInvoiceNumber,
		supplierInvoiceAmount,
		supplierInvoiceComment,
		supplierInvoiceDate,
		supplierInvoiceCreationDate
	) values (
		".$_REQUEST['supplierId'].",
		'".mysql_escape_string($_REQUEST['invoiceNum'])."',
		'".mysql_escape_string($_REQUEST['invoiceAmount'])."',
		'".mysql_escape_string($_REQUEST['invoiceComment'])."',
		'".mysql_escape_string(to_YMD($_REQUEST['invoiceDate']))."',
		now()
	)
";
mysql_query($insertSupplierInvoice, $conexion);

$added = array();

$invoiceId = mysql_insert_id();
$ticketPerInvoice = "
INSERT INTO
	supplierinvoiceticket (
		supplierInvoiceId,
		ticketId
	) values 
";
$first = true;
$tickets = explode("-",$_REQUEST['hiddenTickets']);

foreach($tickets as $ticketId){
	if(isset($added[$ticketId])){continue;}
	else{$added[$ticketId] = '1';}
	if($first){$first = false;}
	else{ $ticketPerInvoice .= ",";}
	
	$ticketPerInvoice .= "($invoiceId, $ticketId)";
}
//echo $ticketPerInvoice;
mysql_query($ticketPerInvoice,$conexion);


mysql_close($conexion);

header ("Location:newSupplier_Invoice.php");

?>
