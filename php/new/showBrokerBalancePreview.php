<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$broker = $_GET['brokerId'];
$driver = $_GET['driverId'];
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

$brokerInfo = mysql_fetch_assoc(mysql_query("select * from broker where brokerId = $broker",$conexion));

$queryReports="
SELECT
	*
FROM
	report
	JOIN broker using (brokerId)
	LEFT JOIN driver ON (driver.driverId = report.reportType)
	JOIN term ON (term.termId = if(driverId is null, broker.termId, driver.termId) )
WHERE 
	report.brokerId=".$broker."
	AND ( reportStartDate between '$fromDate' AND '$toDate' OR reportEndDate between '$fromDate' AND '$toDate')
	";
	
if($driver != 0){ 
	$queryReports.= " AND reportType = ".$driver;
	$driverInfo = mysql_fetch_assoc(mysql_query("select * from driver where driverId = $driver",$conexion));
}

$queryReports.=" ORDER BY reportEndDate desc";

//get the reports

//get the associated cheques from paidcheques

//get the total from tickets

//echo $queryInvoice;
$reports = mysql_query($queryReports,$conexion);



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
	<table class="subcontractor">
		<caption><? if($projectInfo['reportType']==0)echo "";else echo"Drivers";?></caption>
		<tr>
			<td><?echo "<strong>".$brokerInfo['brokerName']."</strong> ".($driver!=0 ? " for ".$driverInfo['driverLastName'].", ".$driverInfo['driverFirstName']: "");?></td>
		</tr>
		<tr>
			<td>
				<table class='insurance'>
					<tr>
						<th>Liability Ins Policy No:</th><td><?echo $brokerInfo['brokerInsuranceLiability'];?></td>
						<th>Wc Ins Policy No:</th><td><?echo $brokerInfo['brokerInsuranceWc'];?></td>
					</tr>
					<tr>
						<th>Liability Ins Expiration Date:</th><td><?echo to_MDY($brokerInfo['brokerLbExpire']);?></td>
						<th>WC Ins Expiration Date:</th><td><?echo to_MDY($brokerInfo['brokerWcExpire']);?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</td>
<td>
	<table class="dates">
		<tr>
			<th><strong>Date Issued: </strong></th>
			<td><?echo to_MDY(date("Y-m-d"));?></td>
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

$tableHolder = "";

