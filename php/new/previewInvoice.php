<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$nextInvoiceId=0;
$queryStatus="SHOW TABLE STATUS LIKE 'invoice'";
$status = mysql_query($queryStatus,$conexion);
$stat = mysql_fetch_assoc($status);
$nextInvoice = $stat['Auto_increment'];

$optionalClause = "";

if(isset($_GET['materialId'])) { $optionalClause = " AND materialId = ".$_GET['materialId']; }
if(isset($_GET['itemId'])) { $optionalClause = " AND itemId = ".$_GET['itemId']; }

if($_GET['startDate']==''){$startDate='0000-00-00';}
else{$startDate=to_YMD(mysql_real_escape_string($_GET['startDate']));}

if($_GET['endDate']==''){$endDate=date("Y-m-d");}
else{$endDate=to_YMD(mysql_real_escape_string($_GET['endDate']));}

//echo $_GET['comment'];

$project = $_GET['projectId'];

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
	project
WHERE
	projectId=".$project;

$projectReg = mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($projectReg);

$queryInfo2="
SELECT addressId, addressLine1 as projectAddress, addressCity as projectCity, addressState as projectState
FROM
	address
WHERE
	addressId=".$projectInfo['addressId'];

$reg2=mysql_query($queryInfo2,$conexion);
$projectInfo2 = mysql_fetch_assoc($reg2);


$queryInfo3="
SELECT *
FROM
	customer
JOIN term using (termId)
WHERE
	customerId=".$projectInfo['customerId'];
	
$reg3=mysql_query($queryInfo3,$conexion);
$projectInfo3 = mysql_fetch_assoc($reg3);

$queryInfo4="
SELECT addressId, addressLine1 as customerAddress, addressCity as customerCity, addressState as customerState, addressZip as customerZip
FROM
	address
WHERE
	addressId=".$projectInfo3['addressId'];
	
$reg4=mysql_query($queryInfo4,$conexion);
$projectInfo4 = mysql_fetch_assoc($reg4);

$dueDate = date("m/d/Y",strtotime(date("m/d/Y", strtotime($endDate))  . " +". $projectInfo3['termValue']." days"));

//$dueDate = 0 + $projectInfo['termValue'];

$queryTickets="
SELECT * 
FROM ticket
JOIN item
USING ( itemId ) 
JOIN project
USING ( projectId ) 
WHERE projectId =".$project." 
	$optionalClause
	AND ticketDate BETWEEN '".mysql_real_escape_string($startDate)."' and '".mysql_real_escape_string($endDate)."'
ORDER BY
	ticketDate, ticketId
";
$ticketsreg = mysql_query($queryTickets,$conexion);
$arraytickets = array();


while($tickets=mysql_fetch_assoc($ticketsreg))
{
$queryInvoiceTicket="select ticketId from invoiceticket where ticketId=".$tickets['ticketId'];
$InvoiceTicket = mysql_query($queryInvoiceTicket,$conexion);
$InvoiceTicketInfo = mysql_fetch_assoc($InvoiceTicket);

$numInvoice=mysql_num_rows($InvoiceTicket);

if($numInvoice==0)
{
	$arraytickets[]=$tickets['ticketId'];
}
}

$result=count($arraytickets);

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

