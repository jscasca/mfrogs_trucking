<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);
$brokerId = $_REQUEST['brokerId'];
$driverId = $_REQUEST['driverId'];

$fromDate = to_YMD($_REQUEST['reportStartDate']);
$toDate = ($_REQUEST['reportEndDate'] != "" ? to_YMD($_REQUEST['reportEndDate']) : date('Y-m-d') );

//$fromDate = '2012-11-05';

//$toDate = '2013-02-10';


//echo $fromDate;
//echo "<br/>";
//echo $toDate;
//echo "<br/>";

//$firstSunday = strtotime('last Sunday', strtotime($fromDate));
//echo date('Y-m-d',$firstSunday);

$firstSunday = (isSunday($fromDate) ? $fromDate : lastSunday($fromDate) );
//echo $firstSunday;

$thisSunday = $firstSunday;
$nextSaturday = getNextSaturday($fromDate);

while( strtotime($thisSunday) <= strtotime($toDate) ){
	//echo $thisSunday." __ ".$nextSaturday."<br/>";
	
	//check for tickets
	$queryTickets = "
		SELECT
			*
		FROM
			ticket
			JOIN truck using (truckId)
			LEFT JOIN reportticket using (ticketId)
		WHERE
			ticketDate BETWEEN '$thisSunday' AND '$nextSaturday'
			AND reportId is null
			AND brokerId = $brokerId 
			".($driverId != 0 ? " AND driverId = ".$driverId : "")."
	";
	//echo $queryTickets."<br/>";
	$ticketsForReport = mysql_query($queryTickets,$conexion);
	if(mysql_num_rows($ticketsForReport) >= 1 ){
		//echo "si hay sueltos<br/>";
		$queryInvoice = "
			INSERT INTO report (
				reportDate,
				brokerId,
				reportStartDate,
				reportEndDate,
				reportType
			)
			values (
				CURDATE(),
				".$_REQUEST['brokerId'].",
				'$thisSunday',
				'$nextSaturday',
				".$_REQUEST['driverId']."
			)";
		mysql_query($queryInvoice,$conexion);
		$reportId = mysql_insert_id();
		
		$insertTickets = "
			INSERT INTO reportticket(
				ticketId,
				reportId
			)
				SELECT
					ticketId,
					".$reportId."
				FROM
					ticket
					JOIN truck using (truckId)
					LEFT JOIN reportticket using (ticketId)
				WHERE
					ticketDate BETWEEN '$thisSunday' AND '$nextSaturday'
					AND reportId is null
					AND brokerId = $brokerId 
					".($driverId != 0 ? " AND driverId = ".$driverId : "")."
		";
		mysql_query($insertTickets,$conexion);
	}
	
	$thisSunday = date('Y-m-d',strtotime('+1 day',strtotime($nextSaturday)));
	$nextSaturday = getNextSaturday($thisSunday);
}

/*
$queryInvoice="
insert into
	report
	(
		reportDate,
		brokerId,
		reportStartDate,
		reportEndDate,
		reportType
	)
	values
	(
		CURDATE(),
		'".mysql_real_escape_string($_REQUEST['brokerId'])."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['reportStartDate']))."',
		'".mysql_real_escape_string(to_YMD($_REQUEST['reportEndDate']))."',
		'".mysql_real_escape_string($_REQUEST['driverId'])."'
	)";

//echo $queryBroker;
mysql_query($queryInvoice,$conexion);
$invoice = mysql_insert_id();

$queryTickets="
INSERT INTO
	reportticket
(
	ticketId,
	reportId
)
	SELECT 
	ticketId,
	".$invoice."
FROM
	ticket
JOIN item using (itemId)
JOIN truck using (truckId)
WHERE
	ticketId NOT IN (select ticketId from reportticket) and
	".($_REQUEST['driverId']==0?"":"driverId = {$_REQUEST['driverId']} and ")."
	ticketDate BETWEEN '".mysql_real_escape_string(to_YMD($_REQUEST['reportStartDate']))."' and '".mysql_real_escape_string(to_YMD($_REQUEST['reportEndDate']))."' and
	brokerId=".$_REQUEST['brokerId']."
";
//echo $queryTickets;
mysql_query($queryTickets,$conexion);
//echo $queryInvoice."<br />";
//echo $queryTickets."<br />";

$queryForBroker = "select * from broker join term using (termId)";
$brokerInfo = mysql_query($queryForBroker,$conexion);
$brokerInf = mysql_fetch_assoc($brokerInfo);
$dueDate = date('Y-m-d',strtotime($_REQUEST['reportEndDate'] .'+ '.$brokerInf['termValue'].' days'));

$queryDueDate = "insert into reportDue (reportId,reportDueDate) values ('".$invoice."','".$dueDate."')";
mysql_query($queryDueDate);

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['brokerId'])." into report');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:newBroker Report.php");

?>
