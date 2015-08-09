<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Ticket";

###############News section###############
$queryNews =
	"SELECT 
		*
	FROM
		news
	ORDER BY
		newsDate desc
	LIMIT
		1";
$news = mysql_query($queryNews,$conexion);
$lastNew = mysql_fetch_assoc($news);
$lastNew = $lastNew["newsComment"]." -".to_MDY($lastNew["newsDate"]);
##########################################

$description = "Double click on the 'MF Ticket' column to see aditional information about the ticket";

if(isset($_GET['i']))
{
###############Show Item###############
$queryLast =
	"SELECT
	*
FROM
	ticket
JOIN item using (itemId)
JOIN project using (projectId)
JOIN material using (materialId)
WHERE
		ticketId=".$_GET['i'];
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
		
		$uSInvoiceQuery = "SELECT * FROM supplierinvoiceticket join supplierinvoice using (supplierInvoiceId) where ticketId=".$lastVal['ticketId'];
		 $snvoiceR = mysql_query($uSInvoiceQuery,$conexion);
		 if(mysql_num_rows($snvoiceR)==0)$lastVal['supplierInvoiceId']="--";
		else{
			$usnv = mysql_fetch_assoc($snvoiceR);
			$lastVal['supplierInvoiceId']=$usnv['supplierInvoiceId'];
			$lastVal['supplierInvoiceNumber']=$usnv['supplierInvoiceNumber'];
		}
		
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
}
$queryTickets ="
SELECT
	*
FROM
	ticket
ORDER BY ticketId DESC
LIMIT 102
";

$tickets = mysql_query($queryTickets,$conexion);
$numTickets = mysql_num_rows($tickets);
##########################################


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?echo$title." -".$subtitle;?></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" href="/trucking/img/favicon.ico" type="image/x-icon" />
	<style media="all" type="text/css">@import "../../css/all.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
var preview=false;
var patternLetters = new RegExp(/ticket/);
patternLetters.compile(patternLetters);

var patternDate = new RegExp(/(\d+)\/(\d+)\/(\d+)/);
patternDate.compile(patternDate);

$(document).ready(function()
{
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this ticket?');
	});
	$('#tickets tr td').live('dblclick',function(){
			var ticketId=$(this).attr('id');
			if(ticketId!=undefined){
				ticketId=ticketId.replace(patternLetters,'');
				location.href = 'viewTicket.php?i='+ticketId;
				//getTicket(ticketId);
			}
			
	});
	
	
	$('#brokerId').live('change',function() {
		var broker=this.value;
		getTrucks(broker);
		getDrivers(broker);
	});
	
	$('#projectId').change(function(){
			getTickets();
	});
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	$('#afterDate').change(function(){getTickets();});
	$('#beforeDate').change(function(){getTickets();});
	$('#ticketMFI').change(function(){getTickets();});
	$('#ticketDump').change(function(){getTickets();});
	$('#invoice').change(function(){getTickets();});

});

function evalDate(date){
		if(date.match(patternDate)){
			date=date.replace(patternDate,'$3-$1-$2');
			return date;
		}else{return '0';}
}

