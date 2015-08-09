<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

$queryMfi="
SELECT
	*,
	CURDATE()
FROM
	mfiinfo
JOIN address using (addressId)
";
$mfiInfo = mysql_fetch_assoc(mysql_query($queryMfi,$conexion));

$projectId = $_GET['projectId'];
$projectQuery = "SELECT * FROM project WHERE projectId = $projectId";
$projectInfo = mysql_fetch_assoc(mysql_query($projectQuery, $conexion));

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$weekTable = array();
$nextSat = getNextSaturday($startDate);
		
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
		<tr><th>Report</th><td colspan='3'>Workers Canvas</td></tr>
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
<?

$workersPerWeek = array();

$workersMissingGender = array();
$workersMissingEthnic = array();

$brokerForTotal = array();
$driverForTotal = array();

$totalAccount = array();

$totalPeople = 0;
$totalMinority = 0;
$totalFemale = 0;
$totalChicagoans = 0;

foreach($weekTable as $weekId=>$week) {
	$workersPerWeek[$weekId]['total'] = 0;
	$workersPerWeek[$weekId]['minority'] = 0;
	$workersPerWeek[$weekId]['female'] = 0;
	$workersPerWeek[$weekId]['chicagoan'] = 0;
	
	$ticketsQuery = "SELECT * FROM ticket JOIN item USING (itemId) WHERE projectId = $projectId AND ticketDate BETWEEN '".$week['startDate']."' AND '".$week['endDate']."'";
	$tickets = mysql_query($ticketsQuery, $conexion);
	
	while($ticket = mysql_fetch_assoc($tickets)) {
		$brokerQuery = "SELECT * FROM truck JOIN broker USING (brokerId) JOIN address ON broker.addressId = address.addressId LEFT JOIN ethnic USING (ethnicId) WHERE truckId = ".$ticket['truckId'];
		//echo $brokerQuery;
		$brokerInfo = mysql_fetch_assoc(mysql_query($brokerQuery, $conexion));
		
		if($brokerInfo['brokerId'] == MFI_BROKER) {
			$driverQuery = "SELECT * FROM driver JOIN address USING (addressId) LEFT JOIN ethnic USING (ethnicId) WHERE driverId =".$ticket['driverId'];
			$driverInfo = mysql_fetch_assoc(mysql_query($driverQuery, $conexion));
			
			if(isset($workersPerWeek[$weekId]['drivers'][$driverInfo['driverId']])) {
				
			} else {
				if(!isset($totalAccount['driver'][$driverInfo['driverId']])) {
					$totalPeople++;
					if($driverInfo['driverGender'] == 'Female') { $totalFemale++; }
					if($driverInfo['ethnicMinority'] == '1') { $totalMinority++; }
					if(strtolower($driverInfo['addressCity']) == 'chicago') { $totalChicagoans++; }
					$totalAccount['driver'][$driverInfo['driverId']] = 1;
				}
				$workersPerWeek[$weekId]['total'] = 1 + $workersPerWeek[$weekId]['total'];
				$workersPerWeek[$weekId]['drivers'][$driverInfo['driverId']] = 1;
				if($driverInfo['driverGender'] == 'Female') {
					$workersPerWeek[$weekId]['female'] = 1 + $workersPerWeek[$weekId]['female'];
				}
				
				if($driverInfo['ethnicMinority'] == '1') {
					$workersPerWeek[$weekId]['minority'] = 1 + $workersPerWeek[$weekId]['minority'];
				}
				if(strtolower($driverInfo['addressCity']) == 'chicago') {
					$workersPerWeek[$weekId]['chicagoan'] = 1 + $workersPerWeek[$weekId]['chicagoan'];
				}
			}
		} else {
			if(isset($workersPerWeek[$weekId]['brokers'][$brokerInfo['brokerId']])) {
				
			} else {
				if(!isset($totalAccount['broker'][$brokerInfo['brokerId']])) {
					$totalPeople++;
					if($brokerInfo['brokerGender'] == 'Female') { $totalFemale++; }
					if($brokerInfo['ethnicMinority'] == '1') { $totalMinority++; }
					if(strtolower($brokerInfo['addressCity']) == 'chicago') { $totalChicagoans++; }
					$totalAccount['broker'][$brokerInfo['brokerId']] = 1;
				}
				$workersPerWeek[$weekId]['total'] = 1 + $workersPerWeek[$weekId]['total'];
				$workersPerWeek[$weekId]['brokers'][$brokerInfo['brokerId']] = 1;
				if($brokerInfo['brokerGender'] == 'Female') {
					$workersPerWeek[$weekId]['female'] = 1 + $workersPerWeek[$weekId]['female'];
				}
				
				if($brokerInfo['ethnicMinority'] == '1') {
					$workersPerWeek[$weekId]['minority'] = 1 + $workersPerWeek[$weekId]['minority'];
				}
				if(strtolower($brokerInfo['addressCity']) == 'chicago') {
					$workersPerWeek[$weekId]['chicagoan'] = 1 + $workersPerWeek[$weekId]['chicagoan'];
				}
			}
		}
	}
}
?>
<table align="center" class="report" width="100%" cellspacing="0" >
	<tr>
		<th>Week</th>
		<th>Total</th>
		<th>Minority</th>
		<th>Female</th>
		<th>Chicagoan</th>
	</tr>
<?

foreach($weekTable as $weekId=>$week) {
	echo "<tr>";
		echo "<td>".to_MDY($week['startDate'])."-".to_MDY($week['endDate'])."</td>";
		echo "<td>".$workersPerWeek[$weekId]['total']."</td>";
		echo "<td>".$workersPerWeek[$weekId]['minority']."</td>";
		echo "<td>".$workersPerWeek[$weekId]['female']."</td>";
		echo "<td>".$workersPerWeek[$weekId]['chicagoan']."</td>";
	echo "</tr>";
}
?>
</table>
<table align="center" class="report" width="100%" cellspacing="0" >
	<tr>
		<th>Contractor</th>
		<th>Total</th>
		<th>Minority</th>
		<th>Female</th>
		<th>Chicago Residency</th>
	</tr>
	<tr>
		<td>Martinez Frog's</td>
		<td><?echo $totalPeople;?></td>
		<td><?echo $totalMinority;?></td>
		<td><?echo $totalFemale;?></td>
		<td><?echo $totalChicagoans;?></td>
	</tr>
</table>
</form>

</body>
</html>

