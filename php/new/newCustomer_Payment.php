<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Cutomer Payment";
$description = "Create a customer payment and assign the cheque amount between multiple invoices. First select a customer from the dropdown menu. Type the number, amount and date of the cheque. Finally select the invoice amount which will be covered by the payment. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
var patternLetters = new RegExp(/ticket/);
patternLetters.compile(patternLetters);
var totalAmount = 0;

$(document).ready(function()
{	
	$('#descriptionDisplay').click(function(){
		$('#hiddenDescription').toggle();
	});
	
	$('.add-invoice').live('click',function(){
		var invoiceId = $(this).attr("invoiceId");
		var difference = parseFloat($('#customerChequeAmount').val()) - parseFloat(totalAmount);
		if(difference <= 0){
			alert("The check can not cover any more invoices.");
			return false;
		}
		var response = moveInvoice(invoiceId, difference);
		
		$(this).closest("tr").remove();
		
		/*var id = $(this).closest("tr").attr('id').replace(patternLetters,'');
		//console.log(id);
		if(id!=undefined){
			moveTicket(id);
			$(this).closest("tr").remove();
		}*/
	});
	
	$('.removable').live('click',function(){
		var toSubstract = $(this).closest("tr").find(".sumable").text();
		totalAmount = parseFloat(totalAmount) - parseFloat(toSubstract);
		updateAmount(totalAmount);
		$(this).closest("tr").remove();
	});
	
	$('#searchButton').click(function(){
		getInvoices();
	});
	
	$('#addButton').click(function(){
		addTickets();
	});
	
	$('#customerId').change(function(){
		var customer = this.value;
		getProjects(customer);
	});
});

function moveInvoice(invoiceId, maxAmount){
	var projectName = $("#projectName"+invoiceId).val();
	var invoiceDate = $("#invoiceDate"+invoiceId).val();
	var invoiceTotal = $("#total"+invoiceId).val();
	var invoicePaid = $("#paid"+invoiceId).val();
	var invoiceBalance = $("#balance"+invoiceId).val();
	var toPay = parseFloat($("#toPay"+invoiceId).val());
	if(toPay > maxAmount){
		toPay = parseFloat(maxAmount);
	}
	var afterBalance = parseFloat(invoiceBalance - toPay);
	
	var newRow = $("<tr invoiceId='"+invoiceId+"' toPayFrom='"+toPay+"' ></tr>").addClass("accountable");
	newRow.append("<td>"+invoiceId+"</td>");
	newRow.append("<td>"+projectName+"</td>");
	newRow.append("<td>"+invoiceDate+"</td>");
	newRow.append("<td>"+invoiceTotal+"</td>");
	newRow.append("<td>"+invoicePaid+"</td>");
	newRow.append("<td>"+invoiceBalance+"</td>");
	newRow.append("<td class='sumable'>"+toPay.toFixed(2)+"</td>");
	newRow.append("<td>"+afterBalance.toFixed(2)+"</td>");
	newRow.append("<td><img src='/trucking/img/118.png' class='removable' width='22px' height='20px' ></td>");
	
	totalAmount = parseFloat(toPay) + parseFloat(totalAmount);
	updateAmount(totalAmount);
	$("#paidInvoices").append(newRow);
}

function updateAmount(newAmount){
	$("#amountSum").text(newAmount.toFixed(2));
}

