<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$hideDeduction = false;
if(isset($_GET['hideDeductions']) && $_GET['hideDeductions']=="true"){
	$hideDeduction = true;
}
$queryLocal =
	"SELECT 
		*
	FROM
		stateinfo";
$locals = mysql_query($queryLocal,$conexion);
$localInfo	= mysql_fetch_assoc($locals);
	
	
$queryProjectInfo = "select * from project where projectId =".$_GET['projectId'];
$projectsInfo = mysql_query($queryProjectInfo,$conexion);
$projectInfo = mysql_fetch_assoc($projectsInfo);
$rate['1'] = $projectInfo['projectClass1PW'];
$rate['2'] = $projectInfo['projectClass2PW'];
$rate['3'] = $projectInfo['projectClass3PW'];
$rate['4'] = $projectInfo['projectClass4PW'];

//p_array($rate);

$queryFirstTicket = "select * from project join item using (projectId) join ticket using (itemId) where projectId=".$_GET['projectId']." order by ticketDate asc limit 1";
$lastTicketReg = mysql_query($queryFirstTicket,$conexion);
$numTickets = mysql_num_rows($lastTicketReg);
	$lastTicket = mysql_fetch_assoc($lastTicketReg);
	$firstTicket = $lastTicket['ticketDate']; //in YYYY_MM_DD format

$week1 = "1";
$week2 = "2";

$ret = $firstTicket;
//echo $ret;
for($i=1;$i<=7;$i++){
	if(date('w',strtotime('-'.$i.' day',strtotime($ret)))==0){
		$ret = date('Y-m-d', strtotime('-'.$i.' day', strtotime($ret)));
		break;
	}
}
//Add the weeks
$firstSunday = $ret;

$firstWeekSunday = date('Y-m-d', strtotime('+'.($_GET['week']-1).' week ',strtotime($firstSunday)));
$day['SUN'] = date('m/d', strtotime($firstWeekSunday));
$day['MON'] = date('m/d', strtotime('+1 day ',strtotime($firstWeekSunday)));
$day['TUE'] = date('m/d', strtotime('+2 day ',strtotime($firstWeekSunday)));
$day['WED'] = date('m/d', strtotime('+3 day ',strtotime($firstWeekSunday)));
$day['THU'] = date('m/d', strtotime('+4 day ',strtotime($firstWeekSunday)));
$day['FRI'] = date('m/d', strtotime('+5 day ',strtotime($firstWeekSunday)));
$day['SAT'] = date('m/d', strtotime('+6 day ',strtotime($firstWeekSunday)));
//echo $firstWeekSunday;
$lastWeekSaturday = date('m/d/Y',strtotime('+6 day',strtotime($firstWeekSunday)));

$queryTickets = "
	SELECT
		driverId,
		SUM(ticketAmount * itemBrokerCost) as totalEarned,
		driverPercentage,
		ticketDate
	FROM
		driver
		JOIN ticket using (driverId)
		JOIN item using (itemId)
	WHERE
		projectId = ".$_GET['projectId']." AND
		brokerId = ".$_GET['brokerId']." AND
		ticketDate between '".$firstWeekSunday."' AND '".date('Y-m-d',strtotime('+6 day',strtotime($firstWeekSunday)))."'
	GROUP BY
		driverId, ticketDate
	ORDER BY
		ticketDate asc
		";
//echo $queryTickets;
$payrollCert = mysql_query($queryTickets,$conexion);
$numPayroll = mysql_num_rows($payrollCert);
$payroll=array();
$driversId[]="0";

while($item = mysql_fetch_assoc($payrollCert)){
		//echo "1";
	$payroll[$item['driverId']][date('m/d',strtotime($item['ticketDate']))] = $item['totalEarned'] * $item['driverPercentage']; 
}
foreach($payroll as $key => $pay ){
	$driversId[] = $key;
}

$queryDrivers = "select * from driver left join ethnic using (ethnicId) left join work using (workId) where driverId IN (".implode(',',$driversId).")";
//echo $queryDrivers;
$driverInfoReg = mysql_query($queryDrivers,$conexion);
while($driversInfo = mysql_fetch_assoc($driverInfoReg)){
	$driverInfo[$driversInfo['driverId']]=$driversInfo;
}
//p_array($driversId);
//p_array($payroll);
//p_array($driverInfo);

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

table.insurance th
{background-color: #666;
color: #fff;
padding: 4px;
text-align: left;
font-size: 12px;
font-weight: bold;}

