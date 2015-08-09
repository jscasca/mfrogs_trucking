<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$queryTickets ="
SELECT
					*
				FROM
					ticket
				JOIN item using (itemId)
				JOIN (select brokerPid, truckId, truckNumber from truck JOIN broker using (brokerId)) as T using (truckId)
				JOIN (select addressId as fromAddressId, addressLine1 as fromAddress from address) as FA using (fromAddressId)
				JOIN (select addressId as toAddressId, addressLine1 as toAddress from address) as TA using (toAddressId)
				JOIN material using (materialId)
				LEFT JOIN invoiceTicket using (ticketId)
WHERE";
if($_GET['afterDate']!="0000-00-00"){$creationDate = $_GET['afterDate'];}
else{$creationDate='0000-00-00';}
$queryTickets.=" ticketDate > '".$creationDate."' ";
if($_GET['beforeDate']!="0000-00-00"){$queryTickets.=" AND ticketDate < '".$_GET['beforeDate']."' ";}
if($_GET['supplierId']!=0){$queryTickets.= " AND supplierId=".$_GET['supplierId'];}

$queryTickets.="
ORDER BY 
	ticketId DESC
LIMIT 102
";
//echo $queryTickets;
$tbody.= "
							<tbody>
						";
$tickets = mysql_query($queryTickets,$conexion);
				$numInvoices = mysql_num_rows($tickets);
				if($numInvoices>0){
					$colorFlag=true;
				while($term = mysql_fetch_assoc($tickets))
				{
					if($colorFlag)
					{
						$tbody.= "<tr>";
						!$colorFlag;
					}
					else
					{
						$tbody.= "<tr class='bg'>";
						!$colorFlag;
					}
					$tbody.= "
						<td class='first style2'>".$term['projectId']."</td>
						<td class='first style2'>".$term['itemNumber']."</td>
						<td class='first style2'>".to_MDY($term['ticketDate'])."</td>
						<td class='first style2'>".$term['brokerPid']."-".$term['truckNumber']."</td>
						<td class='first style2'>".$term['materialName']."</td>
						<td class='first style2'>".$term['fromAddress']."</td>
						<td class='first style2'>".$term['toAddress']."</td>
						<td class='first style2'>".$term['ticketMfi']."</td>
						<td><a href='/trucking/php/view/viewTicket.php?i=".$term['ticketId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
							";
					$tbody.= "</tr>";
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