function getProjects(customer){
	$.ajax({
		type: "GET",
		url: "getProjects.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var projects=$('#projectId');
			projects.children().remove();
			projects.append("<option value='0' >--Select project--</option>");
			jQuery.each(obj, function(i,val){
				projects.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
	//getTickets();
}

function getInvoices(){
	var customerId = $('#customerId').val();
	var projectId = $('#projectId').val();
	var startDate = $('#startDate').val();
	var endDate = $('#endDate').val();
	var invoiced = $("input[name='inInvoice']:checked").val();
	$.ajax({
		type: "GET",
		url: "getCustomerInvoices.php",
		data: "customerId="+customerId+"&projectId="+projectId+"&startDate="+startDate+"&endDate="+endDate+"&invoiced="+invoiced+"&listed=all",
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#tickets > tbody:last').remove();
					$('#tickets').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function addInvoices(){
	if($('.accountable').length){
		var first = true;
		var invoiceList = "";
		$('.accountable').each(function(index, obj){
			if(first){ first = false;}
			else{ invoiceList += "-"; }
			
			var invoiceId = obj.getAttribute("invoiceId");
			var toPayThis = obj.getAttribute("toPayFrom");
			invoiceList += invoiceId+","+toPayThis;
			$("#hiddenInvoices").val(invoiceList)
		});
		console.log(invoiceList);
	}
}
/*
function addTickets(){
	console.log("adding tickets...");
	if($('.accountable').length){
		var first = true;
		var ticketList = "";
		$('.accountable').each(function(index, obj){
			if(first){ first = false;}
			else{ ticketList += "-"; }
			
			var ticketId = obj.getAttribute("ticketId");
			ticketList += ticketId;
		});
		console.log(ticketList);
		$("#hiddenTickets").val(ticketList);
		
	}
}*/

function validateForm(){
	
	addInvoices();
	if( $("#customerId").val() == 0){alert("Please select a customer"); return false;}
	if( $("#customerChequeNum").val() == "" ) { alert("Please type the cheque number"); return false; }
	if( $("#customerChequeAmount").val() == "" ) { alert("Please type the cheque amount"); return false; }
	if( $("#customerChequeDate").val() == "" ) { alert("Please type the payment date"); return false; }
	
	if( $("#hiddenInvoices").val() == "" ) { return confirm("Are you sure you want submit this payment without any invoices? This payment will be taken as a credit and can be used to pay future invoices."); }
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
		</div>
		<div id="center-column">
		
		
			<div class="top-bar">
				<a href="#" class="project"></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" id='descriptionDisplay' >Description</th>
					</tr>
					<tr style="display:none;" id='hiddenDescription'>
						<td class="first" width="172" ><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitCustomerSuperCheck.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="5">New Payment</th>
					</tr>
					<tr class='bg'>
						<td><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td><strong>Cheque #:</strong><span style="color:red;">*</span></td>
						<td><strong>Date:</strong><span style="color:red;">*</span></td>
						<td><strong>Amount:</strong><span style="color:red;">*</span></td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from customer order by customerName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Customer--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['customerId'])&&$_GET['customerId']==$term['customerId'])
										echo "<option selected='selected' value='{$term['customerId']}'>{$term['customerName']}</option>";
									else
										echo "<option value='{$term['customerId']}'>{$term['customerName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no customers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='customerChequeNum' name='customerChequeNum' /></td>
						<td><input type='text' size='10px' id='customerChequeDate' name='customerChequeDate' /></td>
						<td><input type='text' size='10px' id='customerChequeAmount' name='customerChequeAmount' /></td>
					</tr>
					<tr class='bg'>
						<td><strong>Project:</strong></td>
						<td colspan='2'><strong>Note</strong></td>
						<td><strong>Amount</strong></td>
					</tr>
					<tr>
						<td>
						<?
						
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select project--</option>";
						if(isset($_GET['customerId']) && $_GET['customerId']!= 0)
						{
								$queryTerm = "select * from project where customerId = ".$_GET['customerId']." order by projectName asc";
								$terms = mysql_query($queryTerm,$conexion);
								$countTerms = mysql_num_rows($terms);
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['projectId'])&&$_GET['projectId']==$term['projectId'])
										echo "<option selected='selected' value='{$term['projectId']}'>{$term['projectName']}</option>";
									else
										echo "<option value='{$term['projectId']}'>{$term['projectName']}</option>";
								}
						}
						echo "</select>";
						?>
						</td>
						<td colspan='2'><textarea id='customerChequeNote' name='customerChequeNote' cols='52' rows='1' ></textarea></td>
						<td id='amountSum' align='right'></td>
					</tr>
					<tr class='bg'>
						<td>Start date:</td>
						<td><input type='text' size='10px' id='startDate' name='startDate' /></td>
						<td>End date:</td>
						<td><input type='text' size='10px' id='endDate' name='endDate' /></td>
					</tr>
					<tr>
						<td ><button type='submit' id='submitButton' >Submit</button></td>
						
						<td>
							<!--<button type='button' id='addButton' >Submit</button>-->
							<!--<input type='radio' name='listedLimit' value='20' id='twenty' checked ><label for='twenty'>20</label><br/>
							<input type='radio' name='listedLimit' value='50' id='fifty' ><label for='fifty'>50</label><br/>
							<input type='radio' name='listedLimit' value='100' id='hundred' ><label for='hundred'>100</label><br/>
							<input type='radio' name='listedLimit' value='all' id='allListed' ><label for='allListed'>All</label><br/>-->
						</td>
						<td>
							<input type='radio' name='inInvoice' value='no' id='noInInvoice' checked ><label for='noInInvoice'>Not in invoice</label><br/>
							<input type='radio' name='inInvoice' value='yes' id='yesInInvoice' ><label for='yesInInvoice'>In invoice</label><br/>
							<input type='radio' name='inInvoice' value='all' id='allInInvoice' ><label for='allInInvoice'>All</label><br/>
						</td>
						<td><button type='button' id='searchButton' >Search</button><input type='hidden' id='hiddenInvoices' name='hiddenInvoices' /></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<div class='table'>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' id='paidInvoices'>
					<tr>
						<th class='first' >Invoice</th>
						<th>Project</th>
						<th>Date</th>
						<th>Total</th>
						<th>Paid</th>
						<th>Balance</th>
						<th>To Pay</th>
						<th>after Balance</th>
						<th class='last' >Remove</th>
					</tr>
				</table>
			</div>
			<div class='iframes' id='previewFrame' >
			</div>
			<div class='table'>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' id='tickets'>
					<thead>
					<tr>
						<th class='first'>Invoice</th>
						<th>Project</th>
						<th>Date</th>
						<th>Total</th>
						<th>Paid</th>
						<th>Balance</th>
						<th>To Pay</th>
						<th class='last'>Add</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td colspan='13'></td>
					</tr>
					</tbody>
				</table>
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
