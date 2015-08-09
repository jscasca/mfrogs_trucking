<?php

include("../commons.php");

$projectId = $_REQUEST['project'];
$brokerId = $_REQUEST['broker'];
$paperwork = $_REQUEST['papers'];
$groupBy = $_REQUEST['grouping'];

// YYYY-MM-DD
$startDate = $_REQUEST['start']==""?"0000-00-00":$_REQUEST['start'];
$endDate = $_REQUEST['end']==""? date("Y-m-d",strtotime("now")) :$_REQUEST['end'];

//$endDate = date("Y-m-d",strtotime("now"));

$query = "
SELECT
	*
FROM
	dispatch JOIN truckdispatch using (dispatchId) JOIN truck using (truckId)
WHERE
	dispatchDate between '$startDate' AND '$endDate' ";
if($projectId!=0) $query.=" AND projectId = ".$projectId;
if($brokerId!=0) $query.=" AND brokerId = ".$brokerId;
if($paperwork == 1) $query.= " AND truckDispatchPapers = 1";
if($paperwork == 2) $query.= " AND truckDispatchPapers = 0";

$tableData = array();

$resultSet = mysql_query($query,$conexion);
if(mysql_num_rows($resultSet)>0){
	switch($groupBy){
		//Grouping by date
		case 0:
			while($result = mysql_fetch_assoc($resultSet)){
				//$tableData[$result['dispatchDate']][$result['projectId']][$result['brokerId']]['id'] = $result['truckId'];
				$tableData[$result['dispatchDate']][$result['projectId']][$result['brokerId']][$result['truckId']]['number'] = $result['truckNumber'];
				$tableData[$result['dispatchDate']][$result['projectId']][$result['brokerId']][$result['truckId']]['papers'] = $result['truckDispatchPapers'];
				$tableData[$result['dispatchDate']][$result['projectId']][$result['brokerId']][$result['truckId']]['dispatch'] = $result['dispatchId'];
			}
		break;
		//Grouping by project
		case 1:
			while($result = mysql_fetch_assoc($resultSet)){
				//$tableData[$result['projectId']][$result['dispatchDate']][$result['brokerId']]['id'] = $result['truckId'];
				$tableData[$result['projectId']][$result['dispatchDate']][$result['brokerId']][$result['truckId']]['number'] = $result['truckNumber'];
				$tableData[$result['projectId']][$result['dispatchDate']][$result['brokerId']][$result['truckId']]['papers'] = $result['truckDispatchPapers'];
				$tableData[$result['projectId']][$result['dispatchDate']][$result['brokerId']][$result['truckId']]['dispatch'] = $result['dispatchId'];
			}
		break;
		//Grouping by broker
		case 2:
			while($result = mysql_fetch_assoc($resultSet)){
				//$tableData[$result['brokerId']][$result['projectId']][$result['dispatchDate']]['id'] = $result['truckId'];
				$tableData[$result['brokerId']][$result['projectId']][$result['dispatchDate']][$result['truckId']]['number'] = $result['truckNumber'];
				$tableData[$result['brokerId']][$result['projectId']][$result['dispatchDate']][$result['truckId']]['papers'] = $result['truckDispatchPapers'];
				$tableData[$result['brokerId']][$result['projectId']][$result['dispatchDate']][$result['truckId']]['dispatch'] = $result['dispatchId'];
			}
		break;
		
	}
}else{
	
}

$tbody = "<table class='listing form' cellpadding='0' cellspacing='0' id='resultTable'>";

//iterate through first header
foreach($tableData as $bkey => $bigGroup){
	$midCount = count($tableData[$bkey]);
	switch($groupBy){
		case 0:
		$bHeader = $bkey;
		break;
		case 1:
		$bResultSet = mysql_query("select * from project where projectId=".$bkey,$conexion);
		$bRow = mysql_fetch_assoc($bResultSet);
		$bHeader = $bRow['projectName'];
		break;
		case 2:
		$bResultSet = mysql_query("select * from broker where brokerId=".$bkey,$conexion);
		$bRow = mysql_fetch_assoc($bResultSet);
		$bHeader = $bRow['brokerName'];
		break;
	}
	
	$tbody.="<tr><td>$midCount</td><th colspan='5' id='th-b$bkey'>$bHeader</th></tr>";
	//iterate through second header
	foreach($bigGroup as $mkey => $middleGroup){
		$smallCount = count($tableData[$bkey][$mkey]);
		switch($groupBy){
			case 0: case 2:
			$mResultSet = mysql_query("select * from project where projectId=".$mkey,$conexion);
			$mRow = mysql_fetch_assoc($mResultSet);
			$mHeader = $mRow['projectName'];
			break;
			case 1:
			$mHeader = $mkey;
			break;
		}
		
		
		$tbody.="<tr class='tb-b$bkey'><td></td><td>$smallCount</td><th colspan='4' id='th-b$bkey-m$mkey'>$mHeader</th></tr>";
		//iterate through third header
		foreach($middleGroup as  $skey => $smallGroup){
			$miniCount = count($tableData[$bkey][$mkey][$skey]);
			switch($groupBy){
				case 0: case 1:
				$sResultSet = mysql_query("select * from broker where brokerId=".$skey,$conexion);
				$sRow = mysql_fetch_assoc($sResultSet);
				$sHeader = $sRow['brokerName'];
				break;
				
				case 2:
				$sHeader = $skey;
				break;
			}
			$tbody.="<tr class='tb-b$bkey-m$mkey'><td colspan='2'></td><td>$miniCount</td><th colspan='3' id='th-b$bkey-m$mkey-s$skey'>$sHeader</th></tr>";
			//iterate mini
			foreach($smallGroup as $minikey => $miniGroup){
				
				$tbody.="<tr id='truck$minikey' class='tb-b$bkey-m$mkey-s$skey'><td colspan='3'></td><td>".$miniGroup['number']."</td><td><input id='dispatch".$miniGroup['dispatch']."' type='checkbox' ".($miniGroup['papers']?"checked disabled ":"")." /></td><td></td></tr>";
			}
		}
	}
}

$tbody .= "</table>";

$jsonData['query'] = $query;
$jsonData['table'] = $tbody;
echo json_encode($jsonData);



mysql_close($conexion);


?>