function getDrivers(broker){
	$.ajax({
		type: "GET",
		url: "getDrivers.php",
		data: "brokerId="+broker,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#driverId');
			material.children().remove();
			material.append("<option value='0' >--Select Driver--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function getTrucks(broker){
	$.ajax({
		type: "GET",
		url: "getTrucks.php",
		data: "brokerId="+broker,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#truckId');
			material.children().remove();
			material.append("<option value='0' >--Select Truck--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}
function getProjects(customer){
	$.ajax({
		type: "GET",
		url: "getProjects.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#projectId');
			material.children().remove();
			material.append("<option value='0' >--Select Project--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function getTicket(ticketId){
	$.ajax({
		type: "GET",
		url: "getTicket.php",
		data: "ticketId="+ticketId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				var count = $('.top-bar > a').length;
					if(count>1){
						$('.top-bar > a:last').remove();
						$('.top-bar > a:last').remove();
					}
				if(obj.table!=null){
					if($('#viewTicket').length==0){
						$('.top-bar').after(obj.table);
					}else{
						$('#viewTicket').replaceWith(obj.table);
					}
				}
				if(obj.edit!=null){
					
					$('.top-bar').append(obj.edit);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function getTickets(){
	var projectId=$('#projectId').val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var ticketMFI=$('#ticketMFI').val();
	var ticketDump=$('#ticketDump').val();
	var invoice=$('#invoice').val();
	
	afterDate=evalDate(afterDate);
	beforeDate=evalDate(beforeDate);
	
	$.ajax({
		type: "GET",
		url: "getTickets.php",
		data: "afterDate="+afterDate+"&beforeDate="+beforeDate+"&ticketMFI="+ticketMFI+"&ticketDump="+ticketDump+"&projectId="+projectId+"&invoice="+invoice,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#tickets > tbody:last').remove();
					$('#tickets').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
			//material.children().remove();
			//material.append("<option value='0' >--Select Item--</option>");
			/*jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});*/
		},
		async: false
	});
}

function validateForm(){

	if(document.getElementById('customerName').value.length==0){
		alert("Please type a name for the customer");
		document.formValidate.customerName.focus
		return false;
	}
	if(document.getElementById('termId').selectedIndex==0 ){
		alert("Please select a payment term");
			document.formValidate.termId.focus
			return false;
	}
	return true;
}
</script>
<body>
<div id="main">
	<div id="header">
		<a href="/trucking/index.php" class="logo"><img src="/trucking/img/logo.gif" width="118" height="62" alt="" /></a>
		<a href="/trucking/php/logout.php" class="logout">Logout</a>
		<ul id="top-navigation">
		<?
		echo "<li><span><span><a href='../../index.php'>Homepage</a></span></span></li>";
			$results = "../*";
			foreach(glob($results) as $result)
			{
				if(file_exists("./".$result."/menu.php"))
				{
					$name=ucfirst(substr($result,strpos($result,'/')+1));
					if($name==$title)
						echo "<li class='active'><span><span><a href='$result/menu.php'>".$name." Menu</a></span></span></li>" ;
					else
						echo "<li><span><span><a href='$result/menu.php'>".$name." Menu</a></span></span></li>" ;
				}
			}
			
			echo "</ul>";
		?>
		</ul>
		<!--<ul id="top-navigation">
			<li class="active"><span><span>Homepage</span></span></li>
			<li><span><span><a href="#">Users</a></span></span></li>
			<li><span><span><a href="#">Orders</a></span></span></li>
			<li><span><span><a href="#">Settings</a></span></span></li>
			<li><span><span><a href="#">Statistics</a></span></span></li>
			<li><span><span><a href="#">Design</a></span></span></li>
			<li><span><span><a href="#">Contents</a></span></span></li>
		</ul>-->
	</div>
	<div id="middle">
		<div id="left-column">
		<?
		echo "<h3>".$title."</h3>";
		echo "<ul class='nav'>";
		$forms = "./*";
		foreach(glob($forms) as $form)
		{
			$formName = ucfirst(substr($form,strpos($form,'/')+1));
			if(startsWith($formName,$title)==true)
			{
				echo "<li><a href='".$form."'>".str_replace(".php",'',str_replace($title,'',$formName))."</a></li>";
			}
		}
		echo "</ul>";
		?>
			<!--<h3>Header</h3>
			<ul class="nav">
				<li><a href="#">Lorem Ipsum dollar</a></li>
				<li><a href="#">Dollar</a></li>
				<li><a href="#">Lorem dollar</a></li>
				<li><a href="#">Ipsum dollar</a></li>
				<li><a href="#">Lorem Ipsum dollar</a></li>
				<li class="last"><a href="#">Dollar Lorem Ipsum</a></li>
			</ul>-->
		</div>
		
		<div id="center-column">
		
		
			<div class="top-bar">
				
				<a href="#" class="project"></a>
				<?
					if(isset($_GET['i'])&& $lastVal['invoiceId']=="--")
					{ 
						?>
				<a href="deleteTicket.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editTicket.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				<?
					}
					
					if(isset($_GET['i']))
					{ 
						?>
				<a href="/trucking/php/edit/editTicket.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				<?
					}
				?>
			</div><br />
		<?
		if(isset($_GET['i']))
		{
			
			
		/*LEFT JOIN invoiceticket using (ticketId)
		 * 
		 */
		?>	
			<div class="table" id='viewTicket'>
			<form id="formValidate" name="formValidate" method="POST" action="submitEditItem.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
				<tr>
						<th class="full" colspan="2">View Ticket</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Ticket Number:</strong><span style="color:red;">*</span></td>
						<td class="last">
							 <?if(isset($lastVal['ticketMfi']))echo$lastVal['ticketMfi'];?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Project:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['projectId']))
						{
							echo "<strong>".$lastVal['projectId']."</strong> ".$lastVal['projectName'];
							echo "&nbsp;<a href='/trucking/php/view/viewJob.php?i=".$lastVal['projectId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Broker:</strong><span style="color:red;">*</span></td>
						<td class="last">
							 <?if(isset($lastVal['truckId'])){
								 $truckInfo = mysql_query("select * from truck join broker using (brokerId) where truckId = ".$lastVal['truckId']);
								 $truckArr = mysql_fetch_assoc($truckInfo);
								 echo$truckArr['brokerName']." <b>".$truckArr['brokerPid']."-".$truckArr['truckNumber']."</b>";
								 
								 }?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Item #:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['itemId']))
						{
							echo "<strong>".$lastVal['itemNumber']."</strong>";
							echo "&nbsp;<a href='/trucking/php/view/viewItem.php?i=".$lastVal['itemId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</td>
					</tr>
					<?if(isset($lastVal['fromAddress']))
					{
						?>
					<tr>
						<td class="first" width="172"></td>
						<td class="last">
						<?echo "from: ".$lastVal['fromAddress'];?>
						</td>
					</tr>
					<?
					}
					?>
					<?if(isset($lastVal['toAddress']))
					{
						?>
					<tr class='bg'>
						<td class="first" width="172"></td>
						<td class="last">
						<?echo "to: ".$lastVal['toAddress'];?>
						</td>
					</tr>
					<?
					}
					?>
					<?if(isset($lastVal['materialName']))
					{
						?>
					<tr>
						<td class="first" width="172"></td>
						<td class="last">
						<?echo $lastVal['materialName'];?>
						</td>
					</tr>
					<?
					}
					?>
					<tr class="bg">
						<td class="first"><strong>Dump Ticket:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['ticketNumber'])&&$lastVal['ticketNumber']!=0)echo $lastVal['ticketNumber'];?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Date:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['ticketDate']))echo to_MDY($lastVal['ticketDate']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Customer Amount:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['ticketAmount']))echo decimalPad($lastVal['ticketAmount'])." ".$LTH[$lastVal['itemType']];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Broker Amount:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['ticketBrokerAmount']))echo decimalPad($lastVal['ticketBrokerAmount'])." ".$LTH[$lastVal['itemType']];?></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>In Invoice:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['invoiceId'])&& $lastVal['invoiceId']!=null)echo $lastVal['invoiceId'];else echo"--";?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>In Broker Report:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['reportId'])&& $lastVal['reportId']!=null)echo $lastVal['reportId'];else echo"--";?></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>In Supplier Invoice:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<?
								if(isset($lastVal['supplierInvoiceId'])&& $lastVal['supplierInvoiceId']!=null && $lastVal['supplierInvoiceId']!="--" ){
									$supplierInfo = mysql_fetch_assoc(mysql_query("select * from supplierinvoice join supplier using (supplierId) where supplierInvoiceId = ".$lastVal['supplierInvoiceId']));
									echo $supplierInfo['supplierInvoiceNumber']. " for <b>".$supplierInfo['supplierName']."</b>";
								}else{
									echo"--";
								}?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Driver:</strong><span style="color:red;">*</span></td>
						<td class="last"><?echo $lastVal['driverName'];?></td>
					</tr>
					</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
		<?
		}
		?>	
			<div class='table' id='searchBar'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
				<tr>
					<th class="full"  colspan='7'>Tickets</th>
				</tr>
				<tr>
					<td >Project</td>
					<td >After Date</td>
					<td >Before Date</td>
					<td >MFI Ticket</td>
					<td >Dump Ticket</td>
					<td >Show</td>
				</tr>
				<tr>
					<td>
					<?
						$queryTerm0 = "select * from customer order by customerName asc";
						$terms0 = mysql_query($queryTerm0,$conexion);
						$countTerms0= mysql_num_rows($terms0);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt;width:150px'>";
						if($countTerms0 > 0)
						{
							
							if(!isset($_GET['i']))
							{
								echo "<option selected='selected'>--Select Customer--</option>";
								while($term0=mysql_fetch_assoc($terms0))
								{
									echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
							else
							{	
								while($term0=mysql_fetch_assoc($terms0))
								{
									if($_GET['i']==$term0['customerId'])
										echo "<option selected='selected' value='{$term0['customerId']}'>{$term0['customerName']}</option>";
									else
										echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
						}
								else
						{
							echo "<option selected='selected'>There are no customers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
					<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
					</td>
					<td ><input type='text' size='4px' id='afterDate' name='afterDate' /></td>
					<td ><input type='text' size='4px' id='beforeDate' name='beforeDate' /></td>
					<td><input type='text' size='6px' id='ticketMFI' name='ticketMFI' /></td>
					<td ><input type='text' size='6px' id='ticketDump' name='ticketDump' /></td>
					<td>
							<select name='invoice' id='invoice' style='font-family:verdana;font-size:8pt' >
								<option value='0' >All</option>
								<option value='1' >in Invoice</option>
								<option value='2' >no Invoice</option>
							</select>
						</td>
				</tr>
				</table>
			</div>
			
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Tip</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>
			
			<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='tickets'>
				<tr>
					<th class="full"  colspan='10'>Tickets</th>
				</tr>
				<tr>
					<th width='6%'>Date</th>
					<th size='8px' width='6%'>MF Ticket</th>
					<th size='8px' width='12%'>Dump</th>
					<th width='6%'>Invoice</th>
					<!--<th width='6%'>Report</th>
					<th width='6%'>Supplier Invoice</th>-->
					<th width='6%'>Date</th>
					<th size='8px' width='6%'>MF Ticket</th>
					<th size='8px' width='12%'>Dump</th>
					<th width='6%' class='last'>Invoice</th>
					<!--<th width='6%'>Report</th>
					<th width='6% class='last'>Supplier Invoice</th>-->
				</tr>
				<tbody>
				<?
				$actual=0;
				$colorFlag=true;
				$tdClass="";
				if($numTickets>0)
				{
						while($ticket=mysql_fetch_assoc($tickets))
						{
							 $queryInvoice = "SELECT invoiceId FROM invoiceticket where ticketId=".$ticket['ticketId'];
							 $queryReport = "SELECT reportId FROM reportticket where ticketId=".$ticket['ticketId'];
							 $querySupplierInvoice = "SELECT supplierInvoiceId FROM supplierinvoiceticket where ticketId=".$ticket['ticketId'];
							 //echo $queryInvoice;
							 $invoice = mysql_query($queryInvoice,$conexion);
							 $report = mysql_query($queryReport,$conexion);
							 $supplierInvoice = mysql_query($querySupplierInvoice,$conexion);
							 if(mysql_num_rows($invoice)==0)$invoiceId="";
							 else{$inv = mysql_fetch_assoc($invoice);$invoiceId=$inv['invoiceId'];}
							 /*
							 if(mysql_num_rows($report)==0)$reportId="";
							 else{$rep = mysql_fetch_assoc($report);$reportId=$rep['reportId'];}
							 if(mysql_num_rows($supplierInvoice)==0)$snvoiceId="";
							 else{$snv = mysql_fetch_assoc($supplierInvoice);$snvoiceId=$snv['suppliernvoiceId'];}
							*/
							if($colorFlag){ $tdClass=""; }
							else{ $tdClass=" class='bg' "; }
								switch($actual){
									case 0:
									$colorFlag=!$colorFlag;
									echo "<tr>";
									echo "<td width='6%'".$tdClass." >".to_MDY($ticket['ticketDate'])."</td>";
									echo "<td size='8px' width='6%'".$tdClass." id='ticket".$ticket['ticketId']."' >".$ticket['ticketMfi']."</td>";
									echo "<td width='6%'".$tdClass." >".$ticket['ticketNumber']."</td>";
									echo "<td width='6%'".$tdClass." >".$invoiceId."</td>";
									//echo "<td width='6%'".$tdClass." >".$reportId."</td>";
									//echo "<td width='6%'".$tdClass." >".$snvoiceId."</td>";
									$actual++;
									break;
									case 1:
									echo "<td width='6%'".$tdClass." >".to_MDY($ticket['ticketDate'])."</td>";
									echo "<td size='8px' width='6%'".$tdClass." id='ticket".$ticket['ticketId']."' >".$ticket['ticketMfi']."</td>";
									echo "<td width='6%'".$tdClass." >".$ticket['ticketNumber']."</td>";
									echo "<td width='6%'".$tdClass." >".$invoiceId."</td>";
									//echo "<td width='6%'".$tdClass." >".$reportId."</td>";
									//echo "<td width='6% class='last' ".$tdClass." >".$snvoiceId."</td>";
									echo "</tr>";
									$actual=0;
									break;
								}
						}
						switch($actual){
							case 0:break;
							case 1:echo"<td ".$tdClass." colspan='7'></td></tr>";break;
							#case 2:echo"<td ".$tdClass." colspan='6'></td></tr>";break;
						}
				}
				?>
				</tbody>
			</table>
			</div>
		</div>
		
		<div id="right-column">
			<strong class="h">INFO</strong>
			<div class="box">
				<?
				echo $lastNew;
				?>
			</div>
	  </div>
	</div>
	<div id="footer"></div>
</div>


</body>
</html>
<?
mysql_close();
?>
