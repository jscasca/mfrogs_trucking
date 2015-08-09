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
	broker
 ";

switch($_GET['brokerStatus']){
		case '2':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="brokerStatus=0 ";
		$multipleQuery=true;
		break;
		case '1':
		if(!$multipleQuery){$queryTickets.="WHERE ";}
		$queryTickets.="brokerStatus=1 ";
		$multipleQuery=true;
		break;
}
if($_GET['brokerName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="brokerName like '".$_GET['brokerName']."%' ";
	$multipleQuery=true;
}
if($_GET['brokerPid']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="brokerPid like '".$_GET['brokerPid']."%' ";
}
$queryTickets.="
order by brokerPid asc
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
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								$tbody.= "<td ".$tdClass." id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								$tbody.= "<td ".$tdClass.">".showPhoneNumber($broker['brokerTel'])."</td>";
								$tbody.= "<td ".$tdClass.">".showPhoneNumber($broker['brokerMobile'])."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";$tbody.= "</tr>";
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

