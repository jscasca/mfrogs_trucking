<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$projectId=$_GET['projectId'];
$queryProjectInfo = "select * from project where projectId =".$_GET['projectId'];
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


$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$usePercentage = $_GET['usePercentage'];
$onlyMfi = $_GET['onlyMfi'];

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
border: 1px #000 solid;
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
		<tr><th>Report</th><td colspan='3'>Driver Certified Payroll</td></tr>
		<tr><th>Percentage</th><td colspan='3'><?echo $usePercentage==1?"Driver":"100%"?></td></tr>
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
$drivers=array();
$driverPerWeek=array();

	foreach($weekTable as $weekId=>$week)
	{
		$queryTickets="Select * from ticket
		join item using (itemId)
		join project using (projectId)
		where projectId=".$projectId." 
		AND driverId<>0 
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

			if(($onlyMfi==0)||( $onlyMfi==1 && $brokerInfoReg['brokerId']==64)){
				if(!ISSET($driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]))
				$drivers[$ticket['driverId']][$ticket['truckId']]=$ticket['driverId'];
				
				$driverPercentage =1;
				if($usePercentage==1){
					$driverPercentage = $driverInfoReg['driverPercentage']/100;
				}
				
				if(!isset($driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]['tickets'][$ticket['ticketDate']])){
					$driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]['tickets'][$ticket['ticketDate']] = array();
					
					$driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]['tickets'][$ticket['ticketDate']]['TS']= $ticket['ticketBrokerAmount']*$ticket['itemBrokerCost'] * $driverPercentage;
					
				}else{
					$driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]['tickets'][$ticket['ticketDate']]['TS']+= $ticket['ticketBrokerAmount']*$ticket['itemBrokerCost'] * $driverPercentage;
					
				}
				$driverPerWeek[$weekId][$ticket['driverId']][$ticket['truckId']]['tickets'][$ticket['ticketDate']][]= $ticket['ticketId'];
			}
			
		}
	}

//p_array($brokers);
//p_array($brokerPerWeek);
//p_array($driverPerWeek);

