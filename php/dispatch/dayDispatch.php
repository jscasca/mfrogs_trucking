<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);


if(isset($_GET['day']) && $_GET['day']!=""){
	
	if(isset($_GET['move'])){
		$newDay = date("Y-m-d",strtotime($_GET['move']." days" ,strtotime($_GET['day'])));
	}
	
	$tbody= "";
	$dateInformation = "
	SELECT
		* 
	FROM 
		dispatch 
		JOIN project using (projectId) 
		LEFT JOIN (select count(*) as truckCount, dispatchId from truckdispatch group by dispatchId) as td using (dispatchId) 
	WHERE 
		dispatchDate = '$newDay'
	ORDER BY
		projectName";
			$dispatchs = mysql_query($dateInformation, $conexion);
			while($dispatch = mysql_fetch_assoc($dispatchs)){
				$tbody.= "
				<table class='listing form' cellpadding='0' cellspacing='0' id='dispatch".$dispatch['dispatchId']."'> 
					<tr>
						<th class='first' colspan='2'>".$dispatch['projectName']." : ".$dispatch['dispatchComment']."</th>
						<th class='last' width='10%' >".($dispatch['truckCount']==null?"0":$dispatch['truckCount'])."/".$dispatch['dispatchCount']."</th>
					</tr>";
				$tbody.= "<tbody>";
				$getTrucksPerDispatch = "select * from truckdispatch join truck using (truckId) join broker using (brokerId) where dispatchId = ".$dispatch['dispatchId'];
				$trucks = mysql_query($getTrucksPerDispatch,$conexion);
				$flag = true;
				while($truck = mysql_fetch_assoc($trucks)){
					$tbody.= "<tr ".($flag?"class='bg'":"")." id='truck".$truck['truckId']."' ><td width='20%' ><input type='checkbox' ".($truck['truckDispatchPapers']?"checked disabled ":"")." />".$truck['brokerPid']."-".$truck['truckNumber']."</td><td colspan='2'><p>".($truck['truckDispatchComment']==""?"-no comment-":$truck['truckDispatchComment'])."</p></td></tr>";
					$flag = !$flag;
				}
				$tbody.= "</tbody>";
				$tbody.= "</table>
			";
			}
	
	$response['tbody']=$tbody;
	$response['newDay']=to_MDY($newDay);
	echo json_encode($response);
}

	

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

