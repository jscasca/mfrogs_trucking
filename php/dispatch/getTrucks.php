<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

/*
$queryTrucks = "
SELECT 
	truckId,
	truckNumber,
	brokerPid,
	count(*),
	dispatchId 
FROM
	truck
	LEFT JOIN (select dispatchId,truckId from dispatch JOIN truckdispatch using (dispatchId) where dispatchId = ".$_GET['dispatch'].") as D using (truckId)
	JOIN	broker using(brokerId)
	LEFT JOIN	truckfeature using(truckId)
	JOIN (select truckId,count(*) as maxFeatures from truck LEFT JOIN truckfeature using (truckId) group by truckId) as MaxF using (truckId)
	";
if($_GET['features']!="")
	$queryTrucks.=" WHERE featureId ".($_GET['optionsIndicator']==0?" NOT ":"")."IN (".$_GET['features'].") ";


$queryTrucks.= " GROUP BY truckId, maxFeatures, brokerPid";
if(isset($_GET['features']) && $_GET['features']!=""){
	$countFeatures = count(explode(",",$_GET['features']));
	if($_GET['optionsIndicator']=='2')$queryTrucks.= " HAVING count(*)= $countFeatures ";
	if($_GET['optionsIndicator']=='1')$queryTrucks.= " HAVING count(*) >= ".$_GET['optionsIndicator'];
	if($_GET['optionsIndicator']=='0')$queryTrucks.= " HAVING count(*)= maxFeatures";
}else{
	
}*/
$first = true;
$queryTrucks ="
	SELECT
		truckId,
		truckNumber,
		brokerPid,
		count(*) as featureCount,
		dispatchId
	FROM
		truck
		LEFT JOIN truckdispatch using (truckId)
		LEFT JOIN dispatch using (dispatchId)
		JOIN broker using (brokerId)
		LEFT JOIN truckfeature using (truckId)
";
if($_GET['broker']!=0){
	if($first){
		$queryTrucks .= " WHERE ";
		$first = false;
	}
	$queryTrucks .=" brokerId = ".$_GET['broker'];
}
if($_GET['features']!=""){
	if($first){
		$queryTrucks .= " WHERE ";
		$first = false;
	}else $queryTrucks.=" AND ";
	$queryTrucks .= " featureId ".($_GET['optionsIndicator']==0?" NOT ":"")."IN (".$_GET['features'].") ";
}
$queryTrucks .= " GROUP BY truckId, brokerPid";
if(isset($_GET['features']) && $_GET['features']!=""){
	$countFeatures = count(explode(",",$_GET['features']));
	if($_GET['optionsIndicator']=='0')$queryTrucks.= " HAVING featureCount = 0 ";
	if($_GET['optionsIndicator']=='1')$queryTrucks.= " HAVING featureCount > 0 ";
	if($_GET['optionsIndicator']=='2')$queryTrucks.= " HAVING featureCount >= $countFeatures ";
}

$queryTrucks.=" ORDER BY brokerPid  ";
$imgAdd = "112";
$imgRem = "118";
//echo $queryTrucks;
$actual=0; $tdClass=""; $colorFlag=true;
//echo $queryTrucks;
$trucks = mysql_query($queryTrucks,$conexion);
$num_trucks = mysql_num_rows($trucks);

$tbody= "
<table class='long-center' cellpadding='0' cellspacing='0' id='trucksList'>
						<tr>
							<th class='full' colspan='3'><input type='checkbox' name='truckHeader' id='checkAllTrucks' /><label for='checkAllTrucks'>Trucks</label></th>
						</tr>
<tbody>";
if($num_trucks>0){
	while($truck = mysql_fetch_assoc($trucks)){
		
		$colorFlag=!$colorFlag;
		if($colorFlag)$tdClass="";
		else $tdClass="class='bg'";
		if($truck['dispatchId']==$_GET['dispatch']){
			$tdClass="class='white'";
			$img = $imgRem;
		}else{
			$img = $imgAdd;
		}
		switch($actual){
			case 0:
			$tbody.= "<tr>";
			$tbody.= "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/$img.png' width='14px' /></td>";
			$actual++;
			break;
			case 1:
			$tbody.= "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/$img.png' width='14px' /></td>";
			$actual++;
			break;
			case 2:
			$tbody.= "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/$img.png' width='14px' /></td>";
			$tbody.= "</tr>";
			$actual=0;
			break;
		}
}
switch($actual){
		case 0:break;
		case 1:$tbody.="<td ".$tdClass." colspan='2'></td></tr>";break;
		case 2:$tbody.="<td ".$tdClass." colspan='1'></td></tr>";break;
	}
}
$tbody.= "</tbody></table>";
$jsondata['tbody']=$tbody;
$jsondata['query']=$queryTrucks;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

