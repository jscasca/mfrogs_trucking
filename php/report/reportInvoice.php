<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

include '../commonData.php';

#################
$title = "Report";
#################
$subtitle = "Invoice";
$description = "
	Invoices Reports. This page shows a list of the last invoices created.<br/>
	Action:
	<ul>
		<li>Click the (<img src='/trucking/img/87.png' width='18px' />) green bill to pay the selected invoice</li>
		<li>Click the (<img src='/trucking/img/118.png' width='18px' />) red cross to delete the selected invoice</li>
		<li>Click the (<img src='/trucking/img/2.png' width='18px' />) pencil icon to manage the invoice payments (review and delete multiple payments)</li>
	</ul>  
	Color Code:
	<ul>
		<li>Red invoices are completely unpaid. This invoices can be deleted or paid</li>
		<li>Yellow invoices are partly paid. This invoices can not be deleted (Remove all the current payments to delete them)</li>
		<li>Green invoices are completely paid. This invoices can not be deleted or paid additional checks (Remove all the current payments to delete them)</li>
		<li>Blue invoices are over paid. This invoices can not be deleted or paid additional checks (Remove all the current payments to delete them)</li>
	</ul>
	You can use the search bar to further filter the invoices listed.";

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
	$(this).scrollLeft(<?echo$scrollLeftLongView;?>);
	
	$('#descriptionDisplay').click(function(){
		$('#hiddenDescription').toggle();
	});
	
	$('#invoices tr').live('dblclick',function(){
			var invoiceId = this.id;
			invoiceId = invoiceId.replace(/invoice/,'');
			
			var url = 'showInvoice.php?i='+invoiceId;
			var windowName = 'popUp';
			var windowSize = 'width=814,heigh=514,scrollbars=yes';
			window.open(url,windowName,windowSize);
			event.preventDefault();
	});
	
	$('#customerId').change(function() {
		var customer=this.value;
		getInvoices();
		getProjects(customer);
	});
	
	$('#projectId').change(function(){
			getInvoices();
	});
	
	$('#invoiceId').change(function(){getInvoices();});
	$('#afterDate').change(function(){getInvoices();});
	$('#beforeDate').change(function(){getInvoices();});
	$('#invoiceWeek').change(function(){getInvoices();});
	$('#paid').change(function(){getInvoices();});
	
	$('.deletable').live('click',function(){
		var invoiceId = $(this).attr("invoiceId");
		var invoiceNumber = $(this).attr("invoiceNumber");
		
		if(window.confirm("Are you sure you want to delete invoice "+invoiceNumber)){
			deleteInvoice(invoiceId);
		}
	});
	
	$('.payable').live('click', function(){
		var invoiceId = $(this).attr("invoiceId");
		goToWithMetaData(invoiceId, "newReceiptcheque");
	});
	
	$('.managable').live('click',function(){
		var invoiceId = $(this).attr("invoiceId");
		goToWithMetaData(invoiceId, "manageReceiptCheque");
	});
	
	<? if(isset($_GET['customerId']))echo "getInvoices();console.log('getting reports');";?>
	
});

function deleteInvoice(invoiceId){
	$.ajax({
		type: "GET",
		url: "deleteInvoice.php",
		data: "invoiceId="+invoiceId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
				$("#invoice"+obj).remove();
		},
		async: false
	});
}

