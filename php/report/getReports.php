<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

$multiple = false;

$paidR = false;
$unpaidR = false;

switch($_GET['paid']){
		case '2':
		$unpaidR = true;
		break;
		case '1':
		$paidR = true;
		break;
}

$queryReports = "
	SELECT
		*
	FROM
		report
		JOIN broker using (brokerId)
		LEFT JOIN driver ON (driver.driverId = report.reportType)
		JOIN term ON  (term.termId = if(driverId is null, broker.termId, driver.termId))
	WHERE
";

if($_GET['brokerId']!=0){
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryReports.= " report.brokerId=".$_GET['brokerId'];
}

if($_GET['driverId']!=0){
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryReports.= " reportType=".$_GET['driverId'];
}

if($_GET['week']!=""){
	$year = date('Y');
	$sStartDate = week_start_date($_GET['week'], $year);
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryReports.=" reportStartDate < '".$startDate."' AND reportEndDate > '".$startDate."' ";
}

if($_GET['afterDate']!="0"){ 
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryInvoices.=" reportDate > '".$_GET['afterDate']."' ";
}

if($_GET['beforeDate']!="0"){
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryReports.=" reportDate < '".$_GET['beforeDate']."' ";
}

if($_GET['beforeEndDate']!="0"){
	if($multiple){ $queryReports.=" AND "; }
	else{ $multiple = true; }
	$queryReports.=" reportEndDate < '".$_GET['beforeEndDate']."' ";
}

$queryReports.=" ORDER BY reportEndDate desc";

$reports = mysql_query($queryReports,$conexion);
$tbody.= "<tbody>";
while($invoice = mysql_fetch_assoc($reports)){
	$queryTotal = "
		SELECT
			SUM( (ticketBrokerAmount * itemBrokerCost) * (if(item.itemDescription like 'toll%', 100, if(driver.driverId is null, broker.brokerPercentage, driver.driverPercentage ) ) )/100 ) as totalReported
		FROM
			reportticket
			JOIN report using (reportId)
			JOIN ticket using (ticketId)
			JOIN item using (itemId)
			JOIN broker using (brokerId)
			LEFT JOIN driver on (driver.driverId = report.reportType)
		WHERE
			reportId = ".$invoice['reportId']."
	";
	
	$totalInfo = mysql_fetch_assoc(mysql_query($queryTotal,$conexion));
	
	$paidTotal = "
		SELECT
			SUM(paidchequesAmount) as totalPaid,
			COUNT(*) as number
		FROM
			paidcheques
		WHERE
			reportId = ".$invoice['reportId']."
	";
	
	$paidInfo = mysql_fetch_assoc(mysql_query($paidTotal, $conexion));
	
	//$percentage = ($invoice['driverId']==null?$invoice['brokerPercentage']:$invoice['driverPercentage'])/100;
	
	$paidTotal = decimalPad($paidInfo['totalPaid'] == null ? 0 : $paidInfo['totalPaid'], 2);
	$chequesTotal = $paidInfo['number'];
	$reportTotal = decimalPad(($totalInfo['totalReported'] == null ? 0 : $totalInfo['totalReported']), 2);
	
	if($paidTotal == null || $paidTotal <= 0 ){ $paid = 'Unpaid';if($paidR)continue;}
	if($paidTotal != null && $paidTotal >= $reportTotal && $chequesTotal != 0){ $paid = 'Paid'; if($unpaidR)continue;}
	if($paidTotal != null && $paidTotal > 0 && $paidTotal < $reportTotal){ $paid = 'Warning';if($paidR)continue;}
	if($paidTotal != null && $paidTotal > $reportTotal){ $paid = 'Overpaid'; if($unpaid)continue;}
	
	if($colorFlag){$tbody.= "<tr class='even".$paid."' id='report".$invoice['reportId']."'>";}
	
	else{$tbody.= "<tr class='odd".$paid."' id='report".$invoice['reportId']."'>";}
	
	$colorFlag=!$colorFlag;
	
	$tbody.= "<td width='6%'>".$invoice['reportId']."</td>";
	$tbody.= "<td width='5%'>".$invoice['brokerPid']."</td>";
	$tbody.= "<td width='20%'>".($invoice['driverFirstName']==null?'----':$invoice['driverLastName'].", ".$invoice['driverFirstName'])."</td>";
	$tbody.= "<td width='10%'>".to_MDY($invoice['reportDate'])."</td>";
	$tbody.= "<td width='10%'>".to_MDY($invoice['reportStartDate'])."</td>";
	$tbody.= "<td width='10%'>".to_MDY($invoice['reportEndDate'])."</td>";
	$tbody.= "<td width='9%' class='number' > ".(date('m/d/Y', strtotime('+'.$invoice['termValue'].' days', strtotime($invoice['reportEndDate']))))."</td>";
	$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $reportTotal )."</td>";
	$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $paidTotal )."</td>";
	$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $reportTotal - $paidTotal )."</td>";

	//$tbody.= "<td width='8%' class='number' ><a href='newPaycheque.php?reportId=".$invoice['reportId']."'><img src='/trucking/img/87.png' width='24' height='22' /></a></td>";
	$tbody.= "<td width='8%' class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' relHref='".$invoice['reportId']."' /></td>";
	if($paid == "Unpaid" )
	{
	$tbody.= "<td class='number' ><a onclick=\"return confirm('Are you sure you want to delete Invoice #".$invoice['reportId']."?');\" href='deleteReport.php?reportId=".$invoice['reportId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>";
	}
	else
	{
	$tbody.= "<td class='number'></td>";
	}
	$tbody.="<td><img src='/trucking/img/2.png' width='22' height='22' class='managable' reportId='".$invoice['reportId']."' /></td>";
	//$tbody.= "<td></td>";
	$tbody.= "</tr>";
}
$tbody.= "</tbody>";
$jsondata['query']=$queryReports;

