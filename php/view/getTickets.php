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
WHERE";
if($_GET['afterDate']!="0"){$creationDate = $_GET['afterDate'];}
else{$creationDate='0000-00-00';}
$queryTickets.=" ticketDate >= '".$creationDate."' ";
if($_GET['beforeDate']!="0"){$queryTickets.=" AND ticketDate < '".$_GET['beforeDate']."' ";}
if($_GET['ticketMFI']!=""){$queryTickets.=" AND ticketMfi like '".$_GET['ticketMFI']."%' ";}
if($_GET['ticketDump']!=""){$queryTickets.=" AND ticketNumber like '".$_GET['ticketDump']."%' ";}
if($_GET['projectId']!=0){$queryTickets.= " AND projectId=".$_GET['projectId'];}

$queryTickets.="
ORDER BY 
	ticketId DESC
LIMIT 102
";
//echo $queryTickets;
$tbody= "
							<tbody>
						";
$tickets = mysql_query($queryTickets,$conexion);
				$numInvoices = mysql_num_rows($tickets);
				if($numInvoices>0){
					$actual=0;
					$colorFlag=true;
						while($ticket=mysql_fetch_assoc($tickets))
						{
							/*
							 * switch($_GET['invoice']){
		case '2':
		$queryTickets.=" AND invoiceId is null ";
		break;
		case '1':
		$queryTickets.=" AND invoiceId is not null ";
		break;
}
* switch($_GET['invoice']){
		case '2':
		$queryTickets.=" AND invoiceId is null ";
		break;
		case '1':
		$queryTickets.=" AND invoiceId is not null ";
		break;
}
							 * 
							 */
							 $queryInvoice = "SELECT invoiceId FROM invoiceticket where ticketId=".$ticket['ticketId'];
							 $queryReport = "SELECT reportId FROM reportticket where ticketId=".$ticket['ticketId'];
							 $querySupplierInvoice = "SELECT supplierInvoiceId FROM supplierinvoiceticket where ticketId=".$ticket['ticketId'];
							 //echo $queryInvoice;
							 $invoice = mysql_query($queryInvoice,$conexion);
							 $report = mysql_query($queryReport,$conexion);
							 $supplierInvoice = mysql_query($querySupplierInvoice,$conexion);
							 if(mysql_num_rows($invoice)==0){
								 if($_GET['invoice']==1)continue;
								 $invoiceId="";
							 }else{
								 if($_GET['invoice']==2)continue;
								 $inv = mysql_fetch_assoc($invoice);
								 $invoiceId=$inv['invoiceId'];}
							 /*
							 if(mysql_num_rows($report)==0)$reportId="";
							 else{$rep = mysql_fetch_assoc($report);$reportId=$rep['reportId'];}
							 if(mysql_num_rows($supplierInvoice)==0)$snvoiceId="";
							 else{$snv = mysql_fetch_assoc($supplierInvoice);$snvoiceId=$snv['suppliernvoiceId'];}*/
							 
							if($colorFlag)$tdClass="";
							else $tdClass="class='bg'";
								switch($actual){
									case 0:
									$colorFlag=!$colorFlag;
									$tbody.= "<tr>";
									$tbody.= "<td width='6%'".$tdClass." >".to_MDY($ticket['ticketDate'])."</td>";
									$tbody.= "<td size='8px' width='6%'".$tdClass." id='ticket".$ticket['ticketId']."' >".$ticket['ticketMfi']."</td>";
									$tbody.= "<td width='12%'".$tdClass." >".$ticket['ticketNumber']."</td>";
									$tbody.= "<td width='6%'".$tdClass." >".$invoiceId."</td>";
									//$tbody.= "<td width='6%'".$tdClass." >".$treportId."</td>";
									//$tbody.= "<td width='6%' ".$tdClass." >".$snvoiceId."</td>";
									$actual++;
									break;
									case 1:
									$tbody.= "<td width='6%'".$tdClass." >".to_MDY($ticket['ticketDate'])."</td>";
									$tbody.= "<td size='8px' width='6%'".$tdClass." id='ticket".$ticket['ticketId']."' >".$ticket['ticketMfi']."</td>";
									$tbody.= "<td width='12%'".$tdClass." >".$ticket['ticketNumber']."</td>";
									$tbody.= "<td width='6%'".$tdClass." >".$invoiceId."</td>";
									//$tbody.= "<td width='6%'".$tdClass." >".$treportId."</td>";
									//$tbody.= "<td width='6%' ".$tdClass." >".$snvoiceId."</td>";
									#$actual++;
									#break;
									#case 2:
									#$tbody.= "<td ".$tdClass." >".to_MDY($ticket['ticketDate'])."</td>";
									#$tbody.= "<td ".$tdClass."  id='ticket".$ticket['ticketId']."' >".$ticket['ticketMfi']."</td>";
									#$tbody.= "<td ".$tdClass." >".$ticket['invoiceId']."</td>";
									$tbody.= "</tr>";
									$actual=0;
									break;
								}
						}
						switch($actual){
							case 0:break;
							case 1:$tbody.="<td colspan='7'></td></tr>";break;
							#case 2:$tbody.="<td colspan='3'></td></tr>";break;
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

