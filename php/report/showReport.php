<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$reportId = $_GET['i'];

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
	report
WHERE
	reportId=".$reportId;

//echo $queryInfo;
$reg=mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($reg);

$queryInfo2="
SELECT
	*
FROM
	broker
WHERE
	brokerId=".$projectInfo['brokerId'];

$reg2=mysql_query($queryInfo2,$conexion);
$projectInfo2 = mysql_fetch_assoc($reg2);

$percentage = $projectInfo2['brokerPercentage']/100;
$driversName = "";

$terms = mysql_fetch_assoc(mysql_query("select * from term where termId=".$projectInfo2['termId'],$conexion));

$termDays = $terms['termValue'];

if($projectInfo['reportType']!=0){
$queryInfo3="
SELECT
	*
FROM
	driver
WHERE
	driverId=".$projectInfo['reportType'];
//echo $queryInfo3;
$reg3=mysql_query($queryInfo3,$conexion);
$projectInfo3 = mysql_fetch_assoc($reg3);
	$percentage = $projectInfo3['driverPercentage']/100;
	$driversName = ": ".$projectInfo3['driverLastName'].", ".$projectInfo3['driverFirstName'];
$terms = mysql_fetch_assoc(mysql_query("select * from term where termId=".$projectInfo3['termId'],$conexion));

$termDays = $terms['termValue'];	

}



$queryInfo4="
SELECT
	*
FROM
	reportdue
Where
	reportId=".$reportId;

$reg4=mysql_query($queryInfo4,$conexion);
$projectInfo4 = mysql_fetch_assoc($reg4);

//$dueDate = date('Y-m-d',strtotime($endDate .'+ '.$projectInfo['termValue'].' days'));
/*
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
*/
$queryInvoice="
SELECT
	*
FROM
	report

WHERE 
	reportId=".$reportId;

//echo $queryInvoice;
$invoices = mysql_query($queryInvoice,$conexion);

$queryInvoice2="
SELECT
	*
FROM
	reportticket
JOIN ticket using (ticketId)

WHERE 
	reportId=".$reportId."
	Order by ticketDate";

//echo $queryInvoice;
$invoices2 = mysql_query($queryInvoice2,$conexion);


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

