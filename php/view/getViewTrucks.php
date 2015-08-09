<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$multipleQuery =false;
$queryTickets ="
SELECT
	*
FROM
	truck
JOIN
	broker using (brokerId)
 ";

if($_GET['brokerId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="brokerId ='".$_GET['brokerId']."%' ";
	$multipleQuery=true;
}
if($_GET['truckNumber']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="truckNumber like '".$_GET['truckNumber']."%' ";
}
$queryTickets.="
ORDER BY 
	brokerId DESC
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
				$trucks = mysql_query($queryTickets,$conexion);
				$numtrucks=mysql_num_rows($trucks);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numtrucks>0)
				{
						while($truck=mysql_fetch_assoc($trucks))
						{
							if($actual){
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass." id='truck".$truck['truckId']."'>".$truck['brokerPid']."-".$truck['truckNumber']."</td>";
								$tbody.= "<td>".$truck['truckDriver']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								$tbody.= "<td ".$tdClass." id='truck".$truck['truckId']."'>".$truck['brokerPid']."-".$truck['truckNumber']."</td>";
								$tbody.= "<td>".$truck['truckDriver']."</td>";
								$tbody.= "</tr>";
							}
							$actual=!$actual;
						}
						if(!$actual){
							$tbody.="<td ".$tdClass." colspan='1'></td></tr>";
						}
				}
			$tbody.= "</tbody>";
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

