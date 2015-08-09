<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$filter = $_GET['filter'];

//if($_GET['fromDate']==''){$fromDate='0000-00-00';}
//else{$fromDate=to_YMD(mysql_real_escape_string($_GET['fromDate']));}

//if($_GET['toDate']==''){$toDate=date("Y-m-d");}
//else{$toDate=to_YMD(mysql_real_escape_string($_GET['toDate']));}

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
		<caption>Tickets not in existing reports</caption>
		<tr><th>Date Issued</th><td><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
	</table>
</td>
</tr>
<tr>
<td>
	<table class="billinfo">
		<th colspan='2' width="90%">Dates</th>
		<tr><td width='177' ><? echo ""; ?></td></tr>
		<tr><td><strong>From:</strong></td><td><? echo ($fromDate == "" ? "All available": $fromDate); ?></td></tr>
		<tr><td><strong>To:</strong></td><td><? echo ($toDate == "" ? "All available" : $toDate); ?></td></tr>
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

$ticketsQuery = "
	SELECT
		*
	FROM
		ticket
		JOIN item using (itemId)
		JOIN project using (projectId)
		JOIN customer using (customerId)
		JOIN truck using (truckId)
		JOIN broker using (brokerId)
		LEFT JOIN reportticket using (ticketId)
	WHERE
		reportId is null AND
		ticketDate >= '".($fromDate == "" ? "0000-00-00" : to_YMD($fromDate))."' 
		".($toDate == "" ? "" : " AND ticketDate < '".to_YMD($toDate)."' ")."
		".($filter == 0 ? "" : " AND brokerId = ".$filter )."
	ORDER BY
		ticketDate asc
";
//echo $ticketsQuery;
$tickets = mysql_query($ticketsQuery, $conexion);
while($ticket = mysql_fetch_assoc($tickets)){
	
	$tableHolder.="<tr>";
		$tableHolder.="<td>".to_MDY($ticket['ticketDate'])."</td>";
		$tableHolder.="<td>".$ticket['ticketMfi'].($ticket['ticketNumber']==""?"":"/".$ticket['ticketNumber'])."</td>";
		$tableHolder.="<td>".$ticket['customerName']."</td>";
		$tableHolder.="<td>".$ticket['projectName']."</td>";
		$tableHolder.="<td>".$ticket['brokerPid']."-".$ticket['truckNumber']."</td>";
	$tableHolder.="</tr>";
}

?>
	<tr>
		<th >Date</th>
		<th >Ticket</th>
		<th >Customer</th>
		<th >Project</th>
		<th >Truck</th>
	</tr>

<?php

echo $tableHolder;
?>
</table>
</form>

</body>
</html>

