<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$projectId = $_GET['i'];



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

$projectQuery="
SELECT
	*
FROM
	project
	join address using (addressId)
WHERE
	projectId=$projectId
";

$projectReg = mysql_query($projectQuery,$conexion);
$projectInfo = mysql_fetch_assoc($projectReg);


mysql_close();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Invoice</title>
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
{
background-color: #fff;
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

td.invoiceTd{
	text-align: right;
}

</style>

<table class="topt" align="center" >
<tr>
<td width="30%" align="left" >
	<table class="invinfo" width='100%'>
		<caption>Martinez Frogs Inc.</caption>
		<tr><td width='177'><?echo$mfiInfo['addressLine1'];?></td></tr>
		<tr><td><?echo$mfiInfo['addressCity'].", ".$mfiInfo['addressState'].". ".$mfiInfo['addressZip'];?></td></tr>
		<tr><td><? echo "Ph # ".showPhoneNumber($mfiInfo['mfiTel']); ?></td></tr>
		<tr><td><? echo "Fax # ".showPhoneNumber($mfiInfo['mfiFax']); ?></td></tr>
	</table>
</td>

<td width="30%" align="center" >
	<img src='/trucking/img/logo2print.gif' width="140" height="100" />
</td>

<td width="30%" align="right" >
	<table class="invinfo">
		<caption>Project Profit Report</caption>
		<tr><th>Date</th><td><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table class="billinfo">
		<th width="90%">Project Info</th>
		<tr><td width='177' ><? echo $projectInfo['projectName']; ?></td></tr>
		<tr><td><? echo $projectInfo['addressLine1']; ?></td></tr>
		<tr><td><? echo $projectInfo['addressCity'].", ".$projectInfo['addressState']." ".$projectInfo['addressZip']; ?></td></tr>
	</table>
</td>
<td>
</td>
<td>

</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >

<?php
$total=0;
$count=0;
$tableBody = "";
$invoicesQuery = "select * from invoice where projectId = $projectId";
$invoicesReg = mysql_query($invoicesQuery,$conexion);
if(mysql_num_rows($invoicesReg)>0){
	$totalIncome = 0;
	$totalBrokerExpense = 0;
	$totalMaterialExpense = 0;
	
	while($invoice = mysql_fetch_assoc($invoicesReg)){
		$invoiceIncome = 0;
		$invoiceBrokerExpense = 0;
		$invoiceMaterialExpense = 0;
		
		$invoiceId =  $invoice['invoiceId'];
		
		$ticketsQuery = "select * from invoiceticket where invoiceId=$invoiceId";
		$ticketsReg = mysql_query($ticketsQuery,$conexion);
		if(mysql_num_rows($ticketsReg)>0){
			while($invTicket = mysql_fetch_assoc($ticketsReg)){
				$ticketId = $invTicket['ticketId'];
				$ticketQuery = "select * from ticket join item using (itemId) where ticketId = $ticketId";
				$ticketReg = mysql_query($ticketQuery,$conexion);
				
				$ticketInfo = mysql_fetch_assoc($ticketReg);
				$invoiceIncome += $ticketInfo['ticketAmount'] * $ticketInfo['itemCustomerCost'];
				$invoiceBrokerExpense += $ticketInfo['ticketAmount'] * $ticketInfo['itemBrokerCost'];
				$invoiceMaterialExpense += $ticketInfo['ticketAmount'] * $ticketInfo['itemMaterialPrice']; 
			}
		}
		$invoiceTotal = $invoiceIncome - ($invoiceBrokerExpense + $invoiceMaterialExpense);
		$count++;
		$total+=$invoiceTotal;
		
		$tableBody.= "<tr>
			<td>$invoiceId</td>
			<td align=right >".decimalPad($invoiceIncome)."</td>
			<td align=right >".decimalPad($invoiceBrokerExpense)."</td>
			<td align=right >".decimalPad($invoiceMaterialExpense)."</td>
			<td align=right >".decimalPad($invoiceTotal)."</td>
		</tr>";
		
		$totalIncome += $invoiceIncome;
		$totalBrokerExpense += $invoiceBrokerExpense;
		$totalMaterialExpense += $invoiceMaterialExpense;
	}
}
echo "<tr>
	<td></td>
	<th>Total Income per customer</th>
	<th>Total Broker Expense</th>
	<th>Total Material Expense</th>
	<th>Total Gross Profit</th>
</tr>";
echo "<tr>
	<td></td>
	<td align=right >".decimalPad($totalIncome)."</td>
	<td align=right >".decimalPad($totalBrokerExpense)."</td>
	<td align=right >".decimalPad($totalMaterialExpense)."</td>
	<td align=right >".decimalPad($total)."</td>
</tr>";

echo "<tr><td colspan='5'></td></tr>";
?>
<tr>
	<th  >Invoice #</th>
	<th  >Gross Income</th>
	<th  >Broker Expenses</th>
	<th  >Material Expenses</th>
	<th  >Total</th>
</tr>
<?php

echo $tableBody;
echo "<tr>
	<th>Totals</th>
	<td align=right >".decimalPad($totalIncome)."</td>
	<td align=right >".decimalPad($totalBrokerExpense)."</td>
	<td align=right >".decimalPad($totalMaterialExpense)."</td>
	<td align=right >".decimalPad($total)."</td>
</tr>";
echo "<tr><td colspan='1' align=center> $count Invoices </td><td colspan='4'></td></tr>";
echo "<tr><td colspan='3'></td><th><span>Total</span></th><td align=right >".decimalPad($total);echo"</td></tr>";
?>
</table>
</form>

</body>
</html>

