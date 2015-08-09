<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Cutomer Payment";

if(isset($_GET['customerSuperCheckId'])){
	$view = 'edit';
}else{
	$view = 'search';
}
if($view=='search'){
	$description = "
	<span style='color:red;'>Payments can not be modified. If changes need to be made, delete the payment and create a new one.</span><br/>
	In this page you can edit which invoices are being paid by a customer payment. First search the invoice you want to use by double clicking the row of the invoice. You can filter the search by the customer or the start of the cheque number.<br/>
	To delete a payment simple click on the red cross (<img src='/trucking/img/118.png' width='14px' height='14px' />) on each payment row.
	";
}else{
	$description = "
	<span style='color:red;'>Payments can not be modified. If changes need to be made, delete the payment and create a new one.</span><br/>
	To delete this payment method click the red cross (<img src='/trucking/img/118.png' width='14px' height='14px' />) on the top right corner of the page.<br/>
	Use the search button to list invoices that can be paid. You can further filter your search by selecting a project from the dropdown menu and/or selecting a range of dates.<br/>
	You can remove payments made to invoices by clicking the red cross (<img src='/trucking/img/118.png' width='14px' height='14px' />) in each paid invoice from the first table.<br/>
	You can add payments by selecting the amount in the 'To Pay' column and clicking the green arrow (<img src='/trucking/img/23.png' width='14px' height='14px' />) in each invoice from the second table.<br/>
	If the amount to pay is greater than the amount of the cheque. The amount paid will only be the difference.
	";
}


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
<?
if($view == 'edit'){
	$paymentInfo = mysql_fetch_assoc(mysql_query("select * from customer_super_check JOIN customer using (customerId) LEFT JOIN customer_credit using (customerSuperCheckId) where customerSuperCheckId = ".$_GET['customerSuperCheckId'], $conexion));
	$amount = decimalPad(($paymentInfo['customerCreditAmount']!=null? $paymentInfo['customerSuperCheckAmount'] - $paymentInfo['customerCreditAmount']: $paymentInfo['customerSuperCheckAmount']));
	
	echo "var superCheckId = ".$_GET['customerSuperCheckId'].";";
}else{
	$amount = 0;
}
echo "var totalAmount = $amount;";
?>
$(document).ready(function()
{	
	$('#searchButtonPayments').click(function(){
		getPayments();
	});
	
	$('.editable').live('dblclick', function(){
		var superCheckId = $(this).attr("superCheckId");
		console.log('editable clicked: ' + superCheckId);
		window.location = "editCustomer_Payment.php?customerSuperCheckId="+superCheckId;
	});
	
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
	});
	
	$('.removableInvoice').live('click',function(){
		var receiptId = $(this).closest('tr').attr('receiptId');
		deleteInvoice(receiptId);
		var toSubstract = $(this).closest("tr").find(".sumable").text();
		totalAmount = parseFloat(totalAmount) - parseFloat(toSubstract);
		updateAmount(totalAmount);
		$(this).closest("tr").remove();
	});
	
	$('.removablePayment').live('click', function(){
		if(confirm("Are you sure you want to delete this payment and all associated credits?")){
			var superCheckId = $(this).closest('tr').attr("superCheckId");
			deletePayment(superCheckId);
		}
	});
	
	$('#searchButtonEdit').click(function(){
		getInvoices();
	});
});

function deleteInvoice(receiptId){
	$.ajax({
		type: "GET",
		url: "removeReceiptCheque.php",
		data: "receiptId="+receiptId
	});
}

function moveInvoice(invoiceId, maxAmount){
	var toPay = parseFloat($("#toPay"+invoiceId).val());
	if(toPay > maxAmount){
		toPay = parseFloat(maxAmount);
	}
	
	$.ajax({
		type: "GET",
		url: "addReceiptCheque.php",
		data: "invoiceId="+invoiceId+"&amount="+toPay+"&superCheckId="+superCheckId,
		success: function(data){
			var obj = jQuery.parseJSON(data);
			totalAmount = parseFloat(obj.amount) + parseFloat(totalAmount);
			updateAmount(totalAmount);
			$("#paidInvoices").append(obj.newRow);
		}
		
	});
}

