<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Broker";
$description = "Add a new invoice. Invoices contain all the tickets already created in a range of time. Values marked with <span style='color:red;'>*</span> are mandatory.";

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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?echo$title." -".$subtitle;?></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" href="/trucking/img/favicon.ico" type="image/x-icon" />
	<style media="all" type="text/css">@import "../../css/longView.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
var preview=false;
var patternDate = new RegExp(/(\d+)\/(\d+)\/(\d+)/);
patternDate.compile(patternDate);

$(document).ready(function()
{
	$('#reports tr').live('dblclick',function(){
			var reportId = this.id;
			reportId = reportId.replace(/report/,'');
			
			var url = 'showReport.php?i='+reportId;
			var windowName = 'popUp';
			var windowSize = 'width=814,heigh=514,scrollbars=yes';
			window.open(url,windowName,windowSize);
			event.preventDefault();
	});
	
	$('#brokerId').change(function(){
	var broker=this.value;
			getReports();
		//getTrucks(broker);
		getDrivers(broker);
	});
	/*	
	$('#truckId').change(function(){
		var truck = this.value;
		getReports();
		
	});*/
	$('#driverId').change(function(){
		getReports();
	});
	
	$('#afterDate').change(function(){getReports();});
	$('#beforeDate').change(function(){getReports();});
	$('#beforeEndDate').change(function(){getReports();});
	$('#invoiceWeek').change(function(){getReports();});
	$('#paid').change(function(){getReports();});
	
	$('.payable').live('click', function(){
		var reportId = $(this).attr('relHref');
		goToPaymentWithMetaData(reportId);
	});
	
	$('.managable').live('click', function(){
		var reportId = $(this).attr("reportId");
		goToWithMetaData(reportId, "managePayCheque");
	});
	
	<? if(isset($_GET['brokerId']) && $_GET['brokerId']!=0)echo "getReports();console.log('getting reports');";?>
	
});

function deleteReport(reportId){
	$.ajax({
		type: "GET",
		url: "deleteReport.php",
		data: "reportId="+reportId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
				$("#report"+obj).remove();
		},
		async: false
	});
}

function goToWithMetaData(reportId, url){
	console.log("meta");
	var brokerId=$('#brokerId').val();
	var driverId=$('#driverId').val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var beforeEndDate=$('#beforeEndDate').val();
	var invoiceWeek=$('#invoiceWeek').val();
	var paid=$('#paid').val();
	
	window.location = url+".php?reportId="+reportId+"&brokerId="+brokerId+"&driverId="+driverId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&paid="+paid;
}

function goToPaymentWithMetaData(reportId){
	console.log("meta");
	var brokerId=$('#brokerId').val();
	var driverId=$('#driverId').val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var beforeEndDate=$('#beforeEndDate').val();
	var invoiceWeek=$('#invoiceWeek').val();
	var paid=$('#paid').val();
	
	window.location = "newPaycheque.php?reportId="+reportId+"&brokerId="+brokerId+"&driverId="+driverId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&paid="+paid;
}

function getDriver(truck){
	$.ajax({
		type: "GET",
		url: "getDriver.php",
		data: "truckId="+truck,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			$("#driverId").val(obj.driverId); 
		},
		async: false
	});
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


