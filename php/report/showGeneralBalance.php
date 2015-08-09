<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

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
		<caption>General Broker Balance</caption>
		<tr><th>Date Issued</th><td><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table class="billinfo">
		<th colspan='2' width="90%">Balance Information</th>
		<tr><td width='177' ><? echo ""; ?></td></tr>
		<tr><td><strong>From:</strong></td><td><? echo to_MDY($fromDate); ?></td></tr>
		<tr><td><strong>To:</strong></td><td><? echo to_MDY($toDate); ?></td></tr>
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

$tableHolder = "";
$ticketTotaled = 0;
$reportedTotaled = 0;
$paidTotaled = 0;

$ticketsProfitQuery = "
	SELECT
		broker.brokerId,
		broker.brokerName,
		broker.brokerPercentage,
		SUM(ticketBrokerAmount * itemBrokerCost) as totalIncome,
		COUNT(*) as totalTickets
	FROM
		ticket
		JOIN item using (itemId)
		JOIN truck using (truckId)
		JOIN broker using (brokerId)
	WHERE
		ticketDate BETWEEN '$fromDate' AND '$toDate'
	GROUP BY
		brokerName
	ORDER BY
		brokerName
";

$ticketsBalance = mysql_query($ticketsProfitQuery,$conexion);
while($brokerTicketInfo = mysql_fetch_assoc($ticketsBalance)){
	
	$ticketsReportedQuery = "
		SELECT
			SUM(ticketBrokerAmount * itemBrokerCost) as totalReported,
			COUNT(*) as reportedTickets
		FROM
			report
			JOIN reportticket using (reportId)
			JOIN ticket using (ticketId)
			JOIN item using (itemId)
		WHERE
			brokerId = ".$brokerTicketInfo['brokerId']."
			AND ( reportStartDate between '$fromDate' AND '$toDate' OR reportEndDate between '$fromDate' AND '$toDate')
	";
	$reportedInfo = mysql_fetch_assoc(mysql_query($ticketsReportedQuery,$conexion));
	
	$reportsPaidQuery = "
		SELECT
			SUM(paidchequesAmount) as totalPaid,
			COUNT(*) as chequesPaid
		FROM
			report
			JOIN paidcheques using (reportId)
		WHERE
			brokerId = ".$brokerTicketInfo['brokerId']."
			AND ( reportStartDate between '$fromDate' AND '$toDate' OR reportEndDate between '$fromDate' AND '$toDate')
	";
	$paidInfo = mysql_fetch_assoc(mysql_query($reportsPaidQuery,$conexion));
	
	$brokerIncome = $brokerTicketInfo['totalIncome'] * $brokerTicketInfo['brokerPercentage']/100;
	$brokerReported = $reportedInfo['totalReported'] * $brokerTicketInfo['brokerPercentage']/100;
	$brokerPaid = $paidInfo['totalPaid'];
	
	$tableHolder.= "<tr>";
	$tableHolder.= "<td>".$brokerTicketInfo['brokerName']."</td>";
	$tableHolder.= "<td>".decimalPad($brokerTicketInfo['brokerPercentage'])."%</td>";
	$tableHolder.= "<td>".decimalPad($brokerIncome)."</td>";
	$tableHolder.= "<td>(".$brokerTicketInfo['totalTickets'].")</td>";
	$tableHolder.= "<td>".decimalPad($brokerReported)."</td>";
	$tableHolder.= "<td>(".($brokerTicketInfo['totalTickets'] - $reportedInfo['reportedTickets']).")</td>";
	$tableHolder.= "<td>".decimalPad($brokerPaid)."</td>";
	$tableHolder.= "<td>(".$paidInfo['chequesPaid'].")</td>";
	$tableHolder.= "</tr>";
	
	$ticketTotaled += $brokerIncome;
	$reportedTotaled += $brokerReported;
	$paidTotaled += $brokerPaid;
}

//echo $ticketsProfitQuery;
?>
	<tr>
		<td colspan='2'></td>
		<td><? echo decimalPad($ticketTotaled);?></td><td></td>
		<td><? echo decimalPad($reportedTotaled);?></td><td></td>
		<td><? echo decimalPad($paidTotaled);?></td><td></td>
	</tr>
	<tr>
		<th colspan='2'>Broker</th>
		<th >Income</th><th>Total tickets</th>
		<th >In report</th><th>Not reported</th>
		<th >Paid</th><th># cheques</th>
	</tr>

<?php

echo $tableHolder;
?>
</table>
</form>

</body>
</html>