table.insurance td 
{background-color: #CCC;
color: #000;
padding: 4px;
border: 1px #fff solid;
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

</style>

<table align="center" class="report" width="100%" cellspacing="0" >
<tr>
	
	<td colspan="12"></td>
	<? 
	if(!$hideDeduction){
	?>
	<td colspan="7"></td>
	<td colspan="2"></td>
	<?
	}else{
		echo "<td colspan='3'></td>";
	}
	?>
</tr>
<tr>
	<td colspan="4">PayRoll No. <? echo $_GET['week']; ?></td>
	<td colspan="8">Foe week ending: <? echo $lastWeekSaturday; ?></td>
	<? 
	if(!$hideDeduction){
	?>
	<td colspan="6"></td>
	<td colspan="3"></td>
	<?
	}else{
		echo "<td colspan='3'></td>";
	}
	?>
</tr>
<tr>
	<td rowspan="5">Name, Address and SSN</td>
	<td rowspan="5"></td>
	<td rowspan="5">Ethnicity and Gender</td>
	<td rowspan="5">Work and Classification</td>
	<td rowspan="5">OT or ST</td>
	<td colspan="7">(5)</td>
	<td rowspan="5">Total Hours</td>
	<td rowspan="5">Rate of Pay</td>
	<td rowspan="5">Gross amount earned</td>
	<? 
	if(!$hideDeduction){
	?>
	<td colspan="5">(9)</td>
	<td rowspan="5">Net wages paid for week</td>
	<?
	}
	?>
	
</tr>
<tr>
	<td colspan="7">Day and Date</td>
	<? 
	if(!$hideDeduction){
	?>
	<td colspan="5">Deductions</td>
	<?
	}
	?>
</tr>
<tr>
	<td>SUN</td>
	<td>MON</td>
	<td>TUE</td>
	<td>WED</td>
	<td>THU</td>
	<td>FRI</td>
	<td>SAT</td>
	<? 
	if(!$hideDeduction){
	?>
	<td rowspan='3'>FICA</td>
	<td rowspan='3'>TAX</td>
	<td rowspan='3'>FED</td>
	<td rowspan='3'>OTHER</td>
	<td rowspan='3'>TOTAL DEDUCTIONS</td>
	<?
	}
	?>
</tr>
<tr>
	<td><? echo $day['SUN'];?></td>
	<td><? echo $day['MON'];?></td>
	<td><? echo $day['TUE'];?></td>
	<td><? echo $day['WED'];?></td>
	<td><? echo $day['THU'];?></td>
	<td><? echo $day['FRI'];?></td>
	<td><? echo $day['SAT'];?></td>
</tr>
<tr>
	<td colspan="7">Hours Worked</td>
</tr>

<?
//Information
foreach($payroll as $key => $pay ){
	echo "<tr>";
	$driver = $driverInfo[$key];
	$driverRate = ($rate[$driver['driverClass']]!=0?$rate[$driver['driverClass']]:1);
	
	$sun = $payroll[$key][$day['SUN']]/$driverRate;
	$mon = $payroll[$key][$day['MON']]/$driverRate;
	$tue = $payroll[$key][$day['TUE']]/$driverRate;
	$wed = $payroll[$key][$day['WED']]/$driverRate;
	$thu = $payroll[$key][$day['THU']]/$driverRate;
	$fri = $payroll[$key][$day['FRI']]/$driverRate;
	$sat = $payroll[$key][$day['SAT']]/$driverRate;
	
	$totalST = $sun + $mon + $tue + $wed + $thu + $fri + $sat;
	$grossEarnings = $totalST * $driverRate;
		echo "<td rowspan='2'>".$driver['driverFirstName']." ".$driver['driverLastName']." </td>";
		echo "<td rowspan='2'></td>";
		echo "<td rowspan='2'>".$driver['ethnicName']."<br/>".$driver['driverGender']."</td>";
		echo "<td rowspan='2'>".$driver['workName']."<br/> Class ".$driver['driverClass']."</td>";
		echo "<td rowspan='2'></td>";
		echo "<td>".decimalPad($sun)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['MON']]/$driverRate)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['TUE']]/$driverRate)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['WED']]/$driverRate)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['THU']]/$driverRate)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['FRI']]/$driverRate)."</td>";
		echo "<td>".decimalPad($payroll[$key][$day['SAT']]/$driverRate)."</td>";
		echo "<td>".decimalPad($totalST)."</td>";
		echo "<td>".decimalPad($driverRate)."</td>";
		
		echo "<td rowspan='2'>".decimalPad($grossEarnings)."</td>";
		
		if(!$hideDeduction){
			$fica = $grossEarnings*($localInfo['sssec']+$localInfo['medicare'])/100;
			$ilwithholding = $grossEarnings - (78.8);
			$whtax = ($ilwithholding<0?0:$ilwithholding)*$localInfo['withHoldingTax'];
			$fed = $grossEarnings*$localInfo['fed'];
			$other = $grossEarnings*$localInfo['other'];
			$totalDeductions = $fica + $whtax + $fed + $other + $other;
			
		echo "<td rowspan='2'>".decimalPad($fica)."</td>";
		echo "<td rowspan='2'>".decimalPad($whtax)."</td>";
		echo "<td rowspan='2'>".decimalPad($fed)."</td>";
		echo "<td rowspan='2'>".decimalPad($other)."</td>";
		echo "<td rowspan='2'>".decimalPad($totalDeductions)."</td>";
		echo "<td rowspan='2'>".decimalPad($grossEarnings - $totalDeductions)."</td>";
		}
	
	echo"</tr>";
	
	echo "
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	";
}

?>

</table>
</form>

</body>
</html>