foreach($weekTable as $weekId=>$week)
{
	$currentDate = strtotime($week['startDate']);
	$tableBody2.="<tr> <th >Drivers</th>";
	$tableBody2.= "<th colspan='16' title=".to_MDY($week['startDate'])."-".to_MDY($week['endDate'])."> Week ".($weekId+1)."</th>";
	$tableBody2.="</tr>";
	$tableBody2.=  "<tr><td colspan='17' align=center>".to_MDY($week['startDate'])."- ".to_MDY($week['endDate'])."</td></tr>";
	$tableBody2.=  "<tr></tr><td></td><td align=center width=18  height=18>Sun<br/>".date('m/d',$currentDate)."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Mon<br/>".date('m/d',strtotime('+1 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Tue<br/>".date('m/d',strtotime('+2 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Wed<br/>".date('m/d',strtotime('+3 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Thu<br/>".date('m/d',strtotime('+4 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Fri<br/>".date('m/d',strtotime('+5 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Sat<br/>".date('m/d',strtotime('+6 day', $currentDate))."</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Total Hours</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Hourly Rate</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Gross Pay</td>";
	$tableBody2.=  "<td align=center width=18  height=18>FICA</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Federal W/H</td>";
	$tableBody2.=  "<td align=center width=18  height=18>State W/H</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Other</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Total deductables</td>";
	$tableBody2.=  "<td align=center width=18  height=18>Net Pay</td></tr>";
	
	foreach($drivers as $driverId=>$trucks){
	
		$rowTruck = array();
		$row = array();
		$localEarned =array();
		$driver = mysql_fetch_assoc(mysql_query("select * from driver LEFT JOIN ethnic USING (ethnicId) where driverId=".$driverId,$conexion));
		if($driver['driverPW']>$driverRate) $driverRate = $driver['driverPW'];
		$grossPay = 0;
		$rowsHeight = 1;
		
		$driverRate = 0;
		switch($driver['driverClass']){
			case 1: $driverRate = $projectInfo['projectClass1PW'];break;
			case 2: $driverRate = $projectInfo['projectClass2PW'];break;
			case 3: $driverRate = $projectInfo['projectClass3PW'];break;
			case 4: $driverRate = $projectInfo['projectClass4PW'];break;
		}
		
		foreach($trucks as $truckId=>$tickets){
			$truck = mysql_fetch_assoc(mysql_query("select * from truck where truckId=".$truckId,$conexion));
			
			$rowTruck[ $truck['truckId']] = $truck['truckNumber'];
			
			$dateAux=strtotime($week['startDate']);
			$weekDays =7;
			while($weekDays > 0){
				if(isset($driverPerWeek[$weekId][$driverId][$truckId]['tickets'][date("Y-m-d", $dateAux)])){
					$TotalEarned=$driverPerWeek[$weekId][$driverId][$truckId]['tickets'][date("Y-m-d", $dateAux)]['TS'];
					$grossPay+=$TotalEarned;
					
					if(isset($localEarned[$weekDays])) $localEarned[$weekDays]+= $TotalEarned;
					else $localEarned[$weekDays]= $TotalEarned;
					
					$row[$truck['truckId']][$weekDays] = $TotalEarned;
				}else{
					$row[$truck['truckId']][$weekDays] = 0;
				}
				$weekDays--;
				$dateAux=strtotime('+1 day',$dateAux);
			}
			
			$rowsHeight++;
		}
		
		$tableBody2.= "<tr>
		<td width=18  height=18>".$driver['driverFirstName']." ".$driver['driverLastName']."<br/>".$driver['ethnicName']."<br/>".$driver['driverGender']."</td>";
		$dateAux=strtotime($week['startDate']);
		$weekDays =7;
		while($weekDays > 0){
			if(isset($localEarned[$weekDays])){
				$tableBody2.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>".decimalPad($localEarned[$weekDays])." (".($driverRate==0?"N/A":decimalPad($localEarned[$weekDays]/$driverRate)).")</td>";
			}else{
				$tableBody2.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>0.00</td>";
			}
			$weekDays--;
			$dateAux=strtotime('+1 day',$dateAux);
		}
		$fica = $grossPay*($localInfo['sssec']+$localInfo['medicare'])/100;
		$ilwithholding = $grossPay - (78.8);
		$whtax = ($ilwithholding<0?0:$ilwithholding)*$localInfo['withHoldingTax'];
		$fed = $grossPay*$localInfo['fed'];
		$other = $grossPay*$localInfo['other'];
		$totalDeductions = $fica + $whtax + $fed + $other + $other;
		
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".($driverRate==0?"N/A":decimalPad($grossPay/$driverRate))."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($driverRate)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($grossPay)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($fica)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($fed)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($whtax)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($other)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($totalDeductions)."</td>";
		$tableBody2.= "<td rowspan='$rowsHeight' align=right width=18 height=18>".decimalPad($grossPay-$totalDeductions)."</td>";
		$tableBody2.="</tr>";
		
		foreach($rowTruck as $tId=>$tNumber){
			$tableBody2.="<tr>";
			$tableBody2.="<td>$tNumber</td>";
			$dateAux=strtotime($week['startDate']);
			$weekDays =7;
			while($weekDays > 0){
				$tableBody2.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>".decimalPad( $row[$tId][$weekDays] )."</td>";
				$weekDays--;
				$dateAux=strtotime('+1 day',$dateAux);
			}
			$tableBody2.="</tr>";
		}
		/*
		foreach($trucks as $truckId=>$arr){
			
			$tableBody2.= "<tr>
			<td width=18  height=18>".$driver['driverFirstName']."<br/>".$truck['truckNumber']."<br/>".$ethnicity['ethnicName']."<br/>".$driver['driverGender']."</td>";
				
			if(!ISSET($driverPerWeek[$weekId][$driverId][$truckId])) $tableBody2.= "<td class='empty' colspan='16' align=right ></td>";
			else{
				//$brokersSum[$brokerName]['totalBrokerDebt']+=$driverPerWeek[$weekId][$brokers[$brokerName]]['totalDebt'];
				//$brokersSum[$brokerName]['totalBrokerPaid']+=$driverPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'];
				$dateAux=strtotime($week['startDate']);
				$sevenDays=7;
				$grossPay=0;
				while($sevenDays>0){	
					if(ISSET($driverPerWeek[$weekId][$driverId][$truckId][date("Y-m-d", $dateAux)])){	
						$TotalEarned=$driverPerWeek[$weekId][$driverId][$truckId][date("Y-m-d", $dateAux)]['TS'];
						$grossPay+=$TotalEarned;
						$tableBody2.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>".decimalPad($TotalEarned)." (".($driverRate==0?"N/A":decimalPad($TotalEarned/$driverRate)).")</td>";
					}
					else
						$tableBody2.= "<td title='".date("Y-m-d", $dateAux)."' align=right width=18 height=18>0.00</td>";
					
					$sevenDays--;
					$dateAux=strtotime('+1 day',$dateAux);
				}
				
					$fica = $grossPay*($localInfo['sssec']+$localInfo['medicare'])/100;
					$ilwithholding = $grossPay - (78.8);
					$whtax = ($ilwithholding<0?0:$ilwithholding)*$localInfo['withHoldingTax'];
					$fed = $grossPay*$localInfo['fed'];
					$other = $grossPay*$localInfo['other'];
					$totalDeductions = $fica + $whtax + $fed + $other + $other;
					
					$tableBody2.= "<td align=right width=18 height=18>".($driverRate==0?"N/A":decimalPad($grossPay/$driverRate))."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($driverRate)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($grossPay)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($fica)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($fed)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($whtax)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($other)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($totalDeductions)."</td>";
					$tableBody2.= "<td align=right width=18 height=18>".decimalPad($grossPay-$totalDeductions)."</td>";
					$tableBody2.="</tr>";
			}
		}*/
		
	}
}

$tableBody2.= "<tr>";
$tableBody2.="</tr>";

echo "<tr><td colspan='17' ><br/><br/></td></tr>";
echo $tableBody2;

//p_array($driverPerWeek);
//p_array($drivers);
//p_array($brokersSum);
?>
</table>
</form>

</body>
</html>