while($reportInfo=mysql_fetch_assoc($reports)){
	//For each report
	
	//get the number of checks
	$cheques = mysql_query("select * from paidcheques where reportId = ".$reportInfo['reportId'],$conexion);
	
	$payments = mysql_query("SELECT sum(paidchequesAmount) as paid from paidcheques where reportId = ".$reportInfo['reportId'],$conexion);
	$mysqlPaid = mysql_fetch_assoc($payments);
	$totalPaid = decimalPad($mysqlPaid['paid'] == null ? 0 : $mysqlPaid['paid']);
	$paidTotal += $totalPaid;
	
	/*
	 * $queryTotal = "
							SELECT
								SUM( (ticketBrokerAmount * itemBrokerCost) * (if(item.itemDescription = 'TOLL', 100, if(driver.driverId is null, broker.brokerPercentage, driver.driverPercentage ) ) )/100 ) as totalReported
							FROM
								reportticket
								JOIN report using (reportId)
								JOIN ticket using (ticketId)
								JOIN item using (itemId)
								JOIN broker using (brokerId)
								LEFT JOIN driver on (driver.driverId = report.reportType)
							WHERE
								reportId = ".$invoice['reportId']."
						";
	 */
	
	/*$totalPerReportQuery = "
	SELECT
		SUM( (ticketBrokerAmount * itemBrokerCost) * (if(item.itemDescription = 'TOLL', 100, if(driver.driverId is null, broker.brokerPercentage, driver.driverPercentage ) ) )/100 ) as reportTotal
	FROM
		reportticket
		JOIN report using (reportId)
		JOIN ticket using (ticketId)
		JOIN item using (itemId)
		JOIN broker using (brokerId)
		LEFT JOIN driver on (driver.driverId = report.reportType)
	WHERE reportId = ".$reportInfo['reportId'];
	
	$reportTotalInfo = mysql_fetch_assoc(mysql_query($totalPerReportQuery, $conexion));*/
	
	$reportTotal = getReportTotal($reportInfo['reportId'], $conexion);
	//$reportTotal = decimalPad($reportTotalInfo['reportTotal']) ;
	
	$globalTotal += $reportTotal;
	
	$countCheques = mysql_num_rows($cheques);
	if($countCheques <2){
		$rows = 1;
		$rowspan = "";
	}else{
		$rows = $countCheques;
		$rowspan = " rowspan='$countCheques'";
	}
	
	$dueDate = (date('m/d/Y', strtotime('+'.$reportInfo['termValue'].' days', strtotime($reportInfo['reportEndDate']))));
	
	$tableHolder.= "<tr>";
	$tableHolder.= "<td $rowspan>".$reportInfo['reportId']."</td>";
	$tableHolder.= "<td $rowspan>".to_MDY($reportInfo['reportStartDate'])."</td>";
	$tableHolder.= "<td $rowspan>".to_MDY($reportInfo['reportEndDate'])."</td>";
	$tableHolder.= "<td $rowspan>".$dueDate."</td>";
	$tableHolder.= "<td $rowspan>".($reportInfo['driverId']!=null?$reportInfo['driverLastName'].", ".$reportInfo['driverFirstName']:"N/A")."</td>";
	$tableHolder.= "<td $rowspan>".decimalPad($reportInfo['driverId']==null?$brokerInfo['brokerPercentage']:$reportInfo['driverPercentage'])."%</td>";
	
	$tableHolder.= "<td $rowspan>".decimalPad($reportTotal)."</td>";
	$tableHolder.= "<td $rowspan>".decimalPad($reportTotal - $totalPaid)."</td>";
	$tableHolder.= "<td $rowspan>".decimalPad($totalPaid)."</td>";
	$first = true;
	
	if($countCheques == 0){
		$tableHolder.= "<td >--</td>";
		$tableHolder.= "<td >--</td>";
		$tableHolder.= "<td >--</td>";
	}
	
	if($countCheques == 1){
		$rowChequeInfo = mysql_fetch_assoc($cheques);
		$tableHolder.= "<td >".decimalPad($rowChequeInfo['paidchequesAmount'])."</td>";
		$tableHolder.= "<td >".$rowChequeInfo['paidchequeNumber']."</td>";
		$tableHolder.= "<td >".to_MDY($rowChequeInfo['paidchequesDate'])."</td>";
	}else if($countCheques > 1){
		
		while($rowChequeInfo = mysql_fetch_assoc($cheques)){
			if($first){
				$first = false;
			}else{
				$tableHolder.= "</tr><tr>\n";
			}
			$tableHolder.= "<td >".decimalPad($rowChequeInfo['paidchequesAmount'])."</td>";
			$tableHolder.= "<td >".$rowChequeInfo['paidchequeNumber']."</td>";
			$tableHolder.= "<td >".to_MDY($rowChequeInfo['paidchequesDate'])."</td>";
		}
	}
	
	
	//if($countCheques = 0) echo "";
	
	//$chequesInfo = mysql_fetch_assoc($cheques);
	//get the total
	
	$tableHolder.= "</tr>\n";
}
echo "<tr><td colspan='6'></td><td>".decimalPad($globalTotal)."</td><td>".decimalPad($globalTotal - $paidTotal)."</td><td>".decimalPad($paidTotal)."</td><td colspan='3'></td></tr>";
?>
<tr>
	<th width="8%" >Report</th>
	<th colspan='2' >Date Range</th>
	<th width="8%" >Due Date</th>
	<th colspan='2' >Driver</th>
	<th width="16%" >Bill Total</th>
	<th width="8%" >To Pay</th>
	<th width="16%" >Paid</th>
	<th width="8%" >Amount</th>
	<th width="5%" >Cheque Number</th>
	<th width="6%" >Date</th>
</tr>
<?
echo $tableHolder;
?>
</table>

</form>

</body>
</html>

