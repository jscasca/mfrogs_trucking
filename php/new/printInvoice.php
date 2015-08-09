<?php
include("../commons.php");
include("../conexion.php");

require_once("../tcpdf/tcpdf.php");
require_once("../tcpdf/config/lang/eng.php");

$invoiceId = $_GET['i'];

$queryMfi="
SELECT
	*,
	CURDATE()
FROM
	mfiInfo
JOIN address using (addressId)
";
$frogsInfo=mysql_query($queryMfi,$conexion);
$mfiInfo = mysql_fetch_assoc($frogsInfo);

$queryInfo="
SELECT
	*
FROM
	invoice
JOIN project using (projectId)
JOIN (select addressId, addressLine1 as projectAddress, addressCity as projectCity, addressState as projectState from address) as pA using (addressId)
JOIN (
		SELECT
			*
		FROM
			customer
		JOIN term using (termId)
		JOIN (select addressId, addressLine1 as customerAddress, addressCity as customerCity, addressState as customerState, addressZip as customerZip from address) as cA using (addressId)
		 ) as C using (customerId)
WHERE
	invoiceId=".$invoiceId;
	
$reg=mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($reg);

$dueDate = 0 + $projectInfo['termValue'];

$queryInvoice="
SELECT 
	*
FROM
	invoice
JOIN invoiceTicket using (invoiceId)
JOIN ticket using (ticketId)
JOIN (
	SELECT 
		*
	FROM
		item
	JOIN material using (materialId)
	JOIN (select addressId as fromAddressId, addressLine1 as fromAddress from address) as fA using (fromAddressId)
	JOIN (select addressId as toAddressId, addressLine1 as toAddress from address) as tA using (toAddressId)
	) as I using(itemId)
JOIN (
	SELECT
		truckId,
		truckNumber,
		brokerPid
	FROM
		truck
	JOIN broker using(brokerId)
	) T using(truckId)
WHERE
	invoiceId=".$invoiceId;

$invoices = mysql_query($queryInvoice,$conexion);

$dueDate = date("m/d/Y",strtotime(date("m/d/Y", strtotime($projectInfo['invoiceEndDate']))  . " +". $projectInfo['termValue']." days"));
$date=to_MDY($mfiInfo['CURDATE()']);

$customerFax=showPhoneNumber($projectInfo['customerFax']);
$customerTel=showPhoneNumber($projectInfo['customerTel']);

$mfiTel=showPhoneNumber($mfiInfo['mfiTel']);
$mfiFax=showPhoneNumber($mfiInfo['mfiFax']);

$total=0;
$count=0;
$body="<tr>
	<th width='10%' >Date</th>
	<th width='7%' >Truck</th>
	<th width='13%' >Ticket #</th>
	<th width='14%' >Material</th>
	<th width='23%' >From</th>
	<th width='23%' >To</th>
	<th width='5%' >L/T/H</th>
	<th width='10%' >Cost</th>
	<th width='10%' >Amount</th>
</tr>";
while($ticket=mysql_fetch_assoc($invoices))
{
	$body.="<tr>";
		$body.= "<td>".to_MDY($ticket['ticketDate'])."</td>";
		$body.= "<td>".$ticket['brokerPid']."-".$ticket['truckNumber']."</td>";
		$body.= "<td align=left >".$ticket['ticketMfi'];if($ticket['ticketNumber']!="")$body.="/".$ticket['ticketNumber'];$body.="</td>";
		$body.= "<td align=left>".$ticket['materialName']."</td>";
		$body.= "<td align=left>".$ticket['fromAddress']."</td>";
		$body.= "<td align=left>".$ticket['toAddress']."</td>";
		$body.= "<td >";$body.=decimalPad($ticket['ticketAmount']);$body.="</td>";
		$body.= "<td align=right >";$body.=decimalPad($ticket['itemCustomerCost']);$body.="</td>";
		$body.= "<td align=right >";$body.=decimalPad($ticket['ticketAmount']*$ticket['itemCustomerCost']);$body.="</td>";
	$body.= "</tr>";
	$total+=$ticket['ticketAmount']*$ticket['itemCustomerCost'];
	$count++;
}
$body.= "<tr><td colspan='2' align=center> $count Tickets </td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
$body.= "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td colspan='7'></td><th><span>Total</span></th><td align=right >";$body.=decimalPad($total);$body.="</td></tr>";


