<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);
if(isset($_GET['supplierId']))
if(isset($_GET['ticketId']))
if(isset($_GET['invoiceNum'])){

$supplierInvoice = $_GET['invoiceNum'];
$supplierId = $_GET['supplierId'];
$querySupplierInvoice = "select * from supplierinvoice where supplierId= $supplierId and supplierInvoiceNumber = '$supplierInvoice'";
//echo $querySupplierInvoice;
$supplierInvoices = mysql_query($querySupplierInvoice,$conexion);
if(mysql_num_rows($supplierInvoices)>0){
	//Already exists -> only add the tickets
	$invoice = mysql_fetch_assoc($supplierInvoices);
	$invoiceId = $invoice['supplierInvoiceId'];
}else{
	//Create new invoice and add the tickets
	$createInvoice ="
	INSERT INTO
		supplierinvoice
	(supplierId,supplierInvoiceNumber)
	values
	($supplierId,'$supplierInvoice')";
	mysql_query($createInvoice);
	$invoiceId = mysql_insert_id();
}
$ticketId = $_GET['ticketId'];

//Add the ticket to the invoice
$insertTicket = "insert into supplierinvoiceticket (supplierInvoiceId,ticketId) values ($invoiceId,$ticketId)";
mysql_query($insertTicket,$conexion);

$jsondata['invoiceId'] = $supplierInvoice;
$jsondata['ticketId'] = $ticketId;
	echo json_encode($jsondata);

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();
}

?>