function goToWithMetaData(invoiceId, url){
	console.log("meta");
	var customerId=$('#customerId').val();
	var projectId=$('#projectId').val();
	var invoiceNum = $("#invoiceId").val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var invoiceWeek=$('#invoiceWeek').val();
	var paid=$('#paid').val();
	
	//window.location = "newPaycheque.php?reportId="+reportId+"&brokerId="+brokerId+"&driverId="+driverId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&paid="+paid;
	window.location = url+".php?invoiceId="+invoiceId+"&customerId="+customerId+"&projectId="+projectId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&week="+invoiceWeek+"&paid="+paid+"&invoiceNum="+invoiceNum;
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
function getInvoices(){
	var customerId=$('#customerId').val();
	var projectId=$('#projectId').val();
	var invoiceId=$('#invoiceId').val();
	var afterDate=$('#afterDate').val();
	var beforeDate=$('#beforeDate').val();
	var invoiceWeek=$('#invoiceWeek').val();
	var paid=$('#paid').val();
	
	afterDate=evalDate(afterDate);
	beforeDate=evalDate(beforeDate);
	
	$.ajax({
		type: "GET",
		url: "getInvoices.php",
		data: "afterDate="+afterDate+"&beforeDate="+beforeDate+"&week="+invoiceWeek+"&projectId="+projectId+"&customerId="+customerId+"&invoiceId="+invoiceId+"&paid="+paid,
		success:function(data){
			if(data == null) $('#invoices > tbody:last').remove();
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#invoices > tbody:last').remove();
					$('#invoices').append(obj.table);
				}else $('#invoices > tbody:last').remove();
			}else{alert('Error: '+obj.error);$('#invoices > tbody:last').remove();}
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
				<a href="reportInvoice.php" class="returnLink"><img src='/trucking/img/48.png' /></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" id='descriptionDisplay' >Description</th>
					</tr>
					<tr style="display:none;" id='hiddenDescription'>
						<td class="last" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="7">Invoices Report</th>
					</tr>
					<tr class='bg'>
						<td>Project</td>
						<td>Invoice #</td>
						<td>From</td>
						<td>To</td>
						<td>Week</td>
						<td>Paid/Unpaid</td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm0 = "select * from customer order by customerName asc";
						$terms0 = mysql_query($queryTerm0,$conexion);
						$countTerms0= mysql_num_rows($terms0);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms0 > 0)
						{
							
							if(!isset($_GET['customerId']))
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
									if($_GET['customerId']==$term0['customerId'])
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
						echo "<option selected='selected'>--Select Project--</option>";
						if(isset($_GET['customerId']) && $_GET['customerId']!=0){
							$projects = mysql_query("select * from project where customerId = ".$_GET['customerId'], $conexion);
							while($project = mysql_fetch_assoc($projects)){
								if(isset($_GET['projectId']) && $_GET['projectId']==$project['projectId']){
									echo "<option value='".$project['projectId']."' selected='selected' >".$project['projectName']."</option>";
								}else
									echo "<option value='".$project['projectId']."'>".$project['projectName']."</option>";
							}
						}else
							echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='invoiceId' name='invoiceId' <?if(isset($_GET['invoiceNum']) && $_GET['invoiceNum']!="")echo "value='".$_GET['invoiceNum']."'";?> /></td>
						<td><input type='text' size='10px' id='afterDate' name='afterDate' <?if(isset($_GET['afterDate']) && $_GET['afterDate']!="")echo "value='".$_GET['afterDate']."'";?> /></td>
						<td><input type='text' size='10px' id='beforeDate' name='beforeDate' <?if(isset($_GET['beforeDate']) && $_GET['beforeDate']!="")echo "value='".$_GET['beforeDate']."'";?> /></td>
						<td><input type='text' size='10px' id='invoiceWeek' name='invoiceWeek' <?if(isset($_GET['week']) && $_GET['week']!="")echo "value='".$_GET['week']."'";?> /></td>
						<td>
							<select name='paid' id='paid' style='font-family:verdana;font-size:8pt' >
								<option value='0' <?if(isset($_GET['paid']) && $_GET['paid']==0)echo"selected='selected' ";?> >All</option>
								<option value='1' <?if(isset($_GET['paid']) && $_GET['paid']==1)echo"selected='selected' ";?> >Paid</option>
								<option value='2' <?if(isset($_GET['paid']) && $_GET['paid']==2)echo"selected='selected' ";?> >Unpaid</option>
							</select>
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='table' id='reports' >
			<?
				$queryInvoices = "
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
					JOIN project ON (project.projectId = invoice.projectId)
				GROUP BY 
					invoiceId
				ORDER BY
					invoiceId DESC
				LIMIT 50
				";
				/*
				$queryInvoices = "
				SELECT 
					invoiceId,
					projectId,
					projectName,
					invoiceDate,
					invoiceStartDate,
					invoiceEndDate,
					invoicePaid,
					sum(itemCustomerCost*ticketAmount) as invoiceTotal
				FROM 
					invoice
				LEFT JOIN invoiceticket using (invoiceId)
				LEFT JOIN ticket using (ticketId)
				JOIN project using (projectId)
				LEFT JOIN (select itemId, itemCustomerCost from item) as I using (itemId)
				LEFT JOIN invoicepaid using (invoiceId)
				GROUP BY 
					invoiceId
				ORDER BY
					invoiceId DESC
				";*/
				$invoices = mysql_query($queryInvoices,$conexion);
				$numInvoices = mysql_num_rows($invoices);
				if($numInvoices>0){
					echo "
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
						<table id='invoices' class='listing form'  cellpadding='0' cellspacing='0'>
							<tr>
								<th class='first' width='7%'>Job Id</th>
								<th width='7%'>Invoice</th>
								<th width=10>Job Name</th>
								<th width='9%'>Date</th>
								<th width='9%'>Start </th>
								<th width='9%'>End</th>
								<th width=20>Total</th>
								<th width=24>Received</th>
								<th width=15>Balance</th>
								<th class='last' width='8%' colspan='2'></th>
							</tr>
							<tbody>
						";	
					$colorFlag=true;
						while($invoice = mysql_fetch_assoc($invoices)){
							$paid='Unpaid';
							$queryPaid="select SUM( receiptchequesAmount ) AS chequetotal, COUNT(*) as number from receiptcheques where invoiceId=".$invoice['invoiceId'];
							$paidReg=mysql_query($queryPaid,$conexion);
							$paidInfo = mysql_fetch_assoc($paidReg);
							
							$paidTotal = decimalPad($paidInfo['chequetotal']==null?0:$paidInfo['chequetotal']);
							$cheques = $paidInfo['number'];
							$invoiceTotal = decimalPad($invoice['invoiceTotal']==null?0:$invoice['invoiceTotal']);
							$invoiceBalance = decimalFill( decimalPad($invoiceTotal - $paidTotal));
							
							if($paidTotal > 0 && $paidTotal >= $invoiceTotal ) {$paid='Paid';}
							else if($paidTotal>0 && $paidTotal < $invoiceTotal ) {$paid='Warning';}
							else if($paidTotal == NULL ) {$paid='Unpaid';}
							
							if($colorFlag){echo "<tr class='even".$paid."' id='invoice".$invoice['invoiceId']."'>";}
							else{echo "<tr class='odd".$paid."' id='invoice".$invoice['invoiceId']."'>";}
							$colorFlag=!$colorFlag;
							
							echo "<td class='first style2' width='7%'>".$invoice['projectId']."</td>";
							echo "<td class='first style2' width='7%'>".$invoice['invoiceId']."</td>";
							echo "<td width=10>".$invoice['projectName']."</td>";
							echo "<td width='9%'>".to_MDY($invoice['invoiceDate'])."</td>";
							echo "<td width='9%'>".to_MDY($invoice['invoiceStartDate'])."</td>";
							echo "<td width='9%'>".to_MDY($invoice['invoiceEndDate'])."</td>";
							echo "<td width=20 class='number' >$ ".$invoiceTotal."</td>";
							echo "<td width=24 class='number' >$ ".$paidTotal."</td>";
							echo "<td width=15 class='number' >$ ".$invoiceBalance."</td>";
							//echo "<td class='number' ><a href='newReceiptcheque.php?invoiceId=".$invoice['invoiceId']."'><img src='/trucking/img/87.png' width='24' height='22' /></a></td>";
							
							if($paid == 'Unpaid' || $paid == 'Warning') echo "<td class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' invoiceId='".$invoice['invoiceId']."' /></td>";
							else echo "<td></td>";
							
							if($paid == 'Unpaid')echo "<td><img src='/trucking/img/118.png' width='20' height='20' class='deletable' invoiceId='".$invoice['invoiceId']."' invoiceNumber='".$invoice['invoiceId']."' /></td>";
							else echo "<td><img src='/trucking/img/2.png' width='24' height='22' class='managable' invoiceId='".$invoice['invoiceId']."' /></td>";
							
							echo "</tr>";
							
						}echo "</tbody></table>";
				}
			?>
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