function getReports(){
	var brokerId=$('#brokerId').val();
	var driverId=$('#driverId').val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var beforeEndDate=$('#beforeEndDate').val();
	var invoiceWeek=$('#invoiceWeek').val();
	var paid=$('#paid').val();
	
	afterDate=evalDate(afterDate);
	beforeDate=evalDate(beforeDate);
	beforeEndDate=evalDate(beforeEndDate);
	
	$.ajax({
		type: "GET",
		url: "getReports.php",
		data: "afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&brokerId="+brokerId+"&driverId="+driverId+"&paid="+paid,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#reportsT > tbody:last').remove();
					$('#reportsT').append(obj.table);
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

function evalDate(date){
		if(date.match(patternDate)){
			date=date.replace(patternDate,'$3-$1-$2');
			return date;
		}else{return '0';}
}

function validateForm(){
	
	if(!preview){
		if(!confirm("Are you sure you don't want to see the preview?")){
			return false;
		}
	}
	
	if(document.getElementById('projectId').selectedIndex==0 ){
		alert("Please select a project for this invoice");
			document.formValidate.projectId.focus;
			return false;
	}
	
	if(document.getElementById('invoiceStartDate').value.length==0){
		alert("Please type a starting date");
			document.formValidate.invoiceStartDate.focus;
			return false;
	}
	
	if(document.getElementById('invoiceEndDate').value.length==0){
		alert("Please select and end date");
			document.formValidate.invoiceEndDate.focus;
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
				<a href="reportBroker [Invoices].php" class="returnLink"><img src='/trucking/img/48.png' /></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">Invoices Report</th>
					</tr>
					<tr class='bg'>
						<td>Broker</td>
						<td>After</td>
						<td>Before</td>
						<td>Before End Date</td>
						<td>Week</td>
						<td>Paid/Unpaid</td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from broker where brokerStatus=1 order by brokerName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Broker--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['brokerId'])&&$_GET['brokerId']==$term['brokerId'])
										echo "<option selected='selected' value='{$term['brokerId']}'>{$term['brokerName']}</option>";
									else
										echo "<option value='{$term['brokerId']}'>{$term['brokerName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no brokers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						
							<?/*
						echo "<select name='truckId' id='truckId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Truck--</option>";
						if(isset($_GET['b'])){
								$queryTruck = "SELECT * FROM truck WHERE brokerId=".$_GET['b'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if(isset($_GET['i'])&&$_GET['i']==$truck['itemId'])
										echo "<option selected='selected' value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
									else
										echo "<option value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
								}
						}
						
						echo "</select>";*/
						?>
						
						<?
						echo "<select name='driverId' id='driverId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Driver--</option>";
						if(isset($_GET['brokerId'])){
								$queryTruck = "SELECT * FROM driver WHERE brokerId=".$_GET['brokerId'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if(isset($_GET['driverId'])&&$_GET['driverId']==$truck['driverId'])
										echo "<option selected='selected' value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
									else
										echo "<option value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='afterDate' name='afterDate' value='<? echo (isset($_GET['afterDate'])? $_GET['afterDate']:"");?>' /></td>
						<td><input type='text' size='10px' id='beforeDate' name='beforeDate' /></td>
						<td><input type='text' size='10px' id='beforeEndDate' name='beforeEndDate' /></td>
						<td><input type='text' size='10px' id='invoiceWeek' name='invoiceWeek' /></td>
						<td>
							<select name='paid' id='paid' style='font-family:verdana;font-size:8pt' >
								<option value='0' >All</option>
								<option value='1' >Paid</option>
								<option value='2' >Unpaid</option>
							</select>
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='table' id='reports' >
			<?
		$queryInvoices= "
			SELECT 
				*, 
				if(driverId is null, broker.termId, driver.termId) as reportTermId 
			FROM 
				report 
				JOIN broker using (brokerId) 
				LEFT JOIN driver ON (report.reportType = driver.driverId)
				JOIN term ON  (term.termId = if(driverId is null, broker.termId, driver.termId))
			ORDER BY 
				reportEndDate DESC
				
				Limit 40";
				$invoices = mysql_query($queryInvoices,$conexion);
				$numInvoices = mysql_num_rows($invoices);
		
				if($numInvoices>0){
					echo "
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
						<table id='reportsT' class='listing form'  cellpadding='0' cellspacing='0'>
							<tbody>
							<tr>
								<th class='first' width='5%'>Report Number</th>
								<th width='5%'>PID</th>
								<th width='20%'>Driver Name</th>
								<th width='10%'>Creationg Date</th>
								<th width='10%'>Start </th>
								<th width='10%'>End</th>
								<th width='9%'>DueDate</th>
								<th width='9%'>Bill Total</th>
								<th width='9%'>Total Paid</th>
								<th width='9%'>To Pay</th>
								<th class='last' width='8%' colspan='3'></th>
								
							</tr>
							</tbody>
							<tbody>
						";
						
					$colorFlag=true;
					$paid='Unpaid';
							
					while($invoice = mysql_fetch_assoc($invoices) ){
						$queryTotal = "
							SELECT
								SUM( (ticketBrokerAmount * itemBrokerCost) * (if(item.itemDescription like 'toll%', 100, if(driver.driverId is null, broker.brokerPercentage, driver.driverPercentage ) ) )/100 ) as totalReported
							FROM
								reportticket
								JOIN report using (reportId)
								JOIN ticket using (ticketId)
								JOIN item using (itemId)
								JOIN broker using (brokerId)
								LEFT JOIN driver on (driver.driverId = report.reportType)
							WHERE
								reportId = ".$invoice['reportId']."
						";
						
						$totalInfo = mysql_fetch_assoc(mysql_query($queryTotal,$conexion));
						
						$paidTotal = "
							SELECT
								SUM(paidchequesAmount) as totalPaid,
								COUNT(*) as number
							FROM
								paidcheques
							WHERE
								reportId = ".$invoice['reportId']."
						";
						
						$paidInfo = mysql_fetch_assoc(mysql_query($paidTotal, $conexion));
						//$termInfo = mysql_fetch_assoc(mysql_query("select * from term where termId = ".($invoice['driverId']==null?$invoice['']),$conexion));
						
						//$percentage = ($invoice['driverId']==null?$invoice['brokerPercentage']:$invoice['driverPercentage'])/100;
						
						$paidTotal = decimalPad($paidInfo['totalPaid'] == null ? 0 : $paidInfo['totalPaid'], 2);
						$chequesTotal = $paidInfo['number'];
						$reportTotal = decimalPad(($totalInfo['totalReported'] == null ? 0 : $totalInfo['totalReported']),2);
						
						if($paidTotal == null || $paidTotal <= 0 || $chequesTotal == 0 ) $paid = 'Unpaid';
						if($paidTotal != null && $paidTotal >= $reportTotal && $chequesTotal != 0) $paid = 'Paid';
						if($paidTotal != null && $paidTotal > 0 && $paidTotal < $reportTotal) $paid = 'Warning';
						if($paidTotal != null && $paidTotal > $reportTotal) $paid = 'Overpaid';
						
						if($colorFlag){echo "<tr class='even".$paid."' id='report".$invoice['reportId']."'>";}
						
						else{echo "<tr class='odd".$paid."' id='report".$invoice['reportId']."'>";}
						
						$colorFlag=!$colorFlag;
						
						
						echo "<td width='6%'>".$invoice['reportId']."</td>";
						echo "<td width='5%'>".$invoice['brokerPid']."</td>";
						echo "<td width='20%'>".($invoice['driverFirstName']==null?'----':$invoice['driverLastName'].", ".$invoice['driverFirstName'])."</td>";
						echo "<td width='10%'>".to_MDY($invoice['reportDate'])."</td>";
						echo "<td width='10%'>".to_MDY($invoice['reportStartDate'])."</td>";
						echo "<td width='10%'>".to_MDY($invoice['reportEndDate'])."</td>";
						echo "<td width='9%' class='number' > ".(date('m/d/Y', strtotime('+'.$invoice['termValue'].' days', strtotime($invoice['reportEndDate']))))."</td>";
						echo "<td width='9%' class='number' >$ ".decimalPad( $reportTotal )."</td>";
						//echo "<td width='9%' class='number' > ".($projectInfo3['reportCheck']==null?(date('m/d/Y', strtotime('+'.$termDays.' days', strtotime($invoice['reportEndDate'])))):$projectInfo3['reportCheck'])."</td>";
						echo "<td width='9%' class='number' >$ ".decimalPad( $paidTotal )."</td>";
						echo "<td width='9%' class='number' >$ ".decimalPad( $reportTotal - $paidTotal )."</td>";
						
						echo "<td width='8%' class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' relHref='".$invoice['reportId']."' /></td>";
						if($paid == "Unpaid" )
						{
						echo "<td class='number' ><a onclick=\"return confirm('Are you sure you want to delete Invoice #".$invoice['reportId']."?');\" href='deleteReport.php?reportId=".$invoice['reportId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>";
						}
						else
						{
						echo "<td class='number'></td>";
						}
						echo "<td><img src='/trucking/img/2.png' width='22' height='22' class='managable' reportId='".$invoice['reportId']."' /></td>";
						echo "</tr>";
						
					}
				}
				
				echo "</tbody></table>";
			?>
			</div>
			<?
			?>
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
