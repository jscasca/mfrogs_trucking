<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

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

mysql_close();
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

table.report th.customer{
	background-color: #444;
	color: #fff;
	padding: 4px;
	text-align: center;

	font-size: 12px;
	font-weight: bold;
}

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
<td>
	<table class="dates">
		<tr>
			<th><strong>Date Issued: </strong></th>
			<td><?echo date('m/d/Y h:i:s a');?></td>
			<td></td>
		</tr>
	</table>
</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >

<?php
$globalTotal = 0;
$toPayTotal = 0;
$paidTotal = 0;

$totalAmount = 0;
$totalBalance = 0;

$tableHolder = "";
$now = time();

$customers = mysql_query("select * from customer JOIN term using (termId) ".($_GET['customerId']!=0?" WHERE customerId = ".$_GET['customerId']:""), $conexion);
while($customer = mysql_fetch_assoc($customers)){
	$shouldAdd = false;
	$tmpHolder = "";
	$customerTotalAmount = 0;
	$customerTotalBalance = 0;
	
	$lastProjectAmount = 0;
	$lastProjectBalance = 0;
	
	
	$termValue = $customer['termValue'];
	
	$invoicesQuery = "
		SELECT
			invoiceId,
			invoiceDate,
			project.projectId,
			projectName,
			SUM(ticketAmount * itemCustomerCost) as totalAmount
		FROM
			project
			JOIN invoice using (projectId)
			JOIN invoiceticket using (invoiceId)
			JOIN ticket using (ticketId)
			JOIN item using (itemId)
		WHERE
			customerId = ".$customer['customerId']."
		GROUP BY
			invoiceId
		ORDER BY
			projectId,
			invoiceId
	";
	$invoices = mysql_query($invoicesQuery, $conexion);
	$projectsIssued = array();
	if(mysql_num_rows($invoices) > 0){
		$tmpHolder = "<tr><th class='customer' colspan='7'>".$customer['customerName']."</th></tr>";
		
		while($invoice = mysql_fetch_assoc($invoices)){
			$paidInfo = mysql_fetch_assoc(mysql_query("SELECT COALESCE(SUM(receiptchequesAmount),0) as totalPaid, count(*) as totalCheques FROM receiptcheques WHERE invoiceId = ".$invoice['invoiceId'],$conexion));
			
			
			$invoiceAmount = decimalPad($invoice['totalAmount']);
			$invoicePaid = decimalPad($paidInfo['totalPaid']);
			$invoiceBalance = decimalFill(decimalPad($invoiceAmount - $invoicePaid));
			if($invoiceBalance == 0) continue;
			else $shouldAdd = true;
			
			if(!isset($projectsIssued[$invoice['projectId']])){
				if($lastProjectAmount!=0){
					$tmpHolder.="<tr><td colspan='5'></td><td align='right'>".decimalPad($lastProjectAmount)."</td><td align='right'>".decimalPad($lastProjectBalance)."</td></tr>";
					$lastProjectAmount = 0;
					$lastProjectBalance = 0;
				}
				
				$tmpHolder.="<tr><td></td><th class='project' colspan='6' >".$invoice['projectName']."</th></tr>";
				$projectsIssued[$invoice['projectId']] =1;
			}
			$startingDate = strtotime($invoice['invoiceDate']);
			$dateDiff = $now - $startingDate;
			$daysOff = floor($dateDiff/(60*60*24));
			
			$dueDate = date('Y-m-d', strtotime($invoice['invoiceDate']. " +".$termValue." days"));
			
			$customerTotalAmount += $invoiceAmount;
			$lastProjectAmount += $invoiceAmount;
			$totalAmount += $invoiceAmount;
			
			$customerTotalBalance += $invoiceBalance;
			$lastProjectBalance += $invoiceBalance;
			$totalBalance += $invoiceBalance;
			
			$tmpHolder.="<tr>";
				$tmpHolder.= "<td></td>";
				$tmpHolder.= "<td>".$invoice['invoiceId']."</td>";
				$tmpHolder.= "<td>".to_MDY($invoice['invoiceDate'])."</td>";
				$tmpHolder.= "<td>".$daysOff."</td>";
				$tmpHolder.= "<td>".to_MDY($dueDate)."</td>";
				$tmpHolder.= "<td align='right'>".$invoiceAmount."</td>";
				$tmpHolder.= "<td align='right'>".$invoiceBalance."</td>";
			$tmpHolder.="</tr>\n";
		}
		$tmpHolder.="<tr><td colspan='5'></td><td align='right'>".decimalPad($lastProjectAmount)."</td><td align='right'>".decimalPad($lastProjectBalance)."</td></tr>";
		$tmpHolder.="<tr><td colspan='5'></td><th class='total' align='right'>".decimalPad($customerTotalAmount)."</th><th class='total' align='right'>".decimalPad($customerTotalBalance)."</th></tr>";
		if($shouldAdd) $tableHolder.= $tmpHolder;
	}
}

echo "<tr><td colspan='5'></td><td align='right'>".decimalPad($totalAmount)."</td><td align='right'>".decimalPad($totalBalance)."</td></tr>";
?>
<tr>
	<th></th>
	<th>Invoice Number</th>
	<th>Date</th>
	<th>Aging</th>
	<th>Due Date</th>
	<th>Amount</th>
	<th>Balance</th>
</tr>
<?
echo $tableHolder;
echo "<tr><td colspan='5'></td><td align='right'>".decimalPad($totalAmount)."</td><td align='right'>".decimalPad($totalBalance)."</td></tr>";
?>
</table>

</form>

</body>
</html>

