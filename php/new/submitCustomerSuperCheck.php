<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

$chequeNumber = mysql_escape_string($_REQUEST['customerChequeNum']);
$chequeAmount = mysql_escape_string($_REQUEST['customerChequeAmount']);
$chequeDate = mysql_escape_string(to_YMD($_REQUEST['customerChequeDate']));

$paidFromCheque = 0;

$insertSuperCheck = "
INSERT INTO
	customer_super_check (
		customerId,
		customerSuperCheckNumber,
		customerSuperCheckAmount,
		customerSuperCheckDate,
		customerSuperCheckCreationDate,
		customerSuperCheckNote
	) values (
		".$_REQUEST['customerId'].",
		'".$chequeNumber."',
		'".$chequeAmount."',
		'".$chequeDate."',
		now(),
		'".mysql_escape_string($_REQUEST['customerChequeNote'])."'
	)
";
//echo $insertSuperCheck;
mysql_query($insertSuperCheck, $conexion);

$superChequeId = mysql_insert_id();
//$superChequeId = 0;

if($_REQUEST['hiddenInvoices']!=""){
	
	$invoiceInCheque = "
	INSERT INTO
		receiptcheques (
			receiptchequesDate,
			receiptchequeNumber,
			customerSuperCheckId,
			receiptchequesAmount,
			invoiceId
		) values 
	";
	$first = true;
	$invoices = explode("-",$_REQUEST['hiddenInvoices']);

	foreach($invoices as $invoicePair){
		
		$pair = explode(",",$invoicePair);
		
		if($first){$first = false;}
		else{ $invoiceInCheque .= ",";}
		
		$invoiceInCheque .= "('$chequeDate', '$chequeNumber', '$superChequeId', ".$pair[1].",".$pair[0].")";
		$paidFromCheque += $pair[1];
	}
	//echo $invoiceInCheque;
	mysql_query($invoiceInCheque,$conexion);
}

$credit = $chequeAmount - $paidFromCheque;

if($credit > 0){
	$insertCredit = "
		INSERT INTO
			customer_credit (
				customerSuperCheckId,
				customerCreditAmount
			) values (
				$superChequeId,
				$credit
			)
	";
	//echo $insertCredit;
	mysql_query($insertCredit, $conexion);
}

mysql_close($conexion);

header ("Location:newCustomer_Payment.php");

?>
