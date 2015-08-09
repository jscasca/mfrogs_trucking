<?

include("../conexion.php");
include("../commons.php");
//include('../classes/jpgraph/jpgraph.php');
//include('../classes/jpgraph/jpgraph_bar.php');


//p_array($_GET);

$queryTickets = "
SELECT 
	* 
FROM 
	ticket 
	JOIN item using (itemId) 
WHERE
	projectId=".$_GET['projectId']." AND 
	ticketDate between '".to_YMD($_GET['startDate'])."' AND '".to_YMD($_GET['endDate'])."' order by ticketDate desc";
	
//echo $queryTickets;
$overviewGraph = array();
$itemGraph = array();

$tickets = mysql_query($queryTickets,$conexion);
if(mysql_num_rows($tickets)>0){
	while($ticket = mysql_fetch_assoc($tickets)){
		$ticketPerDate[$ticket['ticketDate']][] = $ticket;
		$ticketPerItem[$ticket['itemId']][] = $ticket;
		$ticketPerRequest[] = $ticket; 
		
		//$ticketVal = $ticket['ticketAmount'] * ( $ticket['itemCustomerCost'] - ($ticket['itemMaterialPrice'] + $ticket['itemBrokerCost']));
		
		//if(isset($overviewGraph[$ticket['ticketDate']]))$overviewGrap[$ticket['ticketDate']]+=$ticketVal;
		//else{ $overviewGraph[$ticket['ticketDate']] = $ticketVal;}
		
		//if(isset($itemGraph[$ticket['itemId']][$ticket['ticketDate']]))$itemGrap[$ticket['itemId']][$ticket['ticketDate']]+=$ticketVal;
		//else $itemGraph[$ticket['itemId']][$ticket['ticketDate']] = $ticketVal;
	}
	
	/*

var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);
	 */
	
	$perDateData = array();
	//$perDateData[] = "['Day','Income','Expense']";
	$perDateData[] = array('Day','Income','Expense','Total');
	$tTotal = 0;
	$tIncome = 0;
	$tExpense = 0;
	
	foreach($ticketPerDate as $key=>$val){
		$income = 0;
		$expense = 0;
		$total = 0;
		foreach($ticketPerDate[$key] as $ticketInfo){
			//print_r($ticketInfo);
			$income += $ticketInfo['ticketAmount'] * $ticketInfo['itemCustomerCost'];
			$expense += $ticketInfo['ticketAmount'] * ($ticketInfo['itemBrokerCost']+$ticketInfo['MaterialPrice']);
			$total += $ticketInfo['ticketAmount'] * ($ticketInfo['itemCustomerCost'] -($ticketInfo['itemBrokerCost']+$ticketInfo['MaterialPrice']));
				
		}
		$row = array();
		$row[] = $key;
		$row[] = $income;
		$row[] = $expense;
		$row[] = $total;
		$tIncome += $income;
		$tExpense += $expense;
		$tTotal += $total;
		
		$perDateData[] = $row;
	}
	//echo json_encode($perDateData);
	
	
	/*
	$datax = array();
	$datay = array();
	
	foreach($overviewGraph as $key=>$val){
		$datax[] = $key;
		$datay[] = $val;
	}*/
	$dataHeight = sizeof($perDateData) * 30;

}else{
	echo "nothing to graph";
}


?>

<html>
	<head>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
			console.log("drawing");
			
			var data = google.visualization.arrayToDataTable(
				<? echo json_encode($perDateData);?>
			);
			/*
			var data = google.visualization.arrayToDataTable([
				['Year','data'],
				['2012',1],
				['2011',2],
			]);*/
			var options = {
				title: 'Ticket Profit per Day. Total Profit = Total Income - Total expenses [<?echo "$tTotal = $tIncome - $tExpense";?>]',
				vAxis: {title: 'Date'}
			};
			
			var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
			chart.draw(data, options);
		}
	</script>
	</head>
	<body>
		<div id="chart_div" style="width: 900px; height: <?echo $dataHeight;?>px;"></div>
	</body>
</html>
