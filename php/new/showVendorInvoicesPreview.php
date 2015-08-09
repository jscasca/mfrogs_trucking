<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$vendor = $_GET['vendorId'];
$supplier = $_GET['supplierId'];
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];

if($_GET['fromDate']==''){$fromDate='0000-00-00';}
else{$fromDate=to_YMD(mysql_real_escape_string($_GET['fromDate']));}

if($_GET['toDate']==''){$toDate=date("Y-m-d");}
else{$toDate=to_YMD(mysql_real_escape_string($_GET['toDate']));}

$queryMfi="
SELECT
	*,
	CURDATE()
FROM
	mfiinfo
JOIN address using (addressId)
";
$frogsInfo=mysql_query($queryMfi,$conexion);
$mfiInfo = mysql_fetch_assoc($frogsInfo);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Form LPC-662</title>
<script language="javascript" type="text/javascript">

			
function imprimir()
{
  var Obj = document.getElementById("desaparece");
  Obj.style.visibility = 'hidden';
  window.print();
}


</script>
<style type="text/css">
body {
	font-size:12px;
	font-family:"Courier New", Courier, monospace;
}
</style>
</head>
<body>
<form id="form1" name="form1" method="post" action="">

<style type="text/css">
	
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

table.report td 
{background-color: #fff;
color: #000;
padding: 4px;
border: 1px #000 solid;}

table.report th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: center;

font-size: 12px;
font-weight: bold;}

table.insurance th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: left;
font-size: 12px;
font-weight: bold;}

table.insurance td 
{background-color: #fff;
color: #000;
padding: 4px;
border: 1px #000 solid;
font-size: 13px;
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
font-size: 15px;
font-weight: bold;}

table.subcontractor caption
{font-size: 17px;
	}

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

table.dates td
{
	padding: 3px;
text-align: center;
	}

</style>

<table class="topt" align="center" >
<tr>
<td width="30%" align="left" >
	<table class="invinfo" width='100%'>
		<caption>Martinez Frogs Inc.</caption>
		<tr><td width='177'><?echo$mfiInfo['addressLine1'];?></td></tr>
		<tr><td><?echo$mfiInfo['addressCity'].", ".$mfiInfo['addressState'].". ".$mfiInfo['addressZip'];?></td></tr>
		
	</table>
</td>
<td width="30%" align="center" >
	<img src='/trucking/img/logo2print.gif' width="140" height="100" />
</td>
<td width="30%" align="right" >
	<table class="invinfo">
		<tr><td><? echo "Ph # ".showPhoneNumber($mfiInfo['mfiTel']); ?></td></tr>
		<tr><td><? echo "Fax # ".showPhoneNumber($mfiInfo['mfiFax']); ?></td></tr>
	</table>
</td>
</tr>
<tr>
	<td colspan='3'><hr></td>
</tr>
<tr>
<td colspan='2'>
	
</td>
<td align='right'>
	<table class="dates">
		<tr>
			<th><strong>Date Issued: </strong></th>
			<td><?echo to_MDY(date("Y-m-d"));?></td>
			<td></td>
		</tr>
		<tr>
			<th>From:</th>
			<td><?echo to_MDY($fromDate);?></td>
			<td></td>
		</tr>
		<tr>
			<th>To:</th>
			<td><?echo to_MDY($toDate);?></td>
			<td></td>
		</tr>
	</table>
</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >

<?php
$totalAmount = 0;
$toPayTotal = 0;
$paidTotal = 0;

$tableHolder = "";

$reportQuery = "
	SELECT
		*
	FROM
		supplierinvoice
		JOIN supplier using (supplierId)
	WHERE
		supplierInvoiceDate BETWEEN '$fromDate' AND '$toDate'
		AND vendorId = $vendor
		".($supplier != 0 ? " AND supplierId = $supplier" : "")."
	ORDER BY
		supplierInvoiceDate desc
";
//echo $reportQuery;
$reports = mysql_query($reportQuery,$conexion);

while($reportInfo=mysql_fetch_assoc($reports)){
	//For each report
	$paidAmount = "
		SELECT
			SUM(supplierchequeAmount) as totalPaid
		FROM
			suppliercheque
		WHERE
			supplierInvoiceId = ".$reportInfo['supplierInvoiceId']."
	";
	$paidInfo = mysql_fetch_assoc(mysql_query($paidAmount, $conexion));
	
	$invAmount = decimalPad($reportInfo['supplierInvoiceAmount']);
	$invPaid = decimalPad($paidInfo['totalPaid']);
	$invToPay = decimalPad($invAmount - $invPaid);
	
	$tableHolder.= "<tr>";
	$tableHolder.= "<td>".$reportInfo['supplierName']."</td>";
	$tableHolder.= "<td>".$reportInfo['supplierInvoiceNumber']."</td>";
	$tableHolder.= "<td>".to_MDY($reportInfo['supplierInvoiceDate'])."</td>";
	$tableHolder.= "<td align='right'>".$invAmount."</td>";
	$tableHolder.= "<td align='right'>".$invPaid."</td>";
	$tableHolder.= "<td align='right'>".$invToPay."</td>";
	$totalAmount += $invAmount;
	$paidTotal += $invPaid;
	$toPayTotal += $invToPay;
	$tableHolder.= "</tr>";
}

$tableHolder.= "<tr><td colspan='2'></td><th>Total</th><td align='right'>".decimalPad($totalAmount)."</td><td align='right'>".decimalPad($paidTotal)."</td><td align='right'>".decimalPad($toPayTotal)."</td></tr>";
echo "<tr><td colspan='3'></td><td align='right'>".decimalPad($totalAmount)."</td><td align='right'>".decimalPad($paidTotal)."</td><td align='right'>".decimalPad($toPayTotal)."</td></tr>";
?>
<tr>
	<th>Supplier</th>
	<th>Invoice #</th>
	<th>Date</th>
	<th>Amount</th>
	<th>Paid</th>
	<th>To pay</th>
</tr>
<?
echo $tableHolder;
mysql_close();
?>
</table>

</form>

</body>
</html>

