<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);
//p_array($_SESSION);
$queryReport="Select * from reportticket
where ticketId=".$_GET['ticketId'];
$Report = mysql_query($queryReport,$conexion);
$reports = mysql_fetch_assoc($Report);

$queryLast =
	"SELECT
	*
FROM
	ticket
JOIN item using (itemId)
JOIN project using (projectId)
JOIN (select brokerId, brokerPid, truckId, truckNumber from truck JOIN broker using (brokerId)) as T using (truckId)
JOIN material using (materialId)
WHERE
		ticketId=".$_GET['ticketId'];
		//echo $queryLast;
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);

$uInvoiceQuery = "SELECT * FROM invoiceticket where ticketId=".$lastVal['ticketId'];
		 $invoiceR = mysql_query($uInvoiceQuery,$conexion);
		 if(mysql_num_rows($invoiceR)==0)$lastVal['invoiceId']="--";
		else{$inv = mysql_fetch_assoc($invoiceR);$lastVal['invoiceId']=$inv['invoiceId'];}
		
		$uReportQuery = "SELECT * FROM reportticket where ticketId=".$lastVal['ticketId'];
		 $reportR = mysql_query($uReportQuery,$conexion);
		 if(mysql_num_rows($reportR)==0)$lastVal['reportId']="--";
		else{$rep = mysql_fetch_assoc($reportR);$lastVal['reportId']=$rep['reportId'];}
		
		$uSInvoiceQuery = "SELECT * FROM supplierinvoiceticket where ticketId=".$lastVal['ticketId'];
		 $snvoiceR = mysql_query($uSInvoiceQuery,$conexion);
		 if(mysql_num_rows($snvoiceR)==0)$lastVal['supplierInvoiceId']="--";
		else{$usnv = mysql_fetch_assoc($snvoiceR);$lastVal['supplierInvoiceId']=$usnv['supplierInvoiceId'];}
		
		if($lastVal['driverId']!=0){
			$driverQuery = "SELECT * FROM driver where driverId=".$lastVal['driverId'];
			$driverReg = mysql_query($driverQuery,$conexion);
			if(mysql_num_rows($driverReg)==0)$lastVal['driverName'] = "<span  style='color:red;'>Driver Mismatch!</span>";
			else{
				$driver = mysql_fetch_assoc($driverReg);
				$lastVal['driverName']=$driver['driverLastName'].", ".$driver['driverFirstName']."&nbsp;<a href='/trucking/php/view/viewDriver.php?i=".$driver['driverId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
			}
		}else{
			$lastVal['driverName'] = "--";
		}
		


if($lastVal['invoiceId']=="--")
$edit="<a href='deleteTicket.php?i={$lastVal['ticketId']}' class='delete' ></a>
				<a href='/trucking/php/edit/editTicket.php?i={$lastVal['ticketId']}' class='edit' ></a>";

$tbody="";

$tbody.="<div class='table' id='viewTicket'>
			<form id='formValidate' name='formValidate' method='POST' action='submitEditTicketInfo.php?i=".$lastVal['ticketId']."' >
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' >
				<tr>
						<th class='full' colspan='2'>View Ticket ".$lastVal['ticketId']." </th>
					</tr>
					<tr class='bg'>
						<td class='first' width='172'><strong>Ticket Number:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
							 {$lastVal['ticketMfi']}
						</td>
					</tr>
					<tr class='bg'>
						<td class='first'><strong>Ticket Dump:</strong><span style='color:red;'>*</span></td>
						<td class='last'><input type='text' class='text' id='ticketNumber' name='ticketNumber' value='";
						if(isset($lastVal['ticketNumber']))
							$tbody.=$lastVal['ticketNumber'];
$tbody.="'/></td>
					</tr><tr>
						<td class='first' width='172'><strong>Project:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
						";
						if(isset($lastVal['projectId']))
						{
							$tbody.=$lastVal['projectId']." ".$lastVal['projectName'];
							$tbody.= "&nbsp;<a href='/trucking/php/view/viewJob.php?i=".$lastVal['projectId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
 
$tbody.="	</td>
					</tr>
					<tr class='bg'>
						<td class='first'><strong>Item #:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
						";
						if(isset($lastVal['itemId']))
						{
							$tbody.=$lastVal['itemNumber'];
							$tbody.="&nbsp;<a href='/trucking/php/view/viewItem.php?i=".$lastVal['itemId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						
$tbody.=" </td>
					</tr>
					";
					if(isset($lastVal['fromAddress']))
					{
						
					$tbody.="<tr>
						<td class='first' width='172'></td>
						<td class='last'>
						from: ".$lastVal['fromAddress']."
						</td>
					</tr>";
					}
					
					if(isset($lastVal['toAddress']))
					{
					$tbody.="<tr class='bg'>
						<td class='first' width='172'></td>
						<td class='last'>
						to: ".$lastVal['toAddress']."
						</td>
					</tr>";
					}
					
					if(isset($lastVal['materialName']))
					{
					$tbody.="<tr>
						<td class='first' width='172'></td>
						<td class='last'>
						".$lastVal['materialName']."
						</td>
					</tr>";
					}
$tbody.="	<tr class='bg'>
						<td class='first'><strong>Dump Ticket:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						if(isset($lastVal['ticketNumber'])&&$lastVal['ticketNumber']!=0)
						$tbody.= $lastVal['ticketNumber'];
						$tbody.="</td>
					</tr>
					<tr>
						<td class='first' width='172'><strong>Date:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						if(isset($lastVal['ticketDate']))
						$tbody.= decimalPad($lastVal['ticketDate']);
						$tbody.="</td>
					</tr>
					<tr class='bg'>
						<td class='first'><strong>Amount:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						if(isset($lastVal['ticketAmount']))
						$tbody.= decimalPad($lastVal['ticketAmount']).' '.$LTH[$lastVal['itemType']];
						$tbody.="</td>
					</tr>
					<tr>
						<td class='first' width='172'><strong>In Invoice:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						$tbody.= $lastVal['invoiceId'];
						$tbody.="</td>
					</tr>
					<tr>
						<td class='first' width='172'><strong>In Report:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						$tbody.= $lastVal['reportId'];
						$tbody.="</td>
					</tr>
					<tr>
						<td class='first' width='172'><strong>In Supplier Invoice:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						$tbody.= $lastVal['supplierInvoiceId'];
						$tbody.="</td>
					<tr>
						<td class='first' width='172'><strong>Broker:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						
						$queryState = "select * from broker";
						$states = mysql_query($queryState,$conexion);
						
						$tbody.="<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>
						<option >--Select Broker--</option>";
						
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['brokerId'])&& $lastVal['brokerId'] ==$state['brokerId'])
								$tbody.="<option selected='selected' value='{$state['brokerId']}'>{$state['brokerName']}</option>";
							else
								$tbody.="<option value='{$state['brokerId']}'>{$state['brokerName']}</option>";
						}
						$tbody.="</select>
						</td>
					</tr><tr>
						<td class='first' width='172'><strong>Truck:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						$queryState = "select * from truck where brokerId=".$lastVal['brokerId'];
						$states = mysql_query($queryState,$conexion);
						
						$tbody.="<select name='truckId' id='truckId' style='font-family:verdana;font-size:8pt'>
						<option >--Select Truck--</option>";
						
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['truckId'])&& $lastVal['truckId'] ==$state['truckId'])
								$tbody.="<option selected='selected' value='".$state['truckId']."' >".$state['truckNumber']."</option>";
							else
								$tbody.="<option value='".$state['truckId']."' >".$state['truckNumber']."</option>";
						}
					
					$tbody.="</select>
						</td>
					</tr><tr>
						<td class='first' width='172'><strong>Driver:</strong><span style='color:red;'>*</span></td>
						<td class='last'>";
						
						$queryState = "select * from driver where brokerId=".$lastVal['brokerId'];
						$states = mysql_query($queryState,$conexion);
						
						$tbody.="<select name='driverId' id='driverId' style='font-family:verdana;font-size:8pt'>
						<option >--Select Driver--</option>";
						
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['driverId'])&& $lastVal['driverId'] ==$state['driverId'])
								$tbody.="<option selected='selected' value='".$state['driverId']."' >".$state['driverLastName'].", ".$state['driverFirstName']."</option>";
							else
								$tbody.="<option value='".$state['driverId']."' >".$state['driverLastName'].", ".$state['driverFirstName']."</option>";
						}
						$tbody.="</select>
						</td>
					</tr>
					</table>";

				//if there is no report Associated to any broker or driver then they can be modified
				if(mysql_num_rows($Report)==0)
				{
				$tbody.="<table><tr>
				<td><input type='reset' value='Reset' ></td>
				<td><input type='submit' value='Submit' ></td>
				</tr>
				</table>";
				}
				$tbody.="</form>
	        <!--<p>&nbsp;</p>-->
			</div>";

$jsondata['table']=$tbody;
$jsondata['edit']=$edit;
$jsondata['query']=$queryLast;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

