<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

$startDate = to_YMD($_GET['startDate']);
$endDate = to_YMD($_GET['endDate']);

$weekTable = array();
$nextSat = getNextSaturday($startDate);
		$weekArray['startDate'] = $startDate;
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

//p_array($weekTable);

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
	<table class="invinfo">
		<caption>Date Balance Report</caption>
		<tr><th>Date</th><td><? echo to_MDY($mfiInfo['CURDATE()']);?></td></tr>
		<tr><th>From Date</th><td><? echo to_MDY($startDate);?></td></tr>
		<tr><th>To Date</th><td><? echo to_MDY($endDate);?></td></tr>
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
$brokerPerWeek=array ();
$brokers=array();
$brokersSum= array ();

$totalBrokersPaid=0;
$totalBrokersDebt=0;
	foreach($weekTable as $weekId=>$week)
	{
	$queryReport="Select * from report where reportDate BETWEEN '".$week['startDate']."' AND '".$week['endDate']."'";
	$reportInfo=mysql_query($queryReport,$conexion);
			
			//TOTAL SUM
			$brokerPerWeek[$weekId]['totalWeekDebt']=0;
			$brokerPerWeek[$weekId]['totalWeekPaid']=0;
			
		while($reports= mysql_fetch_assoc($reportInfo)){
			//echo $reports['brokerId']."\n <br/>";
			//echo $queryReport;
			
			$queryBroker="select * from broker where brokerId=".$reports['brokerId'];
			$brokerInfo=mysql_query($queryBroker,$conexion);
			$broker=mysql_fetch_assoc($brokerInfo);
			
			
			if(!ISSET($brokerPerWeek[$weekId][$reports['brokerId']])){
				$brokers[$broker['brokerName']]=$reports['brokerId'];
				$brokersSum[$broker['brokerName']]['totalBrokerDebt']=0;
				$brokersSum[$broker['brokerName']]['totalBrokerPaid']=0;
				$brokerPerWeek[$weekId][$reports['brokerId']]['totalDebt']=0;
				$brokerPerWeek[$weekId][$reports['brokerId']]['totalPaid']=0;
					}
			$queryReportTicket="select * from reportticket where reportId=".$reports['reportId'];
			$reportTicketInfo=mysql_query($queryReportTicket,$conexion);
			
			//$brokerPerWeek[$weekId][$reports['brokerId']]['brokerName']=$broker['brokerName'];
			
			$queryPaidCheque="select * from paidcheques where reportId=".$reports['reportId'];
			$paidChequeInfo=mysql_query($queryPaidCheque,$conexion);
			
				//total amount to be paid.
				while($reportTicket=mysql_fetch_assoc($reportTicketInfo)){
					$queryTicket="select * from ticket where ticketId=".$reportTicket['ticketId'];
					$ticketInfo=mysql_query($queryTicket,$conexion);
					$ticket=mysql_fetch_assoc($ticketInfo);
					
					$queryItem="select * from item where itemId=".$ticket['itemId'];
					$itemInfo=mysql_query($queryItem,$conexion);
					$item=mysql_fetch_assoc($itemInfo);
					
					if(strtolower($ticket['ticketNumber']=='toll'))
					{
						$brokerPerWeek[$weekId][$reports['brokerId']]['totalDebt']+=($ticket['ticketBrokerAmount']*$item['itemBrokerCost']);
					}
					else
					{
						$brokerPerWeek[$weekId][$reports['brokerId']]['totalDebt']+=($ticket['ticketBrokerAmount']*$item['itemBrokerCost'])*$broker['brokerPercentage']/100;
					}
				}
				
				//total amount paid in cheques
				while($paidCheque=mysql_fetch_assoc($paidChequeInfo))
				{
					$brokerPerWeek[$weekId][$reports['brokerId']]['totalPaid']+=$paidCheque['paidchequesAmount'];
				}
		}
	}

	foreach($brokers as $brokerName=>$broker)
	{		$queryBroker="select * from broker where brokerId=".$brokers[$brokerName];
			$brokerInfo=mysql_query($queryBroker,$conexion);
			$broker=mysql_fetch_assoc($brokerInfo);
			
			$tableBody.= "<tr>
			<td  title='".$brokerName."' width=18  height=18>".$broker['brokerPid']."</td>";
		
			foreach($weekTable as $weekId=>$week){
			if(!ISSET($brokerPerWeek[$weekId][$brokers[$brokerName]])) $tableBody.= "<td class='empty' colspan='3' align=right ></td>";
			else{
				$brokersSum[$brokerName]['totalBrokerDebt']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalDebt'];
				$brokersSum[$brokerName]['totalBrokerPaid']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'];
				
				$tableBody.= "<td align=right width=18 height=18>".decimalPad($brokerPerWeek[$weekId][$brokers[$brokerName]]['totalDebt'])."</td>";
				$tableBody.= "<td align=right width=18  height=18>-".decimalPad($brokerPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'])."</td>";
				$tableBody.= "<td align=right width=18  height=18>".decimalPad($brokerPerWeek[$weekId][$brokers[$brokerName]]['totalDebt']-$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'])."</td>";
			}
			
		}
		$tableBody.= "<td align=right width=18  height=18>".decimalPad($brokersSum[$brokerName]['totalBrokerDebt'])."</td>";
		$tableBody.= "<td align=right width=18  height=18>-".decimalPad($brokersSum[$brokerName]['totalBrokerPaid'])."</td>";
		$tableBody.= "<td align=right width=18  height=18>".decimalPad($brokersSum[$brokerName]['totalBrokerDebt']-$brokersSum[$brokerName]['totalBrokerPaid'])."</td>";
		$tableBody.="</tr>";
		$totalBrokersDebt+=$brokersSum[$brokerName]['totalBrokerDebt'];
		$totalBrokersPaid+=$brokersSum[$brokerName]['totalBrokerPaid'];
	}
echo "<tr> <th >Broker</th>";

foreach($weekTable as $weekId=>$week)
{
	echo "<th colspan='3' title=".to_MDY($week['startDate'])."-".to_MDY($week['endDate'])."> Week ".($weekId+1)."</th>";
}
echo "<th colspan='3'> Broker Total </th>";
echo "</tr>";

//TOTAL SUM PER WEEK
echo "<tr><td  width=18  height=18>Week Total</td>";
$tableBody.= "<tr>
			<td  width=18  height=18>Week Total</td>";

foreach($weekTable as $weekId=>$week)
{
	foreach($brokers as $brokerName=>$broker)
	{
			if(ISSET($brokerPerWeek[$weekId][$brokers[$brokerName]])){
				$brokerPerWeek[$weekId]['totalWeekDebt']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalDebt'];
				$brokerPerWeek[$weekId]['totalWeekPaid']+=$brokerPerWeek[$weekId][$brokers[$brokerName]]['totalPaid'];
			}
	}
	$tableBody.= "<td align=right width=18  height=18>".decimalPad($brokerPerWeek[$weekId]['totalWeekDebt'])."</td>";
	$tableBody.= "<td align=right width=18  height=18>-".decimalPad($brokerPerWeek[$weekId]['totalWeekPaid'])."</td>";
	$tableBody.= "<td align=right width=18  height=18>".decimalPad($brokerPerWeek[$weekId]['totalWeekDebt']-$brokerPerWeek[$weekId]['totalWeekPaid'])."</td>";
	
	echo "<td align=right width=18  height=18>".decimalPad($brokerPerWeek[$weekId]['totalWeekDebt'])."</td>";
	echo "<td align=right width=18  height=18>-".decimalPad($brokerPerWeek[$weekId]['totalWeekPaid'])."</td>";
	echo "<td align=right width=18  height=18>".decimalPad($brokerPerWeek[$weekId]['totalWeekDebt']-$brokerPerWeek[$weekId]['totalWeekPaid'])."</td>";
}
$tableBody.= "<td align=right width=18  height=18>".decimalPad($totalBrokersDebt)."</td>";
$tableBody.= "<td align=right width=18  height=18>-".decimalPad($totalBrokersPaid)."</td>";
$tableBody.= "<td align=right width=18  height=18>".decimalPad($totalBrokersDebt-$totalBrokersPaid)."</td>";
$tableBody.="</tr>";

echo "<td align=right width=18  height=18>".decimalPad($totalBrokersDebt)."</td>";
echo "<td align=right width=18  height=18>-".decimalPad($totalBrokersPaid)."</td>";
echo "<td align=right width=18  height=18>".decimalPad($totalBrokersDebt-$totalBrokersPaid)."</td>";
echo "</tr>";


echo $tableBody;

//p_array($brokerPerWeek);
//p_array($brokers);
//p_array($brokersSum);
?>
</table>
</form>

</body>
</html>

