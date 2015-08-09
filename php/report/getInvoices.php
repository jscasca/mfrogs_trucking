<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$queryInvoices="
SELECT 
invoiceId,
project.projectId,
projectName,
invoiceDate,
invoiceStartDate,
invoiceEndDate,
sum(itemCustomerCost*ticketAmount) as invoiceTotal 
	FROM 
		invoice 
		JOIN invoiceticket using (invoiceId) 
		JOIN ticket using (ticketId) 
		JOIN item using (itemId) 
		JOIN project ON (invoice.projectId = project.projectId) 
WHERE ";

if($_GET['afterDate']!="0"){ $creationDate = $_GET['afterDate'];
}else{$creationDate='0000-00-00';}

if($_GET['beforeDate']!="0"){ $endDate = $_GET['beforeDate'];
}else{$endDate = date('Y-m-d');}

$multiLine = false;

//$queryInvoices.=" invoiceDate > '".$creationDate."' ";

if($_GET['afterDate']!="0")
{
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.="invoiceDate  >= '".$creationDate."' ";
}

if($_GET['beforeDate']!="0")
{
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.="invoiceDate  <= '".$endDate."'";
}

if($_GET['week']!="")
{
	$year = date('Y');
	$startDate = week_start_date($_GET['week'], $year);
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.=" invoiceDate < '".lastSunday($startDate)."' AND invoiceDate > '".getNextSaturday($startDate)."' ";
}

if($_GET['invoiceId']!=0)
{
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.= " invoiceId like '".$_GET['invoiceId']."%' ";
}

if($_GET['projectId']!=0)
{
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.= " project.projectId=".$_GET['projectId'];
}

if($_GET['customerId']!=0)
{
	if($multiLine)$queryInvoices.=" AND ";$multiLine =true;
	$queryInvoices.= " customerId=".$_GET['customerId'];
}

$queryInvoices.="
 GROUP BY  
	invoiceId
 ORDER BY 
	invoiceId DESC";
	
	//echo $queryInvoices;
$tbody= "";
$tbody.= "<tbody>";
$invoices = mysql_query($queryInvoices,$conexion);
				$numInvoices = mysql_num_rows($invoices);
				if($numInvoices>0){
					
					$colorFlag=true;
					
						while($invoice = mysql_fetch_assoc($invoices)){
							$paid='Unpaid';
							$queryPaid="select SUM( receiptchequesAmount ) AS chequetotal, COUNT(*) AS countCheques from receiptcheques where invoiceId=".$invoice['invoiceId'];
							$paidReg=mysql_query($queryPaid,$conexion);
							$paidInfo = mysql_fetch_assoc($paidReg);
							
							$paidTotal = decimalPad($paidInfo['chequetotal']==null?'0':$paidInfo['chequetotal']);
							$invoiceTotal = decimalPad($invoice['invoiceTotal']);
							$invoiceBalance = decimalFill( decimalPad($invoiceTotal - $paidTotal));
							
							if($invoiceBalance < 0 ){ $paid = 'Overpaid';}
							if($paidTotal>0 && $invoiceBalance == 0 ) {$paid='Paid';}
							else if($paidTotal>0 && $invoiceBalance > 0 ) {$paid='Warning';}
							else if($paidInfo['chequetotal'] == NULL ) {$paid='Unpaid';}
							
							if($paid=='Paid' && $_GET['paid']==2)continue;
							if(($paid=='Warning' || $paid=='Unpaid' ) && $_GET['paid']==1)continue;
							
							if($colorFlag){$tbody.= "<tr class='even".$paid."' id='invoice".$invoice['invoiceId']."'>";}
							else{$tbody.= "<tr class='odd".$paid."' id='invoice".$invoice['invoiceId']."'>";}
							$colorFlag=!$colorFlag;
							$tbody.= "<td class='first style2' width='7%'>".$invoice['projectId']."</td>";
							$tbody.= "<td class='first style2' width='7%'>".$invoice['invoiceId']."</td>";
							$tbody.= "<td width=10>".$invoice['projectName']."</td>";
							$tbody.= "<td width='9%'>".to_MDY($invoice['invoiceDate'])."</td>";
							$tbody.= "<td width='9%'>".to_MDY($invoice['invoiceStartDate'])."</td>";
							$tbody.= "<td width='9%'>".to_MDY($invoice['invoiceEndDate'])."</td>";
							$tbody.= "<td width=20 class='number' >$ ".$invoiceTotal."</td>";
							$tbody.= "<td width=24 class='number' >$ ".$paidTotal."</td>";
							$tbody.= "<td width=15 class='number' >$ ".$invoiceBalance."</td>";
							
							
							if($paid == 'Unpaid' || $paid == 'Warning') $tbody.= "<td class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' invoiceId='".$invoice['invoiceId']."' /></td>";
							else $tbody.= "<td></td>";
							
							if($paid == 'Unpaid')$tbody.= "<td><img src='/trucking/img/118.png' width='20' height='20' class='deletable' invoiceId='".$invoice['invoiceId']."' invoiceNumber='".$invoice['invoiceId']."' /></td>";
							else $tbody.= "<td><img src='/trucking/img/2.png' width='24' height='22' class='managable' invoiceId='".$invoice['invoiceId']."' /></td>";
							
							$tbody.= "</tr>";
							
						}
				}
			$tbody.= "</tbody>";
$jsondata['table']=$tbody;
$jsondata['query']=str_replace("\r","",str_replace("\t","",str_replace("\n","",$queryInvoices)));

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

