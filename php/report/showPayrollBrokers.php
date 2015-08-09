<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$projectId=$_GET['projectId'];
$queryProjectInfo = "select * from project where projectId =$projectId";
$projectsInfo = mysql_query($queryProjectInfo,$conexion);
$projectInfo = mysql_fetch_assoc($projectsInfo);

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


$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$usePercentage = $_GET['usePercentage'];

$weekTable = array();
$nextSat = getNextSaturday($startDate);
		//$weekArray['startDate'] = $startDate;
		$weekArray['startDate'] = date('Y-m-d',strtotime($startDate));
		$weekArray['endDate'] = $nextSat;
	$weekTable[] = $weekArray;

while(strtotime($nextSat)< strtotime($endDate)){
	$weekArray = array();
	$nextSun = date('Y-m-d',strtotime('+1 day',strtotime($nextSat)));
		$weekArray['startDate'] = $nextSun;
		$weekArray['endDate'] = getNextSaturday($nextSun);
		$nextSat = getNextSaturday($nextSun);
	$weekTable[] = $weekArray;
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
		<tr><th>Project</th><td colspan='3'><strong><? echo $projectInfo['projectName'];?></strong></td></tr>
		<tr><th>Date</th><td colspan='3'><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
		<tr><th>Report</th><td colspan='3'>Broker Certified Payroll</td></tr>
		<tr><th>Percentage</th><td colspan='3'><?echo $usePercentage==1?"Broker":"100%"?></td></tr>
		<tr><th>From:</th><td><? echo date('m/d/Y',strtotime($startDate));?></td><th>To:</th><td><? echo date('m/d/Y',strtotime($endDate));?></td></tr>
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

<?php
$total=0;
$count=0;
$tableBody = "";
$tableBody2="";
$brokerPerWeek=array ();
$brokers=array();

	foreach($weekTable as $weekId=>$week)
	{
		$queryTickets="Select * from ticket
		join item using (itemId)
		join project using (projectId)
		where projectId=".$projectId." 
		AND ticketDate BETWEEN '".$week['startDate']."' AND '".$week['endDate']."'";
		$ticketInfo=mysql_query($queryTickets,$conexion);
		
		while($ticket= mysql_fetch_assoc($ticketInfo)){
			
			$queryBroker="Select brokerId,brokerName,brokerPercentage from truck
			join broker using (brokerId)
			where truckId=".$ticket['truckId'];
			$brokerInfo=mysql_query($queryBroker,$conexion);
			$brokerInfoReg= mysql_fetch_assoc($brokerInfo);
			
			$queryDrivers="Select * from driver where driverId=".$ticket['driverId'];
			$driverInfo=mysql_query($queryDrivers,$conexion);
			$driverInfoReg=mysql_fetch_assoc($driverInfo);

			if(!ISSET($brokerPerWeek[$weekId][$brokerInfoReg['brokerId']])){
				$brokers[$brokerInfoReg['brokerName']]=$brokerInfoReg['brokerId'];
			}	
			
			$brokerPercentage = 1;
			if($usePercentage==1){
				$brokerPercentage = $brokerInfoReg['brokerPercentage']/100;
			}
			
			if(!ISSET($brokerPerWeek[$weekId][$brokerInfoReg['brokerId']]['tickets'][$ticket['ticketDate']])){
				$brokerPerWeek[$weekId][$brokerInfoReg['brokerId']]['tickets'][$ticket['ticketDate']]=array();
				
				$brokerPerWeek[$weekId][$brokerInfoReg['brokerId']]['tickets'][$ticket['ticketDate']]['TS']=$ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']*$brokerPercentage;
				
			}
			else{	
				
				$brokerPerWeek[$weekId][$brokerInfoReg['brokerId']]['tickets'][$ticket['ticketDate']]['TS']+=$ticket['ticketBrokerAmount']*$ticket['itemBrokerCost']*$brokerPercentage;
				
			}
			
			$brokerPerWeek[$weekId][$brokerInfoReg['brokerId']]['tickets'][$ticket['ticketDate']][]=$ticket['ticketId'];
			
			
		}
	}

//p_array($brokers);
//p_array($brokerPerWeek);
//p_array($driverPerWeek);

foreach($weekTable as $weekId=>$week)
{
	$currentDate = strtotime($week['startDate']);//date('m/d/Y',strtotime('+1 day',strtotime($startDate)));
	$tableBody.="<tr> <th >Broker</th>";
	$tableBody.= "<th colspan='16' title=".to_MDY($week['startDate'])."-".to_MDY($week['endDate'])."> Week ".($weekId+1)."</th>";
	$tableBody.="</tr>";
	$tableBody.=  "<tr><td colspan='17' align=center>".to_MDY($week['startDate'])."- ".to_MDY($week['endDate'])."</td></tr>";
	$tableBody.=  "<tr></tr><td></td><td align=center width=18  height=18>Sun<br/>".date('m/d',$currentDate)."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Mon<br/>".date('m/d',strtotime('+1 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Tue<br/>".date('m/d',strtotime('+2 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Wed<br/>".date('m/d',strtotime('+3 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Thu<br/>".date('m/d',strtotime('+4 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Fri<br/>".date('m/d',strtotime('+5 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Sat<br/>".date('m/d',strtotime('+6 day', $currentDate))."</td>";
	$tableBody.=  "<td align=center width=18  height=18>Total Hours</td>";
	$tableBody.=  "<td align=center width=18  height=18>Hourly Rate</td>";
	$tableBody.=  "<td align=center width=18  height=18>Gross Pay</td>";
	$tableBody.=  "<td align=center width=18  height=18>Total Made</td>";
	$tableBody.=  "<td align=center width=18  height=18>FICA</td>";
	$tableBody.=  "<td align=center width=18  height=18>Federal W/H</td>";
	$tableBody.=  "<td align=center width=18  height=18>State W/H</td>";
	$tableBody.=  "<td align=center width=18  height=18>Other</td>";
	$tableBody.=  "<td align=center width=18  height=18>Total deductables</td>";
	$tableBody.=  "<td align=center width=18  height=18>Net Pay</td></tr>";
	
	foreach($brokers as $brokerName=>$broker)
	{		$queryBroker="select * from broker where brokerId=".$brokers[$brokerName];
			$brokerInfo=mysql_query($queryBroker,$conexion);
			$broker1=mysql_fetch_assoc($brokerInfo);
			$tableRow = "";
			
			$thisPercentage = 1;
			if($usePercentage==1){
				$thisPercentage = $broker1['brokerPercentage']/100;
			}
			
			$sumQuery ="select 
					brokerId,
					sum(ticketBrokerAmount*itemBrokerCost) as totalMade 
				from ticket join item using (itemId) join truck using (truckId) 
				where ticketDate>='".$week['startDate']."' and ticketDate<='".$week['endDate']."' and brokerId=".$broker1['brokerId'];
			//echo $sumQuery;
			$brokerTotalMade = mysql_fetch_assoc(mysql_query($sumQuery,$conexion));
			
			$tableRow.= "<tr>
			<td  title='".$broker1['brokerPid']."' width=18  height=18>".$brokerName."</td>";
				
			if(!ISSET($brokerPerWeek[$weekId][$brokers[$brokerName]])) $tableRow.= "<td class='empty' colspan='16' align=right ></td>";
			else{
				//$brokersSum[$brokerName]['totalBrokerDebt']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalDebt'];
				//$brokersSum[$brokerName]['totalBrokerPaid']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'];
				$dateAux=strtotime($week['startDate']);
				$sevenDays=7;
				$grossPay=0;
				while($sevenDays>0){	
					if(ISSET($brokerPerWeek[$weekId][$brokers[$brokerName]]['tickets'][date("Y-m-d", $dateAux)])){	
						$TotalEarned=$brokerPerWeek[$weekId][$brokers[$brokerName]]['tickets'][date("Y-m-d", $dateAux)]['TS'];
						$grossPay+=$TotalEarned;
						$tableRow.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>".decimalPad($TotalEarned)." (".($hourlyRate==0?"N/A":decimalPad($TotalEarned/$hourlyRate)).")</td>";
					}
					else
						$tableRow.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>0.00</td>";
						$sevenDays--;
						$dateAux=strtotime('+1 day',$dateAux);
				}
				
					$fica = $grossPay*($localInfo['sssec']+$localInfo['medicare'])/100;
					$ilwithholding = $grossPay - (78.8);
					$whtax = ($ilwithholding<0?0:$ilwithholding)*$localInfo['withHoldingTax'];
					$fed = $grossPay*$localInfo['fed'];
					$other = $grossPay*$localInfo['other'];
					$totalDeductions = $fica + $whtax + $fed + $other + $other;
					
					
					$tableRow.= "<td align=right width=18 height=18>".($hourlyRate==0?"N/A":decimalPad($grossPay/$hourlyRate))."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($hourlyRate)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($grossPay)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($brokerTotalMade['totalMade']*$thisPercentage)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($fica)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($fed)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($whtax)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($other)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($totalDeductions)."</td>";
					$tableRow.= "<td align=right width=18 height=18>".decimalPad($grossPay-$totalDeductions)."</td>";
					$tableRow.="</tr>";
					
					$tableBody.=$tableRow;
			}
			
		
	}
}

$tableBody.= "<tr>";
$tableBody.="</tr>";


echo $tableBody;
//p_array($brokerPerWeek);
//p_array($drivers);
//p_array($brokersSum);
?>
</table>
</form>

</body>
</html>

