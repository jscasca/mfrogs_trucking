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
	driver
	JOIN broker using (brokerId)
 ";

switch($_GET['driverStatus']){
		case '2':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="driverStatus=0 ";
		$multipleQuery=true;
		break;
		case '1':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="driverStatus=1 ";
		$multipleQuery=true;
		break;
}
if($_GET['driverName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="( driverLastName like '".$_GET['driverName']."%' OR driverFirstName like '".$_GET['driverName']."%' ) ";
	$multipleQuery=true;
}
if($_GET['brokerPid']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="brokerPid like '".$_GET['brokerPid']."%' ";
}
if($_GET['brokerId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="brokerId = ".$_GET['brokerId']." ";
}
$queryTickets.="
ORDER BY 
	brokerPid, driverLastName
";
//echo $queryTickets;
$tbody = "";
$tbody.= "<tbody>";
				$brokers = mysql_query($queryTickets,$conexion);
				$numBrokers=mysql_num_rows($brokers);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numBrokers>0)
				{
						while($broker=mysql_fetch_assoc($brokers))
						{
							if($actual){
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								$tbody.= "<td ".$tdClass." id='driver".$broker['driverId']."'>".$broker['driverLastName'].", ".$broker['driverFirstName']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								$tbody.= "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								$tbody.= "<td ".$tdClass." id='driver".$broker['driverId']."'>".$broker['driverLastName'].", ".$broker['driverFirstName']."</td>";
								$tbody.= "</tr>";
							}
							$actual=!$actual;
								
						}
						if(!$actual){
							$tbody.="<td ".$tdClass." colspan='2'></td></tr>";
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

