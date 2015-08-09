<?
include("../conexion.php");
include("../commons.php");
include("../password.php");
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

if($_GET['fromDate']==''){$fromDate='0000-00-00';}
else{$fromDate=to_YMD(mysql_real_escape_string($_GET['fromDate']));}

if($_GET['toDate']==''){$toDate=date("Y-m-d");}
else{$toDate=to_YMD(mysql_real_escape_string($_GET['toDate']));}


	
						//sum(ticketBrokerAmount * itemBrokerCost) as invoiceTotal
//$reportTotal=$projectInfo6['suma'] * ($projectInfo['brokerPercentage']/100);

//mysql_close();
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
	<td><strong>From: <?echo to_MDY($fromDate);?></strong></td>
	<td><strong>To: <?echo to_MDY($toDate);?></strong></td>
	<td></td>
</tr>

</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >

<?php

$queryTotal = "
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
								reportId = ".$reportInfo['reportId']."
						";

$queryInvoices="
SELECT 
	brokerName,
	SUM(ticketBrokerAmount * itemBrokerCost) AS totalGross,
	SUM( (ticketBrokerAmount * itemBrokerCost) * ( if(item.itemDescription = 'TOLL', 100, broker.brokerPercentage)/100 ) ) as totalIncome
FROM 
	ticket
	JOIN item USING ( itemId )
	JOIN truck USING ( truckId )
	JOIN broker USING ( brokerId )
WHERE 
	ticketDate BETWEEN '".$fromDate."' AND '".$toDate."'
GROUP BY brokerId";

//echo $queryInvoices;
$totalGross=0;
$totalIncome=0;
$count=0;
$tbody = "";	
$invoices = mysql_query($queryInvoices,$conexion);					
while($invoice = mysql_fetch_assoc($invoices)){
						

		$tbody.= "<tr>";
			$tbody.= "<td>".$invoice['brokerName']."</td>";
			$tbody.= "<td align=right >$".decimalPad($invoice['totalGross']);$tbody.="</td>";
			$tbody.= "<td align=right >$".decimalPad($invoice['totalIncome']);$tbody.="</td>";
			$tbody.= "<td align=right >$".decimalPad($invoice['totalGross'] - $invoice['totalIncome']);$tbody.="</td>";
		
		$tbody.= "</tr>\n";
		$totalGross += $invoice['totalGross'];
		$totalIncome += $invoice['totalIncome'];
	}
$tbody.= "<tr><th><span>Total</span></th><td align=right ><strong>$".decimalPad($totalGross);$tbody.="</strong></td><td align=right ><strong>$".decimalPad($totalIncome);$tbody.="</strong></td><td align=right ><strong>$".decimalPad($totalGross - $totalIncome);$tbody.="</strong></td></tr>";


echo  "<tr><td></td><td align=right ><strong>$".decimalPad($totalGross);echo"</strong></td><td align=right ><strong>$".decimalPad($totalIncome);echo"</strong></td><td align=right ><strong>$".decimalPad($totalGross - $totalIncome);echo"</strong></td></tr>";

?>
<tr>
	<th width="25%" >Broker</th>
	<th width="25%" >Gross</th>
	<th width="25%" >Income</th>
	<th width="25%" >Profit</th>
</tr>
<?
echo $tbody;
?>
</table>

</form>

</body>
</html>