/*
$queryInvoices="
SELECT 
ticketId,
truckId,
	reportId, 
	reportType, 
	chequetotal, 
	reportTotal, 
	brokerPid, 
	brokerId, 
	brokerName, 
	reportDate, 
	reportStartDate, 
	reportEndDate, 
	reportDueDate, 
	reportCheck
FROM 
	(
	SELECT 
	ticketId,
	truckId,
		reportId, 
		reportType,
		brokerPid, 
		brokerId, 
		brokerName, 
		reportDate, 
		reportStartDate, 
		reportEndDate, 
		reportDueDate, 
		reportCheck, 
		SUM( itemBrokerCost* ticketBrokerAmount ) * ( brokerPercentage /100 ) AS reportTotal
	FROM 
		report 
		LEFT JOIN reportticket USING ( reportId ) 
		LEFT JOIN ticket USING ( ticketId ) 
		JOIN broker USING ( brokerId ) 
		LEFT JOIN (
			SELECT 
				itemId, 
				itemBrokerCost
			FROM 
				item) AS I USING ( itemId ) 
		LEFT JOIN reportpaid USING ( reportId ) 
		LEFT JOIN reportdue USING ( reportId ) 
	GROUP BY reportId
	ORDER BY reportId DESC
	) AS t1
	LEFT OUTER JOIN (
		SELECT 
			reportId AS t2Id, 
			SUM( paidchequesAmount ) AS chequetotal
		FROM 
			paidcheques 
		GROUP BY 
			reportId) AS t2 ON t2Id = reportId
WHERE";

if($_GET['afterDate']!="0")
{
	$creationDate = $_GET['afterDate'];
}else{$creationDate='0000-00-00';}

$queryInvoices.=" reportDate > '".$creationDate."' ";

if($_GET['beforeDate']!="0")
{
	$queryInvoices.=" AND reportDate < '".$_GET['beforeDate']."' ";
}

if($_GET['beforeEndDate']!="0")
{
	$queryInvoices.=" AND reportEndDate < '".$_GET['beforeEndDate']."' ";
}

if($_GET['week']!="")
{
	$year = date('Y');
	$sStartDate = week_start_date($_GET['week'], $year);
	$queryInvoices.=" AND reportStartDate < '".$startDate."' AND reportEndDate > '".$startDate."' ";
}

if($_GET['driverId']!=0)
{
	$queryInvoices.= " AND reportType=".$_GET['driverId'];
}

if($_GET['truckId']!=0)
{
	$queryInvoices.= " AND truckId=".$_GET['truckId'];
}

if($_GET['brokerId']!=0)
{
	$queryInvoices.= " AND brokerId=".$_GET['brokerId'];
}


switch($_GET['paid']){
		case '2':
		$queryInvoices.=" AND (chequetotal is null OR chequetotal<reportTotal)";
		break;
		case '1':
		$queryInvoices.=" AND chequetotal>=reportTotal";
		break;
}

$queryInvoices.="
GROUP BY 
	reportId
ORDER BY
	reportId DESC";
	$tbody = "";
$tbody.= "<tbody>";
$invoices = mysql_query($queryInvoices,$conexion);
$numInvoices = mysql_num_rows($invoices);
if($numInvoices>0){
	$colorFlag=true;
	$paid='Unpaid';
	while($invoice = mysql_fetch_assoc($invoices)){

		$queryInvoices2="select * from report where reportId=".$invoice['reportId'];						
		$invoices2 = mysql_query($queryInvoices2,$conexion);
		$invoice2 = mysql_fetch_assoc($invoices2);

		$queryInvoices3="select * from driver 
		where driverId=".$invoice2['reportType'];
		$invoices3 = mysql_query($queryInvoices3,$conexion);
		$invoice3 = mysql_fetch_assoc($invoices3);				

		if($invoice['chequetotal']>0 && $invoice['chequetotal'] >= $invoice['reportTotal'] ) {$paid='Paid';}
		else if($invoice['chequetotal']>0 && $invoice['chequetotal']<$invoice['reportTotal'] ) {$paid='Warning';}
		else if($invoice['chequetotal'] == NULL ) {$paid='Unpaid';}

		#if($invoice['reportCheck']!=null){$paid='Paid';}


		if($colorFlag){$tbody.= "<tr class='even".$paid."' id='report".$invoice['reportId']."'>";}
		else{$tbody.= "<tr class='odd".$paid."' id='report".$invoice['reportId']."'>";}
		$colorFlag=!$colorFlag;
		
		$tbody.= "<td width='6%' class='first style2'>".$invoice['brokerPid']."</td>";
		$tbody.= "<td width='20%'>".$invoice['brokerName']."</td>";
		$tbody.= "<td width='20%'>".($invoice3['driverFirstName']==null?'----':$invoice3['driverLastName'].", ".$invoice3['driverFirstName'])."</td>";
		$tbody.= "<td width='10%'>".to_MDY($invoice['reportDate'])."</td>";
		$tbody.= "<td width='10%'>".to_MDY($invoice['reportStartDate'])."</td>";
		$tbody.= "<td width='10%'>".to_MDY($invoice['reportEndDate'])."</td>";
		$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $invoice['reportTotal']==null?'0':$invoice['reportTotal'])."</td>";
		$tbody.= "<td width='9%' class='number' > ".($invoice['reportCheck']==null?($invoice['reportDueDate']==null?'--':to_MDY($invoice['reportDueDate'])):$invoice['reportCheck'])."</td>";
		$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $invoice['chequetotal']==null?'0':$invoice['chequetotal'])."</td>";
		$tbody.= "<td width='9%' class='number' >$ ".decimalPad( $invoice['reportTotal'] - $invoice['chequetotal']==null?'0':$invoice['reportTotal'] - $invoice['chequetotal'])."</td>";
		
		$tbody.= "<td width='8%' class='number' ><a href='newPaycheque.php?reportId=".$invoice['reportId']."'><img src='/trucking/img/87.png' width='20' height='20' /></a></td>";
		
		if($paid == "Unpaid" )
		{
			$tbody.= "<td class='number' ><a onclick=\"return confirm('Are you sure you want to delete Invoice #".$invoice['reportId']."?');\" href='deleteReport.php?reportId=".$invoice['reportId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>";
		}
		else
		{
			$tbody.="<td class='number'></td>";
		}
		//$tbody.= "<td></td>";
		$tbody.= "</tr>";
	}
}
$tbody.= "</tbody>";
*/
$jsondata['table']=$tbody;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

