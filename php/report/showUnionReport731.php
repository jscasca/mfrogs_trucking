<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$unionId=$_GET['unionId'];

$mfiId = 64;

//deductions
$queryLocal =
	"SELECT 
		*
	FROM
		stateinfo";
$locals = mysql_query($queryLocal,$conexion);
$localInfo	= mysql_fetch_assoc($locals);

	$hourlyRate=$projectInfo['projectBrokerPW'];

if($projectInfo['projectClass3PW']==0) $driverRate =$localInfo['hourlyRate'];
else $driverRate = $projectInfo['projectClass3PW'];

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$weekTable = array();

$nextSat = getNextSaturday($startDate);
$week[1]['startDate'] = date('Y-m-d',strtotime($startDate));
$week[1]['endDate'] = $nextSat;

for($i=2;$i<=5;$i++){
	$nextSun = date('Y-m-d',strtotime('+1 day',strtotime($nextSat)));
	
	if(strtotime($nextSun)>strtotime($endDate)){
		$week[$i]['startDate'] = $nextSat;
		$nextSat = getNextSaturday($nextSun);
		$week[$i]['endDate'] = $nextSun;
	}else{
	
		$week[$i]['startDate'] = $nextSun;
		$nextSat = getNextSaturday($nextSun);
		$week[$i]['endDate'] = $nextSat;
		
	}
}

function getNextSaturday($date)
{
	$now = strtotime($date);
	//echo $now;
	$nextSaturday = strtotime('next Saturday', $now);
	return date('Y-m-d',$nextSaturday);
}


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

table.report td.empty{
background-color: #B0C4DE;
}

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
	<table class="invinfo" width='100%'>
		<caption><? echo $projectInfo['projectName'];?></caption>
		<tr><th>Date</th><td colspan='3'><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
		<tr><th>Report</th><td colspan='3'>Date Balance Report</td></tr>
		<tr><th>From:</th><td><? echo date('m/d/Y',strtotime('+1 day',strtotime($startDate)));?></td><th>To:</th><td><? echo date('m/d/Y',strtotime('+1 day',strtotime($endDate)));?></td></tr>
	</table>
</td>
</tr>
<tr>
<td>

</td>
<td>
</td>
<td>
</td>
</tr>
</table>

<br>

<table align="center" class="report" width="100%" cellspacing="0" >
	<tr>
		<th></th>
		<th>SSN</th>
		<th>Name</th>
		<th>Remains</th>
		<th>Rate</th>
		<th>Union Rate</th>
		<th>1</th>
		<th>2</th>
		<th>3</th>
		<th>4</th>
		<th>5</th>
		<th>Total</th>
		<th>Remaning</th>
		<th>Date Hired</th>
	</tr>
<?php
$total=0;
$count=0;
$tableBody = "";
$tableBody2="";
$brokerPerWeek=array ();
$brokers=array();
$drivers=array();
$driverPerWeek=array();

$map = array();
$rowId=1;

