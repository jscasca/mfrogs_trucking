<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);
$multiple = false;

$queryTickets = "SELECT 
		* 
	FROM 
		invoice
		JOIN project using (projectId)
		JOIN customer using (customerId)
		";

if($_GET['startDate']!=""){
	if(!$multiple){
		$queryTickets .= " WHERE ";
		$multiple = true;
	}else
		$queryTickets .= " AND ";
	$queryTickets .= "ticketDate >= '".to_YMD($_GET['startDate'])."'";
}

if($_GET['endDate']!=""){
	if(!$multiple){
		$queryTickets .= " WHERE ";
		$multiple = true;
	}else
		$queryTickets .= " AND ";
	$queryTickets .= "ticketDate <= '".to_YMD($_GET['endDate'])."'";
}

if($_GET['customerId']!=0){
	if(!$multiple){
		$queryTickets .= " WHERE ";
		$multiple = true;
	}else
		$queryTickets .= " AND ";
	$queryTickets .= "customerId = ".$_GET['customerId'];
}

if($_GET['projectId']!=0){
	if(!$multiple){
		$queryTickets .= " WHERE ";
		$multiple = true;
	}else
		$queryTickets .= " AND ";
	$queryTickets .= " invoice.projectId = ".$_GET['projectId'];
}
	
$queryTickets .= "	
	ORDER BY
		invoiceId desc
";

if($_GET['listed']!='all'){
	$queryTickets .= "limit ".$_GET['listed'];
}

//echo $queryTickets;
	