function removePayment(invoiceId, superCheckId){
	
}

function updateAmount(newAmount){
	$("#amountSum").text(newAmount.toFixed(2));
}

function deletePayment(customerSuperCheckId){
	$.ajax({
		type: "GET",
		url: "deleteCustomerPayment.php",
		data: "customerSuperCheckId="+customerSuperCheckId,
		success: function(data){
			obj = jQuery.parseJSON(data);
			console.log(obj);
			$("#paymentRow"+obj).remove();
		}
	});
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

function getPayments(){
	var customerId = $('#customerId').val();
	var checkNumber = $('#chequeNumberLike').val();
	$.ajax({
		type: "GET",
		url: "getEditCustomerPayments.php",
		data: "customerId="+customerId+"&checkNumber="+checkNumber,
		success: function(data){
			var obj = jQuery.parseJSON(data);
			console.log(obj);
			if(obj.table != null){
				$('#searchResultsTable > tbody:last').remove();
				$('#searchResultsTable').append(obj.table);
			}
		}
	});
}

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
				<?if($view == 'edit') echo "<a href='deleteCustomerPayment.php?customerSuperCheckId=".$_GET['customerSuperCheckId']."' class='delete' onclick='return confirm(\"If you delete this cheque all payments associated with it will be deleted. Are you sure you want to delete it?\")' ></a>";?>
				<a href="#" class="project"></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2" id='descriptionDisplay' >Description</th>
					</tr>
					<tr style="display:none;" id='hiddenDescription'>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			<?
			if($view == 'edit' ){
				
			?>
			
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
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='".$paymentInfo['customerId']."' >".$paymentInfo['customerName']."</option>";
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='customerChequeNum' name='customerChequeNum' value='<?echo $paymentInfo['customerSuperCheckNumber'];?>' disabled /></td>
						<td><input type='text' size='10px' id='customerChequeDate' name='customerChequeDate' value='<?echo to_MDY($paymentInfo['customerSuperCheckDate']);?>' disabled /></td>
						<td><input type='text' size='10px' id='customerChequeAmount' name='customerChequeAmount' value='<?echo decimalPad($paymentInfo['customerSuperCheckAmount']);?>' disabled /></td>
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
								$queryTerm = "select * from project where customerId = ".$paymentInfo['customerId']." order by projectName asc";
								$terms = mysql_query($queryTerm,$conexion);
								$countTerms = mysql_num_rows($terms);
								while($term=mysql_fetch_assoc($terms))
								{
									echo "<option value='{$term['projectId']}'>{$term['projectName']}</option>";
								}
						echo "</select>";
						?>
						</td>
						<td colspan='2'><textarea id='customerChequeNote' name='customerChequeNote' cols='52' rows='1' disabled ><?echo $paymentInfo['customerSuperCheckNote']?></textarea></td>
						<td id='amountSum' align='right'><? echo decimalPad(($paymentInfo['customerCreditAmount']!=null? $paymentInfo['customerSuperCheckAmount'] - $paymentInfo['customerCreditAmount']: $paymentInfo['customerSuperCheckAmount']));?></td>
					</tr>
					<tr class='bg'>
						<td>Start date:</td>
						<td><input type='text' size='10px' id='startDate' name='startDate' /></td>
						<td>End date:</td>
						<td><input type='text' size='10px' id='endDate' name='endDate' /></td>
					</tr>
					<tr>
						<td ><!--<button type='submit' id='submitButtonEdit' >Submit</button>--></td>
						
						<td>
							<!--<button type='button' id='addButton' >Submit</button>-->
							<!--<input type='radio' name='listedLimit' value='20' id='twenty' checked ><label for='twenty'>20</label><br/>
							<input type='radio' name='listedLimit' value='50' id='fifty' ><label for='fifty'>50</label><br/>
							<input type='radio' name='listedLimit' value='100' id='hundred' ><label for='hundred'>100</label><br/>
							<input type='radio' name='listedLimit' value='all' id='allListed' ><label for='allListed'>All</label><br/>-->
						</td>
						<td>
						</td>
						<td><button type='button' id='searchButtonEdit' >Search</button></td>
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
						<th>This check</th>
						<th>after Balance</th>
						<th class='last' >Remove</th>
					</tr>
					<?
					$queryInvoices = "select * from receiptcheques JOIN invoice using (invoiceId) JOIN project using (projectId) where customerSuperCheckId = ".$paymentInfo['customerSuperCheckId'];
					$invoices = mysql_query($queryInvoices, $conexion);
					while($invoice = mysql_fetch_assoc($invoices)){
						$total = getInvoiceTotal($invoice['invoiceId'], $conexion);
						$paid = getInvoicePaid($invoice['invoiceId'], $conexion);
						echo "<tr id='receipt".$invoice['receiptchequesId']."' receiptId='".$invoice['receiptchequesId']."'>
							<td>".$invoice['invoiceId']."</td>
							<td>".$invoice['projectName']."</td>
							<td>".to_MDY($invoice['invoiceDate'])."</td>
							<td>".decimalPad($total)."</td>
							<td>".decimalPad($paid - $invoice['receiptchequesAmount'])."</td>
							<td>".decimalPad($total - ($paid - $invoice['receiptchequesAmount']))."</td>
							<td class='sumable'>".decimalPad($invoice['receiptchequesAmount'])."</td>
							<td>".decimalPad($total - $paid)."</td>
							<td><img src='/trucking/img/118.png' width='20px' height='20px' class='removableInvoice' /></td>
						</tr>";
					}
					?>
				</table>
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
		}
		
		if($view == 'search'){
		?>
			<div class='table' id='searchBarDiv'>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' id='searchBarTable'>
					<tr>
						<th class='full' colspan='3' >Search Payments</th>
					</tr>
					<tr class='bg'>
						<td>Customer</td>
						<td>Cheque Number</td>
						<td>Search</td>
					</tr>
					<tr>
						<td>
							<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt' >
							<option value='0'>--Select Customer--</option>
							<?
							$customers = mysql_query("select * from customer order by customerName",$conexion);
							while($customer=mysql_fetch_assoc($customers)){
								echo "<option value='".$customer['customerId']."' >".$customer['customerName']."</option>";
							}
							?>
							</select>
						</td>
						<td><input type='text' id='chequeNumberLike' /></td>
						<td><button type='button' id='searchButtonPayments' >Search</button></td>
					</tr>
				</table>
			</div>
			
			<div class='table' id='searchBarDiv'>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' id='searchResultsTable'>
					<thead>
						<tr class='bg'>
							<th>Cheque Number</th>
							<th>Date</th>
							<th>Amount</th>
							<th>Remove</th>
						</tr>
					</thead>
					<tbody>
						<?
						$superchecks = mysql_query("select * from customer_super_check order by customerSuperCheckId limit 40", $conexion);
						while($check = mysql_fetch_assoc($superchecks)){
							echo "<tr class='editable' id='paymentRow".$check['customerSuperCheckId']."' superCheckId='".$check['customerSuperCheckId']."' >";
								echo "<td>".$check['customerSuperCheckNumber']."</td>";
								echo "<td>".to_MDY($check['customerSuperCheckDate'])."</td>";
								echo "<td>".decimalPad($check['customerSuperCheckAmount'])."</td>";
								echo "<td><img src='/trucking/img/118.png' width='20px' height='20px' class='removablePayment' /></td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
			
		<?
		}
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