$totalWorkerHours = 0;
$unionInfo = mysql_fetch_assoc(mysql_query("select * from union731 where unionStartDate <='".$startDate."' and unionEndDate>='".$endDate."'",$conexion));
	

	$queryDrivers = "select * from driver where unionId=".$unionId;
	$drivers = mysql_query($queryDrivers,$conexion);
	while($driver = mysql_fetch_assoc($drivers)){
		$driverRow['name'] = $driver['driverFirstName']." ".$driver['driverLastName'];
		$totalEarning = 0;
		$totalHours = 0;
		
		$lastWeekHours = 0;
		
		$lastRemainingHours = mysql_fetch_assoc(mysql_query("select * from remainings_731 where driverId=".$driver['driverId']." and remainingStartDate<'$startDate' order by remainingStartDate desc limit 1",$conexion));
		//echo "select * from remainings_731 where driverId=".$driver['driverId']." and remainingStartDate<'$startDate' order by remaningStartDate desc limit 1";
		$lastRem = 0;
		if($lastRemainingHours!=null)$lastRem = $lastRemainingHours['remainingValue'];
		$driverRate = $driver['driverPW'];
		if($unionInfo!=null){
			if($unionInfo['unionClass'.$driver['driverClass'].'HourlyRate']>$driverRate){
				$driverRate = $unionInfo['unionClass'.$driver['driverClass'].'HourlyRate'];
			}
		}
		
		$row= "<tr>";
		$row.="<td>$rowId</td>";
		$row.= "<td>".$driver['driverSSN']."</td>";
		$row.= "<td>".$driver['driverFirstName']." ".$driver['driverLastName']."</td>";
		$row.= "<td>".decimalPad($lastRem)."</td>";
		$row.= "<td>".decimalPad($driver['driverPW'])."</td>";
		$row.= "<td>".decimalPad($driverRate)."</td>";
		$driverPercentage = $driver['driverPercentage']/100;
		
		
		
		for($i=1;$i<=5;$i++){
			$ticketsQuery = "select sum(ticketBrokerAmount*itemBrokerCost*".$driverPercentage.") as weekTotal from ticket join item using (itemId) where driverId=".$driver['driverId']." and ticketDate>='".$week[$i]['startDate']."' and ticketDate<='".$week[$i]['endDate']."'";
			//echo $ticketsQuery;
			$tickets = mysql_query($ticketsQuery,$conexion);
			$weekTickets = mysql_fetch_assoc($tickets);
			$weekTotal = $weekTickets['weekTotal']+$lastRem; $lastRem = 0;
			//$map[$driver['driverId']][$i]['earnings'] = $weekTickets['weekTotal'];
			$workedTime = $weekTotal/$driverRate;
			//$map[$driver['driverId']][$i]['time'] = $workedTime;
			$workedHours = floor($workedTime);
			$workedHours+=$lastWeekHours;
			if($workedHours>40){
				$lastWeekHours = $workedHours -40;
				$workedHours = 40;
			}else{
				$lastWeekHours=0;
			}
			//$map[$driver['driverId']][$i]['hours'] = $workedHours;
			$totalEarning += $weekTotal;
			$totalHours += $workedHours;
			$row.= "<td>".decimalPad($weekTotal)."<br/>(".$workedHours.")</td>";
		}
		$remaining = $totalEarning - ($totalHours*$driverRate);
		$row.= "<td>".decimalPad($totalEarning)."<br/>(".$totalHours.")</td>";
		$row.= "<td>".decimalPad($remaining)."</td>";
		$row.= "<td>".to_MDY($driver['driverStartDate'])."</td>";
		$totalWorkerHours+=$totalHours;
		$lastRemaining = mysql_fetch_assoc(mysql_query("select * from remainings_731 where driverId=".$driver['driverId']." and remainingStartDate='$startDate' limit 1",$conexion));
		if($lastRemaining==null){
			mysql_query("insert into remainings_731 (driverId,remainingValue,remainingStartDate) values (".$driver['driverId'].",".decimalPad($remaining).",'$startDate')",$conexion);
		}else{
			mysql_query("update remainings_731 set remainingValue='".decimalPad($remaining)."' where driverId=".$driver['driverId']." and remainingStartDate='$startDate' ",$conexion);
		}
		
		if($totalEarning>0){echo $row;$rowId++;}
	}
	
	echo "<tr>
		<th colspan='6'></th>
		<td colspan='5'>Total Hours</td>
		<td>".$totalWorkerHours."</td>
		<td>PRIOR PREIORD PAYMENTS</td>
		<td>TOTAL CHECK AMOUNT</td>
		</tr>";
	
	if($unionInfo==null){
		echo "<tr>
			<td colspan='11'>No union 731 information for this period</td>
		</tr>";
	}else{
		echo "<tr><th colspan='6'></th><td colspan='5'>Total Welfare @ ".decimalPad($unionInfo['unionWelfare'])."</td><td align='right'>".decimalPad($unionInfo['unionWelfare']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th colspan='6'></th><td colspan='5'>Total Pension @ ".decimalPad($unionInfo['unionPension'])."</td><td align='right'>".decimalPad($unionInfo['unionPension']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><td colspan='14'></td></tr>";
		echo "<tr><th ></th><td colspan='8'>CHICAGLOAND CONSTRUCTION SAFETY COUNCIL</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionCCSC'])."</td><td align='right'>".decimalPad($unionInfo['unionCCSC']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th ></th><td colspan='8'>CONSTRUCTION INDUSTRY SERVICE CORPORATION</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionCISC'])."</td><td align='right'>".decimalPad($unionInfo['unionCISC']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th ></th><td colspan='8'>MARBA INDUSTRY ADVANCEMENT FUND</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionMIAF'])."</td><td align='right'>".decimalPad($unionInfo['unionMIAF']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th ></th><td colspan='8'>IL. TEAMSTER EMPLOYER TRAINING FUND</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionITETF'])."</td><td align='right'>".decimalPad($unionInfo['unionITETF']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th ></th><td colspan='8'>L.M.C.C. TASK FORCE</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionLTF'])."</td><td align='right'>".decimalPad($unionInfo['unionLTF']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		echo "<tr><th ></th><td colspan='8'>L.U. 731 SCHOLARSHIP FUND</td><td align='right' colspan='2'>$ ".decimalPad($unionInfo['unionSF'])."</td><td align='right'>".decimalPad($unionInfo['unionSF']*$totalWorkerHours)."</td><td></td><td></td></tr>";
		
	}


//p_array($brokerPerWeek);
//p_array($drivers);
//p_array($brokersSum);
?>
</table>
</form>

</body>
</html>

