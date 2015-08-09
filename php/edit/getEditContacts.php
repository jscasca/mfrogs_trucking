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
	contact
JOIN
	customer
using (customerId) ";

if($_GET['customerId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="customerId='".$_GET['customerId']."%' ";
	$multipleQuery=true;
}

if($_GET['contactName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="contactName like '".$_GET['contactName']."%' ";
	$multipleQuery=true;
}
$queryTickets.="
ORDER BY 
	customerName
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
				$contacts = mysql_query($queryTickets,$conexion);
				$numBrokers=mysql_num_rows($contacts);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numBrokers>0)
				{
						while($contact=mysql_fetch_assoc($contacts))
						{
							if($actual){
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass.">From: </td>";
								$tbody.= "<td ".$tdClass.">".$contact['customerName']."</td>";
								$tbody.= "<td ".$tdClass." id='contact".$contact['contactId']."'>".$contact['contactName']."</td>";
								$tbody.= "</tr>";
								
							}else{
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass.">From: </td>";
								$tbody.= "<td ".$tdClass.">".$contact['customerName']."</td>";
								$tbody.= "<td ".$tdClass." id='contact".$contact['contactId']."'>".$contact['contactName']."</td>";
								
								$tbody.= "</tr>";
							}
							$actual=!$actual;
							$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
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

