<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$nextInvoiceId=0;
$queryStatus="SHOW TABLE STATUS LIKE 'report'";
$status = mysql_query($queryStatus,$conexion);
$stat = mysql_fetch_assoc($status);
$nextInvoice = $stat['Auto_increment'];

if($_GET['startDate']==''){$startDate='0000-00-00';}
else{$startDate=to_YMD(mysql_real_escape_string($_GET['startDate']));}

if($_GET['endDate']==''){$endDate=date("Y-m-d");}
else{$endDate=to_YMD(mysql_real_escape_string($_GET['endDate']));}

$broker = $_GET['brokerId'];

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

$queryInfo="
SELECT
	*
FROM
	broker
WHERE
	brokerId=".$broker;

$reg=mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($reg);


$queryInfo2="
SELECT
*
from address
WHERE
	addressId=".$projectInfo['addressId'];
	
$reg2=mysql_query($queryInfo2,$conexion);
$projectInfo2 = mysql_fetch_assoc($reg2);

$queryInfo3="
SELECT
*
from term
WHERE
	termId=".$projectInfo['termId'];
	
$reg3=mysql_query($queryInfo3,$conexion);
$projectInfo3 = mysql_fetch_assoc($reg3);

$dueDate = date('Y-m-d',strtotime($endDate .'+ '.$projectInfo3['termValue'].' days'));


$queryInvoice="
SELECT 
	*
FROM
	ticket
	JOIN item using (itemId)
	JOIN material using (materialId)
	JOIN truck using (truckId)
	LEFT JOIN reportticket using (ticketId)
WHERE
	reportId IS NULL
	AND brokerId=".$broker."
	AND ticketDate BETWEEN '".$startDate."' AND '".$endDate."' 
ORDER BY
	ticketDate, ticketId
";
//echo $queryInvoice;
$invoices = mysql_query($queryInvoice,$conexion);

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
{background-color: #CCC;
color: #000;
padding: 4px;
border: 1px #fff solid;}

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
{background-color: #CCC;
color: #000;
padding: 4px;
border: 1px #fff solid;
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
		<caption>Subcontractors</caption>
		<tr>
			<td><?echo $projectInfo['brokerName']?></td>
		</tr>
		<tr>
			<td>
				<table class='insurance'>
					<tr>
						<th>Liability Ins Policy No:</th><td><?echo $projectInfo['brokerInsuranceLiability'];?></td>
						<th>Wc Ins Policy No:</th><td><?echo $projectInfo['brokerInsuranceWc'];?></td>
					</tr>
					<tr>
						<th>Liability Ins Expiration Date:</th><td><?echo to_MDY($projectInfo['brokerLbExpire']);?></td>
						<th>WC Ins Expiration Date:</th><td><?echo to_MDY($projectInfo['brokerWcExpire']);?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</td>
<td>
	<table class="dates">
		<tr>
			<th colspan='2'><strong>Dates</strong></th>
			<th><strong>Due Date</strong></th>
		</tr>
		<tr>
			<td><?echo to_MDY($startDate);?></td>
			<td><?echo to_MDY($endDate);?></td>
			<td><?echo to_MDY($dueDate);?></td>
		</tr>
	</table>
</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >
<tr>
	<th width="8%" >Date</th>
	<th width="13%" >Customer</th>
	<th width="6%" >Truck</th>
	<th width="9%" >Ticket #</th>
	<th width="16%" >From</th>
	<th width="16%" >To</th>
	<th width="5%" >L/T/H</th>
	<th width="6%" >Rate</th>
	<th width="8%" >Amount</th>
	<th width="5%" >%</th>
	<th width="8%" >Total</th>
</tr>

<?php
$total=0;
$count=0;
$subtotal = 0;
while($ticket=mysql_fetch_assoc($invoices)){

$queryInvoice3="
select customerName, projectId 
from project JOIN customer using (customerId)
WHERE
	projectId=".$ticket['projectId'];

$invoices3 = mysql_query($queryInvoice3,$conexion);
$invoicesInfo3=mysql_fetch_assoc($invoices3);

	echo "<tr>";
		echo "<td>".to_MDY($ticket['ticketDate'])."</td>";
		echo "<td>".$invoicesInfo3['customerName']."</td>";
		echo "<td>".$projectInfo['brokerPid']."-".$ticket['truckNumber']."</td>";
		echo "<td align=left >".$ticket['ticketMfi'];if($ticket['ticketNumber']!="")echo"/".$ticket['ticketNumber'];echo"</td>";
		echo "<td align=left>".$ticket['itemDisplayFrom']."</td>";
		echo "<td align=left>".$ticket['itemDisplayTo']."</td>";
		echo "<td >".decimalPad($ticket['ticketBrokerAmount']);echo"</td>";
		echo "<td align=right >".decimalPad($ticket['itemBrokerCost']);echo"</td>";
		echo "<td align=right >".decimalPad($ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']);echo"</td>";
		
		if(strpos(strtolower($ticket['itemDescription']),"toll")===FALSE){
			echo "<td>".decimalPad($projectInfo['brokerPercentage'])."%</td>";
			echo "<td align=right >".decimalPad($ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']*($projectInfo['brokerPercentage']/100));echo"</td>";
			$total+=$ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']*($projectInfo['brokerPercentage']/100);
		}else{
			echo "<td>".decimalPad('100')."%</td>";
			echo "<td align=right >".decimalPad($ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']);echo"</td>";
			$total+=$ticket['ticketBrokerAmount']*$ticket['itemBrokerCost'];
		}
		
	echo "</tr>";
	$subtotal+=$ticket['ticketBrokerAmount']*$ticket['itemBrokerCost'];
	
	$count++;
}
echo "<tr><td colspan='2' align=center> $count Tickets </td><td colspan='5'></td><th>Subtotal</th><td align=right >".decimalPad($subtotal);echo"</td><td></td><td align=right >".decimalPad($total);echo"</td></tr>";
echo "<tr><td colspan='9'></td><th><span>Total</span></th><td align=right ><strong>".decimalPad($total);echo"</strong></td></tr>";
?>
</table>
</form>

</body>
</html>

