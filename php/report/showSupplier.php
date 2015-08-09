<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$invoiceId = $_GET['i'];

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
	supplierinvoice
WHERE
	supplierInvoiceId=".$invoiceId;
	
$reg=mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($reg);

$queryInfo2="
SELECT
	*
FROM
	supplier
WHERE
	supplierId=".$projectInfo['supplierId'];
	
$reg2=mysql_query($queryInfo2,$conexion);
$projectInfo2 = mysql_fetch_assoc($reg2);

$queryInfo3="
SELECT
	vendorId, vendorName 
FROM
	vendor
WHERE
	vendorId=".$projectInfo2['vendorId'];
	
$reg3=mysql_query($queryInfo3,$conexion);
$projectInfo3 = mysql_fetch_assoc($reg3);

$queryInfo4="
SELECT
	addressId, addressLine1 as supplierAddress, addressCity as supplierCity, addressState as supplierState, addressZip as supplierZip
FROM
	address
WHERE
	addressId=".$projectInfo2['addressId'];
	
$reg4=mysql_query($queryInfo4,$conexion);
$projectInfo4 = mysql_fetch_assoc($reg4);

$queryInfo5="
select MAX(ticketDate) as maxT, supplierInvoiceId 
	from supplierinvoiceticket 
join ticket using (ticketId) 
Where 
	supplierInvoiceId=".$invoiceId. "
	group by supplierInvoiceId";
	
$reg5=mysql_query($queryInfo5,$conexion);
$projectInfo5 = mysql_fetch_assoc($reg5);

$queryInfo6="
select MIN(ticketDate) as minT, supplierInvoiceId 
	from supplierinvoiceticket 
	join ticket using (ticketId)
Where 
	supplierInvoiceId=".$invoiceId."
	group by supplierInvoiceId";
	
$reg6=mysql_query($queryInfo6,$conexion);
$projectInfo6 = mysql_fetch_assoc($reg6);

//$dueDate = date("m/d/Y",strtotime(date("m/d/Y", strtotime($projectInfo['invoiceEndDate']))  . " +". $projectInfo['termValue']." days"));

$queryInvoice="
SELECT 
	*
FROM
	supplierinvoiceticket
WHERE
	supplierInvoiceId=".$invoiceId."
	ORDER BY 
	ticketId
	";
//echo $queryInvoice;
$invoices = mysql_query($queryInvoice,$conexion);




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
		
	</table>
</td>
<td width="30%" align="center" >
	<img src='/trucking/img/logo2print.gif' width="140" height="100" />
</td>
<td width="30%" align="right" >
	<table class="invinfo">
		<caption>Invoice</caption>
		<tr>
			<td><? echo $projectInfo['supplierInvoiceNumber'];?></td>
		</tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table class="billinfo">
		<tr>
		<th width="90%">Bill From <?echo $projectInfo3['vendorName'];?></th>
		</tr>
		<tr><td><?echo $projectInfo2['supplierName'];?></td></tr>
		<tr><td><?echo $projectInfo4['supplierAddress'];?></td></tr>
		<tr><td><?echo $projectInfo4['supplierCity'].", ".$projectInfo4['supplierState']." ".$projectInfo4['supplierZip'];?></td></tr>
	</table>
</td>
<td>
</td>
<td>
	<table class="proinfo">
		<tr><th>Starting: </th><td><?echo to_MDY($projectInfo6['minT']);?></td></tr>
		<tr><th>Ending: </th><td><?echo to_MDY($projectInfo5['maxT']);?></td></tr>
	</table>
</td>
</tr>
</table>

<?
if($projectInfo['supplierInvoiceComment']!="")echo "
<br>
*".$projectInfo['supplierInvoiceComment']."
<br>";
?>
<br>

<table align="center" class="report" width="100%" cellspacing="0" >
<tr>
	<th width="10%" >Date</th>
	<th width="8%" >Truck</th>
	<th width="6%" >MFI</th>
	<th width="6%" >Ticket</th>
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
while($ticket=mysql_fetch_assoc($invoices))
{

$queryInvoice2="
SELECT 
	*
FROM
	ticket
WHERE
	ticketId=".$ticket['ticketId']."
	ORDER BY ticketDate asc";
//echo $queryInvoice;
$invoices2 = mysql_query($queryInvoice2,$conexion);
if(mysql_num_rows($invoices2)==0)continue;
$invoices2Info = mysql_fetch_assoc($invoices2);

$queryInvoice3="
SELECT 
	*
FROM
	item
WHERE
	itemId=".$invoices2Info['itemId'];

$invoices3 = mysql_query($queryInvoice3,$conexion);
$invoices3Info = mysql_fetch_assoc($invoices3);

$queryInvoice4="
SELECT 
	*
FROM
	material
WHERE
	materialId=".$invoices3Info['materialId'];

$invoices4 = mysql_query($queryInvoice4,$conexion);
$invoices4Info = mysql_fetch_assoc($invoices4);

$queryInvoice5="
SELECT 
	*
FROM
	project
WHERE
	projectId=".$invoices3Info['projectId'];

$invoices5 = mysql_query($queryInvoice5,$conexion);
$invoices5Info = mysql_fetch_assoc($invoices5);

$queryInvoice6="
SELECT 
	truckId, truckNumber, brokerPid
FROM
	truck
	Join broker using (brokerId)
WHERE
	truckId=".$invoices2Info['truckId'];

$invoices6 = mysql_query($queryInvoice6,$conexion);
$invoices6Info = mysql_fetch_assoc($invoices6);

	echo "<tr>";
		echo "<td>".to_MDY($invoices2Info['ticketDate'])."</td>";
		echo "<td>".$invoices6Info['brokerPid']."-".$invoices6Info['truckNumber']."</td>";
		echo "<td align=left >".$invoices2Info['ticketMfi']."</td>";
		echo "<td align=left >".$invoices2Info['ticketNumber']."</td>";
		echo "<td align=left>".$invoices4Info['materialName']."</td>";
		echo "<td align=left>".$invoices3Info['itemDisplayFrom']."</td>";
		echo "<td align=left>".$invoices3Info['itemDisplayTo']."</td>";
		echo "<td >".decimalPad($invoices2Info['ticketAmount']);echo"</td>";
		echo "<td align=right >".decimalPad($invoices3Info['itemMaterialPrice']);echo"</td>";
		echo "<td align=right >".decimalPad($invoices2Info['ticketAmount']*$invoices3Info['itemMaterialPrice']);echo"</td>";
	echo "</tr>";
	$total+=$invoices2Info['ticketAmount']*$invoices3Info['itemMaterialPrice'];
	$count++;
}
echo "<tr><td colspan='2' align=center> $count Tickets </td><td colspan='8'></td></tr>";
echo "<tr><td colspan='8'></td><th><span>Total</span></th><td align=right >".decimalPad($total);echo"</td></tr>";
?>
</table>
</form>

</body>
</html>

