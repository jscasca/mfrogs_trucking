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

//get all the cheques

//group by number



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
</td>
<td>
	<table class="dates">
		<tr>
			<th><strong>Date Issued: </strong></th>
			<td><?echo to_MDY(date("Y-m-d"));?></td>
			<td></td>
		</tr>
		<tr>
			<th><strong>From: </strong></th>
			<td><?echo to_MDY($fromDate);?></td>
			<td></td>
		</tr>
		<tr>
			<th><strong>To: </strong></th>
			<td><?echo to_MDY($toDate);?></td>
			<td></td>
		</tr>
	</table>
</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >

<?php

$paidTotal = 0;

$tableHolder = "";

$chequesQuery = "
	SELECT
		paidchequesDate,
		paidchequeNumber,
		reportId,
		SUM(paidchequesAmount) as totalPaid,
		COUNT(*) as totalCheques
	FROM
		paidcheques
	WHERE
		paidchequesDate BETWEEN '$fromDate' and '$toDate'
	GROUP BY
		paidchequesDate, paidchequeNumber
	ORDER BY
		paidchequesDate desc, paidchequeNumber
";
//echo $chequesQuery;

$reports = mysql_query($chequesQuery,$conexion);

while($reportInfo=mysql_fetch_assoc($reports)){
	
	if($reportInfo['totalCheques']>1){
		//more than one check
		
		$reportsPaid = "
			SELECT
				*
			FROM
				paidcheques 
			WHERE
				paidchequesDate ='".$reportInfo['paidchequesDate']."'
				AND paidchequeNumber = '".$reportInfo['paidchequeNumber']."'
		";
		$cheques = mysql_query($reportsPaid,$conexion);
		$tableHolder.="<tr>";
			$tableHolder.="<td rowspan='".$reportInfo['totalCheques']."'>".to_MDY($reportInfo['paidchequesDate'])."</td>";
			$tableHolder.="<td rowspan='".$reportInfo['totalCheques']."'>".$reportInfo['paidchequeNumber']."</td>";
			$tableHolder.="<td rowspan='".$reportInfo['totalCheques']."' align='right'>".decimalPad($reportInfo['totalPaid'])."</td>";
			
			//$tableHolder.="<td>".$reportPaid['reportId']."</td>";
			//$tableHolder.="<td align='rigth'>".decimalPad($reportTotal)."</td>";
			
		$firstBroker = true;
		//echo "\n".$reportsPaid;
		while($payment = mysql_fetch_assoc($cheques)){
			//print_r($payment);
			$reportTotal = getReportTotal($payment['reportId'], $conexion);
			
			if($firstBroker){
				$firstBroker = false;
				$reportPaid = mysql_fetch_assoc(mysql_query("select * from report JOIN broker using (brokerId) where reportId = ".$payment['reportId'],$conexion));
				$tableHolder.="<td rowspan='".$reportInfo['totalCheques']."'>".$reportPaid['brokerName']."</td>";
			}else{
				$tableHolder.= "</tr><tr>\n";
			}
			$tableHolder.="<td>".$payment['reportId']."</td>";
			$tableHolder.="<td>".decimalPad($reportTotal)."</td>";
		}
		
		$tableHolder.="</tr>\n";
		
	}else{
		//just one report
		$reportPaid = mysql_fetch_assoc(mysql_query("select * from report JOIN broker using (brokerId) where reportId = ".$reportInfo['reportId'],$conexion));
		$reportTotal = getReportTotal($reportInfo['reportId'], $conexion);
		$tableHolder.="<tr>";
			$tableHolder.="<td>".to_MDY($reportInfo['paidchequesDate'])."</td>";
			$tableHolder.="<td>".$reportInfo['paidchequeNumber']."</td>";
			$tableHolder.="<td align='right'>".decimalPad($reportInfo['totalPaid'])."</td>";
			$tableHolder.="<td>".$reportPaid['brokerName']."</td>";
			
			$tableHolder.="<td>".$reportPaid['reportId']."</td>";
			$tableHolder.="<td align='rigth'>".decimalPad($reportTotal)."</td>";
			
		
		$tableHolder.="</tr>\n";
		
	}
	$globalTotal+=$reportInfo['totalPaid'];
}
echo "<tr><td colspan='2'></td><td align='right'>".decimalPad($globalTotal)."</td><td colspan='3'></td></tr>";
?>
<tr>
	<th>Date</th>
	<th>Cheque #</th>
	<th>Amount</th>
	<th>Broker</th>
	<th>Report</th>
	<th>reported</th>
</tr>
<?
echo $tableHolder;
?>
</table>

</form>

</body>
</html>