mysql_close();

session_start();

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Martinez Frogs Inc');
$pdf->SetTitle('Invoice Example');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
	
table.report 
{text-align: center;
font-family: Verdana, Geneva, Arial, Helvetica, sans-serif ;
font-weight: normal;
font-size: 11px;
color: #fff;
width: '100%';
background-color: #666;
border: 0px;
border-collapse: collapse;
border-spacing: 0px;}

table.topt
{
width: '90%';
}

table.mainReport
{
	font-size: 24px;
	color: #fff;
	bacground-color: #666;	
	border: 0px;
	border-collapse: collapse;
	border-spacing: 0px;
}

table.mainReport td 
{background-color: #CCC;
color: #000;
padding: 4px;
}

table.mainReport th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: center;

font-size: 24px;
font-weight: bold;}

table.report td 
{background-color: #CCC;
color: #000;
padding: 4px;
border: 10px #fff solid;}

table.report th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: center;

font-size: 12px;
font-weight: bold;}

table.mfinfo caption{
font-size: 16px;
font-weight: bold;
}
table.mfinfo td{
font-size: 16px;
font-style: italic;
}
table.invinfo caption{
font-size: 20px;
font-weight: bold;
font-style: italic;
}

table.invinfo th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: center;
border-bottom: 2px #fff solid;
font-size: 27px;
font-weight: bold;}

table.billinfo th
{background-color: #666;
color: #fff;
padding: 4px;
width: "100%";
text-align: center;
border-bottom: 2px #fff solid;
font-size: 15px;
font-weight: bold;}

table.billinfo 
{
font-family: Verdana, Geneva, Arial, Helvetica, sans-serif ;
font-weight: normal;
font-size: 11px;
color: #000;
padding: 4px;
text-align: left;
border: 1px #fff solid;}

table.proinfo th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: center;
border-bottom: 2px #fff solid;
font-size: 15px;
font-weight: bold;}

</style>
<table>
<tr>
<td cwidth='205'>
<table class="invinfo" width='100%'>
		<tr><th>Martinez Frogs Inc.</th></tr>
		<tr><td width='177'>{$mfiInfo['addressLine1']}</td></tr>
		<tr><td>{$mfiInfo['addressCity']}, {$mfiInfo['addressState']}-{$mfiInfo['addressZip']}</td></tr>
		<tr><td>Ph # $mfiTel</td></tr>
		<tr><td>Fax # $mfiFax</td></tr>
	</table>
</td>
<td width='205'>
<table class="invinfo">
		<tr><th>Date</th><th>Invoice #</th></tr>
		<tr><td>$date</td><td>{$projectInfo['invoiceId']}</td></tr>
		<tr><th>Terms</th><th>Due Date</th></tr>
		<tr><td>{$projectInfo['termName']}</td><td>$dueDate</td></tr>
	</table>
</td>
</tr>
<tr>
<td width='65'>
<table class="invinfo">
		<tr><th>Bill To:</th><td></td></tr>
		<tr><td>{$projectInfo['customerName']}</td></tr>
		<tr><td>{$projectInfo['customerAddress']}</td></tr>
		<tr><td>{$projectInfo['customerCity']}, {$projectInfo['customerState']}-{$projectInfo['customerZip']}</td></tr>
		<tr><td>Ph # $customerTel</td></tr>
		<tr><td>Fax # $customerFax</td></tr>
	</table>
</td>
<td width='205'>
<table class="invinfo">
		<tr><th>Project Name:</th><td>{$projectInfo['projectName']}</td></tr>
		<tr><th>Project Address:</th><td>{$projectInfo['projectAddress']} {$projectInfo['projectCity']},{$projectInfo['projectState']}</td></tr>
	</table>
</td>
</tr>
</table>
<br />
<br />
<table align="center" class="mainReport" width="100%" cellspacing="0" >
$body
</table>
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_061.pdf', 'I');


?>