td.invoiceId{
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
		<caption>Invoice</caption>
		<tr><th>Date</th><th>Invoice #</th></tr>
		<tr><td><? echo to_MDY($mfiInfo['CURDATE()']);?></td><td class="invoiceId"><? echo $nextInvoice;?></td></tr>
		<tr><th>Terms</th><th>Due Date</th></tr>
		<tr><td><? echo $projectInfo3['termName'];?></td><td><? echo $dueDate;?></td></tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table class="billinfo">
		<th width="90%">Bill To</th>
		<tr><td width='177' ><? echo $projectInfo3['customerName']; ?></td></tr>
		<tr><td><? echo $projectInfo4['customerAddress']; ?></td></tr>
		<tr><td><? echo $projectInfo4['customerCity'].", ".$projectInfo4['customerState']." ".$projectInfo4['customerZip']; ?></td></tr>
		<tr><td><? echo "Ph # ".showPhoneNumber($projectInfo3['customerTel']); ?></td></tr>
		<tr><td><? echo "Fax # ".showPhoneNumber($projectInfo3['customerFax']); ?></td></tr>
	</table>
</td>
<td>
</td>
<td>
	<table class="proinfo">
		<tr><th>Project</th><td><? echo $projectInfo['projectName']; ?></td></tr>
		<tr><th>Address</th><td><? echo $projectInfo2['projectAddress']." ".$projectInfo2['projectCity'].", ".$projectInfo2['projectState']; ?></th></tr>
	</table>
</td>
</tr>
</table>

<?
if ($_GET['comment']!="") 
echo "
<br>
*{$_GET['comment']}
<br>";
?>
<br>

<table align="center" class="report" width="100%" cellspacing="0" >
<tr>
	<th width="10%" >Date</th>
	<th width="7%" >Truck</th>
	<th width="13%" >Ticket #</th>
	<th width="14%" >Material</th>
	<th width="23%" >From</th>
	<th width="23%" >To</th>
	<th width="5%" >L/T/H</th>
	<th width="10%" >Cost</th>
	<th width="10%" >Amount</th>
</tr>

<?php
$total=0;
$count=0;


foreach ($arraytickets as $ticketId)
{
$queryTicket="
SELECT * 
FROM ticket
WHERE ticketId=".$ticketId;
$ticketreg = mysql_query($queryTicket,$conexion);
$ticket = mysql_fetch_assoc($ticketreg);

$queryItem="
SELECT 
	*
FROM
	item
WHERE
	itemId=".$ticket['itemId'];
	
$itemreg = mysql_query($queryItem,$conexion);
if(mysql_num_rows($itemreg)==0)continue;
$item = mysql_fetch_assoc($itemreg);

$queryMaterial="
SELECT 
	*
FROM
	material
WHERE
	materialId=".$item['materialId'];

$materialreg = mysql_query($queryMaterial,$conexion);
$material = mysql_fetch_assoc($materialreg);

$queryTruck="
SELECT 
	truckId, truckNumber, brokerId
FROM
	truck
WHERE
	truckId=".$ticket['truckId'];

$truckreg = mysql_query($queryTruck,$conexion);
$truck = mysql_fetch_assoc($truckreg);

$queryBroker="
SELECT 
	brokerPid
FROM
	broker
WHERE
	brokerId=".$truck['brokerId'];

$brokerreg = mysql_query($queryBroker,$conexion);
$broker = mysql_fetch_assoc($brokerreg);

	echo "<tr>";
		echo "<td>".to_MDY($ticket['ticketDate'])."</td>";
		echo "<td>".$broker['brokerPid']."-".$truck['truckNumber']."</td>";
		echo "<td align=left >".$ticket['ticketMfi'];if($ticket['ticketNumber']!="")echo"/".$ticket['ticketNumber'];echo"</td>";
		echo "<td align=left>".$material['materialName']."</td>";
		echo "<td align=left>".$item['itemDisplayFrom']."</td>";
		echo "<td align=left>".$item['itemDisplayTo']."</td>";
		echo "<td >";printf("%01.2f",$ticket['ticketAmount']);echo"</td>";
		echo "<td align=right >";printf("%01.2f",$item['itemCustomerCost']);echo"</td>";
		echo "<td align=right >";printf("%01.2f",$ticket['ticketAmount']*$item['itemCustomerCost']);echo"</td>";
	echo "</tr>";
	$total+=$ticket['ticketAmount']*$item['itemCustomerCost'];
	$count++;
}
echo "<tr><td colspan='2' align=center> $count Tickets </td><td colspan='7'></td></tr>";
echo "<tr><td colspan='7'></td><th><span>Total</span></th><td align=right >";printf("%01.2f",$total);echo"</td></tr>";
?>
</table>
</form>

</body>
</html>