//echo $queryTickets;	
$colorFlag=true;
$terms = mysql_query($queryTickets,$conexion);
$numTerms = mysql_num_rows($terms);
$tbody = "<tbody>";
while($term = mysql_fetch_assoc($terms)){
	
	//get invoice balance
	$invoiceTotal = getInvoiceTotal($term['invoiceId'], $conexion);
	$invoicePaid = getInvoicePaid($term['invoiceId'], $conexion);
	$balance = decimalPad($invoiceTotal - $invoicePaid);
	$cheques = mysql_fetch_assoc(mysql_query("select count(*) as paidCheques from receiptcheques WHERE invoiceId = ".$term['invoiceId'], $conexion));
	
	if($cheques['paidCheques'] > 0 && $balance <= 0)continue;
	
	//if already paid continue
	if($balance <= 0)/*continue;*/$paid = true;
	else $paid = false;
	//
	//TODO: try with mysql query
	//if($_GET['vendorId']!=0 && $_GET['vendorId']!=$term['vendorId'])continue;
	//if($_GET['supplierId']!=0 && $_GET['supplierId']!=$term['supplierId'])continue;
	/*if($term['supplierInvoiceId'] == null){
		
	}else{
		$supplierInvoiceQuery = "select * from supplierinvoice where supplierInvoiceId = ".$term['supplierInvoiceId'];
		$supplierInvoices = mysql_fetch_assoc(mysql_query($supplierInvoiceQuery));
		$term['supplierInvoiceNumber'] = $supplierInvoices['supplierInvoiceNumber'];
	}*/
	
	if($colorFlag)
	{
		$tbody.= "<tr id='invoice".$term['ticketId']."' >";
		!$colorFlag;
	}
	else
	{
		$tbody.= "<tr id='invoice".$term['ticketId']."' class='bg'>";
		!$colorFlag;
	}
	
	$tbody.="
		<td>".$term['invoiceId']."</td>
		<td>".$term['projectName']."<input type='hidden' value=\"".$term['projectName']."\" id='projectName".$term['invoiceId']."' /></td>
		<td>".to_MDY($term['invoiceDate'])."<input type='hidden' value='".to_MDY($term['invoiceDate'])."' id='invoiceDate".$term['invoiceId']."'/></td>
		<td>".$invoiceTotal."<input type='hidden' value='".$invoiceTotal."' id='total".$term['invoiceId']."'/></td>
		<td>".$invoicePaid."<input type='hidden' value='".$invoicePaid."' id='paid".$term['invoiceId']."'/></td>
		<td>".$balance."<input type='hidden' value='".$balance."' id='balance".$term['invoiceId']."'/></td>
		<td><input type='text' value='".$balance."' class='account' id='toPay".$term['invoiceId']."' /></td>
		<td>".($paid ? "": "<img src='/trucking/img/23.png' width='20px' height='20px' class='add-invoice' invoiceId='".$term['invoiceId']."' />")."</td>
	";
	/*$tbody.= "
		<td class='first style2'>".$term['projectId']."<input type='hidden' value=\"".$term['projectName']."\" id='projectName".$term['ticketId']."'/></td>
		<td class='first style2'>".$term['customerName']."</td>
		<td class='first style2'>".$term['itemNumber']."</td>
		<td class='first style2'>".to_MDY($term['ticketDate'])."<input type='hidden' value='".to_MDY($term['ticketDate'])."' id='ticketDate".$term['ticketId']."'/></td>
		<td class='first style2'>".$term['materialName']."<input type='hidden' value='".$term['materialName']."' id='materialName".$term['ticketId']."'/></td>
		<td class='first style2'>".$term['itemDisplayFrom']."</td>
		<td class='first style2'>".$term['ticketMfi']."<input type='hidden' value='".$term['ticketMfi']."' id='ticketMfi".$term['ticketId']."'/></td>
		<td class='first style2'>".$term['ticketNumber']."<input type='hidden' value='".$term['ticketNumber']."' id='ticketNumber".$term['ticketId']."'/></td>
		<td class='number'>$".decimalPad($term['itemMaterialPrice'])."<input type='hidden' value='".decimalPad(decimalPad($term['itemMaterialPrice'])*decimalPad($term['ticketBrokerAmount']))."' id='price".$term['ticketId']."'/></td>
		<td class='number'>".decimalPad($term['ticketAmount'])."</td>
		<td class='number'>$".decimalPad($term['itemCustomerCost'])."</td>
		<td class='number'>".decimalPad($term['ticketBrokerAmount'])."</td>
		<td class='number'>$".decimalPad($term['itemBrokerCost'])."</td>
		<td>".($term['supplierInvoiceNumber']==null?"<img src='/trucking/img/23.png' class='add-ticket' width='20px'>":"<label class='rm-ticket'>".$term['supplierInvoiceNumber']."</label>")."</td>
			";*/
	$tbody.= "</tr>";
	/*$tbody.= "
		<td class='first style2'>".$project['projectName']."</td>
		<td class='first style2'>".$item['itemNumber']."</td>
		<td class='first style2'>".to_MDY($term['ticketDate'])."</td>
		<td class='first style2'>".$material['materialName']."</td>
		<td class='first style2'>".$item['itemDisplayFrom']."</td>
		<td class='first style2'>".$term['ticketMfi']."</td>
		<td class='first style2'>".$term['ticketNumber']."</td>
		<td class='number'>$".decimalPad($item['itemMaterialPrice'])."</td>
		<td class='number'>".decimalPad($term['ticketAmount'])."</td>
		<td class='number'>$".decimalPad($item['itemCustomerCost'])."</td>
		<td class='number'>".decimalPad($term['ticketBrokerAmount'])."</td>
		<td class='number'>$".decimalPad($item['itemBrokerCost'])."</td>
		<td>".($term['supplierInvoiceNumber']==0?"<img src='/trucking/img/23.png' class='add-ticket' width='20px'>":"<label class='rm-ticket'>".$term['supplierInvoiceNumber']."</label>")."</td>
			";
	$tbody.= "</tr>";*/
}
$tbody.= "</tbody>";

$jsondata['table'] = $tbody;
$jsondata['query'] = $queryTickets;
	echo json_encode($jsondata);


mysql_close();


?>

