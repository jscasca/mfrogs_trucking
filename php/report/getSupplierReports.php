<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
/*
ELECT 

 * */
$queryTickets = "
	SELECT
		*
	FROM
		supplierinvoice
	JOIN supplier using (supplierId)
	JOIN vendor using (vendorId)";
$first = true;

if($_GET['vendor']!=0){
	if($_GET['supplier']!=0){
		if($first){
			$queryTickets.=" WHERE ";
			$first=false;
		}else{
			$queryTickets.=" AND ";
		}
		$queryTickets.= " supplierId=".$_GET['supplier'];
	}else{
		if($first){
			$queryTickets.=" WHERE ";
			$first=false;
		}else{
			$queryTickets.=" AND ";
		}
		$queryTickets.= " vendorId=".$_GET['vendor'];
	}
}
if($_GET['invoiceNum']!=""){
	if($first){
			$queryTickets.=" WHERE ";
			$first=false;
		}else{
			$queryTickets.=" AND ";
		}
		$queryTickets.= " supplierInvoiceNumber like '%".$_GET['invoiceNum']."%'";
}
	
$queryTickets.= "
	ORDER BY
		supplierInvoiceDate DESC
	";
$terms = mysql_query($queryTickets,$conexion);
$numTerms = mysql_num_rows($terms);
$tbody= "</body>";
if($numTerms>0)
{
	$tbody.="
		<tbody>";
	$colorFlag=true;
	while($invoice = mysql_fetch_assoc($terms))
	{
		$paidTotal = "
			SELECT
				SUM(supplierchequeAmount) as totalPaid,
				COUNT(*) as number
			FROM
				suppliercheque
			WHERE
				supplierInvoiceId = ".$invoice['supplierInvoiceId']."
		";
		
		$paidInfo = mysql_fetch_assoc(mysql_query($paidTotal, $conexion));
					
		$paidTotal = decimalPad($paidInfo['totalPaid'] == null ? 0 : $paidInfo['totalPaid']);
		$chequeTotal = $paidInfo['number'] == null ? 0 : $paidInfo['number'];
		$reportTotal = decimalPad($invoice['supplierInvoiceAmount'] );
		
		if($paidTotal == null || $paidTotal <= 0 || $chequeTotal == 0 ) $paid = 'Unpaid';
		if($paidTotal != null && $paidTotal >= $reportTotal && $chequeTotal != 0) $paid = 'Paid';
		if($paidTotal != null && $paidTotal > 0 && $paidTotal < $reportTotal) $paid = 'Warning';
		if($paidTotal != null && $paidTotal > $reportTotal) $paid = 'Overpaid';
		
		if($colorFlag){$tbody.= "<tr class='even".$paid."' id='invoice".$invoice['supplierInvoiceId']."'>";}
		else{$tbody.= "<tr class='odd".$paid."' id='invoice".$invoice['supplierInvoiceId']."'>";}
		$colorFlag=!$colorFlag;
		
		$tbody.= "<td>".$invoice['supplierName']."</td>";
		$tbody.= "<td>".$invoice['supplierInvoiceNumber']."</td>";
		$tbody.= "<td>".to_MDY($invoice['supplierInvoiceDate'])."</td>";
		$tbody.= "<td>".decimalPad($reportTotal)."</td>";
		$tbody.= "<td>".decimalPad($reportTotal - $paidTotal)."</td>";
		$tbody.= "<td>".decimalPad($paidTotal)."</td>";
		
		if($paid == 'Unpaid' || $paid == 'Warning') $tbody.= "<td class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' /></td>";
		else $tbody.= "<td></td>";
		
		if($paid == 'Unpaid')$tbody.= "<td><img src='/trucking/img/118.png' width='20' height='20' class='deletable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' supplierInvoiceNumber='".$invoice['supplierInvoiceNumber']."' /></td>";
		else $tbody.= "<td><img src='/trucking/img/2.png' width='24' height='22' class='managable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' /></td>";
		
		$tbody.= "</tr>";
	}
}
$tbody.= "</tbody>";

$jsondata['table']=$tbody;
$jsondata['query']=$queryTickets;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