.hide_and_print{display:none;}
@media print {
	.show_no_print{display:none;}
	.hide_and_print{display:block;}
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
			<td><h1><?echo $projectInfo2['brokerName'].$driversName;?></h1></td>
		</tr>
		<tr>
			<td>
				<table class='insurance'>
					<tr>
						<th>Liability Ins Policy No:</th><td><?echo $projectInfo2['brokerInsuranceLiability'];?></td>
						<th>Wc Ins Policy No:</th><td><?echo $projectInfo2['brokerInsuranceWc'];?></td>
					</tr>
					<tr>
						<th>Liability Ins Expiration Date:</th><td><?echo to_MDY($projectInfo2['brokerLbExpire']);?></td>
						<th>WC Ins Expiration Date:</th><td><?echo to_MDY($projectInfo2['brokerWcExpire']);?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</td>
<td>
	<table class="dates">
		<tr>
			<th>Week:</th>
			<td></td>
			<td class='right'><?echo getDateWeek($projectInfo['reportStartDate']);?></td>
		</tr>
		<tr>
			<th>From:</th>
			<td></td>
			<td class='right'><?echo to_MDY($projectInfo['reportStartDate']);?></td>
		</tr>
		<tr>
			<th>To:</th>
			<td></td>
			<td class='right'><?echo to_MDY($projectInfo['reportEndDate']);?></td>
		</tr>
		<tr class="bordermark">
			<th><strong>Due Date</strong></th>
			<td> </td>
			<td class='right'><?echo date('m/d/Y', strtotime('+'.$termDays.' days', strtotime($projectInfo['reportEndDate'])));?></td>
		</tr>
		<tr>
			<th>Report:</th>
			<td></td>
			<td><? echo $projectInfo['reportId']; ?></td>
		</tr>
	</table>
</td>
</tr>
</table>

<br>

<div class="show_no_print">
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
	<? if($projectInfo['reportType']==0){?>
		<th width="5%" >%</th>
		<th width="8%" >Total</th>
		<?}?>
</tr>

<?php

$projectsInAct = array();

$total=0;
$count=0;
$subtotal=0;
$hiddenTable = "";
	while($ticket=mysql_fetch_assoc($invoices2))
	{

	$queryInvoice3="
SELECT 
	*
FROM
	ticket
WHERE
	ticketId=".$ticket['ticketId']."
	
	ORDER BY ticketDate
	";
	
$invoices3 = mysql_query($queryInvoice3,$conexion);
$invoices3Info = mysql_fetch_assoc($invoices3);


$queryInvoice4="
SELECT 
	*
FROM
	item
WHERE
	itemId=".$invoices3Info['itemId'];

$invoices4 = mysql_query($queryInvoice4,$conexion);
if(mysql_num_rows($invoices4)==0)continue;
$invoices4Info = mysql_fetch_assoc($invoices4);

$queryInvoice5="
SELECT 
	*
FROM
	material
WHERE
	materialId=".$invoices4Info['materialId'];

$invoices5 = mysql_query($queryInvoice5,$conexion);
$invoices5Info = mysql_fetch_assoc($invoices5);

$queryInvoice6="
SELECT 
	truckId, truckNumber
FROM
	truck
WHERE
	truckId=".$invoices3Info['truckId']."
	ORDER BY truckNumber
	";

$invoices6 = mysql_query($queryInvoice6,$conexion);
$invoices6Info = mysql_fetch_assoc($invoices6);

$queryInvoice7="
SELECT 
	*
FROM
	project JOIN address using (addressId)
WHERE
	projectId=".$invoices4Info['projectId'];

$invoices7 = mysql_query($queryInvoice7,$conexion);
$invoices7Info = mysql_fetch_assoc($invoices7);

$queryInvoice8="
SELECT 
	customerId, customerName
FROM
	customer
WHERE
	customerId=".$invoices7Info['customerId'];

$invoices8 = mysql_query($queryInvoice8,$conexion);
$invoices8Info = mysql_fetch_assoc($invoices8);

		if($invoices7Info['projectUnder'] == ILLINOIS_PW_ACT || $invoices7Info['projectUnder'] == DAVIS_BACON_ACT) { $projectsInAct[$invoices7Info['projectName']. " ".$invoices7Info['addressLine1']] = $invoices7Info['projectUnder'];}
		
		$row ="";
		$row.= "<tr>";
			$row.= "<td>".to_MDY($invoices3Info['ticketDate'],true)."</td>";
			$row.= "<td>".$invoices8Info['customerName']."</td>";
			$row.= "<td>".$projectInfo2['brokerPid']."-".$invoices6Info['truckNumber']."</td>";
			$row.= "<td align=left >".$invoices3Info['ticketMfi'];if($invoices3Info['ticketNumber']!="")$row.="/".$invoices3Info['ticketNumber'];$row.="</td>";
			$row.= "<td align=left>".$invoices4Info['itemDisplayFrom']."</td>";
			$row.= "<td align=left>".$invoices4Info['itemDisplayTo']."</td>";
			$row.= "<td >".decimalPad($invoices3Info['ticketBrokerAmount'],2);$row.="</td>";
			$row.= "<td align=right >".decimalPad($invoices4Info['itemBrokerCost'],2);$row.="</td>";
			$row.= "<td align=right >".decimalPad($invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost'],2);$row.="</td>";
			
		$hiddenRow = $row."</tr>\n";
			
			//echo "toll==".$invoices3Info['ticketNumber'];
			//if($invoices3Info['ticketNumber']!=""){
				if(strpos(strtolower($invoices4Info['itemDescription']),"toll")===FALSE){
				//if(strpos($invoices4Info['itemDescription'],"TOLL")===FALSE){
				//if($invoices4Info['ticketNumber'] == 'TOLL'){
					$row.= "<td>".decimalPad($percentage)."%</td>";
					$row.= "<td align=right >".decimalPad($invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost']*$percentage);$row.="</td>";
					$total+=$invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost']*$percentage;
				}else{
					$row.= "<td>".decimalPad('100')."%</td>";
					$row.= "<td align=right >".decimalPad($invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost']);$row.="</td>";
					$total+=$invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost'];
				}
			/*}else{
				$row.= "<td>".decimalPad($percentage)."%</td>";
				$row.= "<td align=right >".decimalPad($invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost']*$percentage);$row.="</td>";
				$total+=$invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost']*$percentage;
			}*/
			
		$row.= "</tr>";
		if($invoices4Info['itemBrokerCost']==0 || $invoices3Info['ticketBrokerAmount']==0)continue;
		
		echo $row;
		$hiddenTable.=$hiddenRow;
		
		$subtotal+=$invoices3Info['ticketBrokerAmount']*$invoices4Info['itemBrokerCost'];
		
				
		$count++;
	}
	echo "<tr><td colspan='2' align=center> $count Tickets </td><td colspan='5'></td><th>Subtotal</th><td align=right >".decimalPad($subtotal);echo"</td><td></td><td align=right >".decimalPad($total);echo"</td></tr>";
	echo "<tr><td colspan='9'></td><th><span>Total</span></th><td align=right ><strong>".decimalPad($total,2);echo"</strong></td></tr>";
	
	//$hiddenTable.="<tr><td colspan='2' align=center> $count Tickets </td><td colspan='5'></td><td><strong>Subtotal</strong></td><td align=right ><strong>".decimalPad($subtotal);echo"</strong></td></tr>";
	//$hiddenTable.="<tr><td colspan='7'></td><td><span>Discount</span></td><td align=right >".decimalPad(decimalPad($total) - decimalPad($subtotal));echo"</td></tr>";
	//$hiddenTable.="<tr><td colspan='7'></td><td><span>Total</span></td><td align=right >".decimalPad($total);echo"</td></tr>";

	$preHidenTable.="<tr>
		<td colspan='2'><strong> $count Tickets</strong></td>
		<td><strong>Subtotal</strong></td><td align='right'><strong>".decimalPad($subtotal, 2)."</strong></td>
		<td><strong>Discount</strong></td><td align='right'><strong>".decimalPad(decimalPad($total) - decimalPad($subtotal), 2)."</strong></td>
		<td><strong>Total</strong></td><td align='right'><strong>".decimalPad($total, 2)."</strong></td>
	</tr>\n";
?>
</table>
</div>

<div class="hide_and_print">
<table align="center" class="report" width="100%" cellspacing="0" >
<?
echo $preHidenTable;
?>
</table>
<table align="center" class="report" width="100%" cellspacing="0" >
<tr>
	<th  >Date</th>
	<th  >Customer</th>
	<th  >Truck</th>
	<th  >Ticket #</th>
	<th  >From</th>
	<th  >To</th>
	<th  >L/T/H</th>
	<th  >Rate</th>
	<th  >Amount</th>
</tr>
<?
echo $hiddenTable;
?>
</table>
</div>

<div>
	<?
	foreach($projectsInAct as $projectName=>$actNumber) {
		$text = "";
		if($actNumber == DAVIS_BACON_ACT) {
			$text = "the <strong>Davis Bacon Act</strong>.";
		}
		if($actNumber == ILLINOIS_PW_ACT) {
			$text = "the <strong>Illinois Prevailing Wage Act</strong>.";
		}
		echo "*<strong>$projectName</strong>: is a prevailing wage job under $text</br>";
	}
	?>
</div>

</form>

</body>
</html>

