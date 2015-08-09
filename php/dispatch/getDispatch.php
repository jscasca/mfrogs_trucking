<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['dispatchDate']))
{
	if(isset($_GET['move'])){
		$currentDay = date("Y-m-d",strtotime($_GET['move']." days" ,strtotime($_GET['dispatchDate'])));
	}
	$tbody="<tbody>";
	$dispatchQuery = "select * from dispatch JOIN project using (projectId) LEFT JOIN (select count(*) as truckCount, dispatchId from truckdispatch group by dispatchId) as td using (dispatchId) where dispatchDate = '$currentDay' order by projectName asc";
						$dispatches = mysql_query($dispatchQuery,$conexion);
						$num_dispatch = mysql_num_rows($dispatches);
						if($num_dispatch>0){
							while($dispatch = mysql_fetch_assoc($dispatches)){
								if($dispatch['truckCount']==$dispatch['dispatchCount']){
									$tbody.= "<tr>";
								}else
									$tbody.= "<tr>";
									
								$tbody.="<td id='dispatch".$dispatch['dispatchId']."' >";
								$tbody.= $dispatch['projectName']." <Strong><label id='count".$dispatch['dispatchId']."'>".($dispatch['truckCount']==null?"0":$dispatch['truckCount'])."</label>/<label id='max".$dispatch['dispatchCount']."' >".$dispatch['dispatchCount']."</label></Strong>";
								$tbody.="</td>";
								$tbody.= "</tr>";
							}
						}
	$tbody.="</tbody>";
	$response['tbody']=$tbody;
	$response['currentDate']=to_MDY($currentDay);
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

