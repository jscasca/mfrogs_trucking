<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_GET);

//p_array($_SESSION);

$invoiceId = $_REQUEST['invoiceId'];
$amount = $_REQUEST['amount'];
$customerSuperCheckId = $_REQUEST['superCheckId'];

$superCheckInfo = mysql_fetch_assoc(mysql_query("select * from customer_super_check where customerSuperCheckId = $customerSuperCheckId", $conexion));

$invoiceInfo = mysql_fetch_assoc(mysql_query("select * from invoice JOIN project USING (projectId) where invoiceId = $invoiceId", $conexion));

$insertQuery = "insert into receiptcheques (receiptchequesDate, invoiceId, receiptchequesAmount, receiptchequeNumber, customerSuperCheckId)
	values ('".$superCheckInfo['customerSuperCheckDate']."', $invoiceId, $amount, '".$superCheckInfo['customerSuperCheckNumber']."', $customerSuperCheckId)";
	mysql_query($insertQuery, $conexion);
$receiptId = mysql_insert_id();
$newBalance = updateCredit($customerSuperCheckId, $conexion);

$total = getInvoiceTotal($invoiceInfo['invoiceId'], $conexion);
$paid = getInvoicePaid($invoiceInfo['invoiceId'], $conexion);

$newRow = "<tr id='receipt$receiptId' receiptId='".$receiptId."'>";
	$newRow.="<td>".$invoiceInfo['invoiceId']."</td>";
	$newRow.="<td>".$invoiceInfo['projectName']."</td>";
	$newRow.="<td>".decimalPad($invoiceInfo['invoiceDate'])."</td>";
	$newRow.="<td>".decimalPad($total)."</td>";
	$newRow.="<td>".decimalPad($paid - $amount)."</td>";
	$newRow.="<td>".decimalPad($total - ($paid - $amount))."</td>";
	$newRow.="<td class='sumable' >".decimalPad($amount)."</td>";
	$newRow.="<td>".decimalPad($total - $paid)."</td>";
	$newRow.="<td><img src='/trucking/img/118.png' width='20px' height='20px' class='removableInvoice' /></td>";
$newRow.="</tr>";

$jsonData['amount'] = $amount;
$jsonData['newRow'] = $newRow;

echo json_encode($jsonData);

mysql_close();


?>
