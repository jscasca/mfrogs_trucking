<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

$invoiceId = $_GET['invoiceId'];

$customerId = $_GET['customerId'];
$projectId = $_GET['projectId'];
$afterDate = $_GET['afterDate'];
$beforeDate = $_GET['beforeDate'];
$week = $_GET['week'];
$paid = $_GET['paid'];
$invoiceNum = $_GET['invoiceNum'];

$invoiceInfo = mysql_fetch_assoc(mysql_query("select * from invoice join project using (projectId) where invoiceId = $invoiceId", $conexion));

$additionalParams = "?customerId=$customerId&projectId=$projectId&afterDate=$afterDate&beforeDate=$beforeDate&beforeEndDate=$beforeEndDate&week$week=&paid=$paid&invoiceNum=$invoiceNum";

#################
$title = "New Receipt Cheque";
#################
$subtitle = "Pay Cheque";
$description = "Pay Receipt Cheques. Values marked with <span style='color:red;'>*</span> are mandatory.";

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

###############Next Autoincrement#########
$next_inc=0;
$showstatus="show table status like 'receiptcheques'";
$status=mysql_query($showstatus,$conexion);
$row=mysql_fetch_assoc($status);
$next_inc=$row['Auto_increment'];
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
	
$(document).ready(function(){
	
	$('.submitable').click(function(){
		console.log("here");
		var superCheckId = $(this).attr("customerSuperCheckId");
		var maxDeductable = parseFloat($('#max'+superCheckId).val());
		var amount = parseFloat($('#payable'+superCheckId).val());
		var missing = parseFloat($('#missingAmount').val());
		if(amount > maxDeductable){
			alert("The credit balance is lower than the amount you want to pay.");
			return false;
		}else{
			if(amount > missing){
				if(!confirm("The amount you have entered is more than the amount you have to pay. Are you sure you want to overpay this invoice?")){
					return false;
				}
			}
			$('#superCheckId').val(superCheckId);
			$('#creditAmount').val(amount);
			$('#fromCredit').submit();
		}
		
		
	});
});
	
function validateForm(){

	if(document.getElementById('receiptchequeNumber').value.length==0){
		alert("Please type a Cheque Number.");
		document.formValidate.receiptchequeNumber.focus;
		return false;
	}
	if(document.getElementById('receiptchequesAmount').value.length==0){
		alert("Please type an amount.");
		document.formValidate.receiptchequesAmount.focus;
		return false;
	}
	if(document.getElementById('receiptchequesDate').value.length==0){
		alert("Please type a date.");
		document.formValidate.receiptchequesDate.focus;
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
				<a href="reportInvoice.php<?echo $additionalParams;?>" class='returnLink'>Back<img src='/trucking/img/72.png' width='32px' height='32px' /></a>
				<a href="#" class="shellproject"></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Pay Receipt Cheque</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitReceiptCheque.php<?echo $additionalParams;?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Receipt Cheque</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Cheque ID:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" disabled value="<?echo$next_inc;?>" /></td>
					</tr>
						<input type="hidden" id='invoiceId' name='invoiceId' value="<?echo$invoiceId;?>" />
					<?
					
					
					$totalQuery = "
						SELECT
							SUM(itemCustomerCost*ticketAmount) as invoiceTotal
						FROM
							invoiceticket
							JOIN ticket using (ticketId)
							JOIN item using (itemId)
						WHERE
							invoiceId = $invoiceId
					";
					$invoiceTotal = mysql_fetch_assoc(mysql_query($totalQuery,$conexion));
					$paidQuery = "
						SELECT
							SUM(receiptchequesAmount) as totalPaid
						FROM
							receiptcheques
						WHERE
							invoiceId = $invoiceId
					";
					$paidTotal = mysql_fetch_assoc(mysql_query($paidQuery, $conexion));
					$missing = decimalPad($invoiceTotal['invoiceTotal']) - decimalPad($paidTotal['totalPaid']);
					?>
					<tr>
						<td class="first" width="100"><strong>Cheque Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='receiptchequeNumber' name='receiptchequeNumber'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Cheque Date:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='receiptchequesDate' name='receiptchequesDate' value='<?echo to_MDY($invoiceInfo['invoiceDate']);?>' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Amount:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='receiptchequesAmount' name='receiptchequesAmount' value='<?echo decimalPad($missing);?>' /></td>
					</tr>
				</table>
				<table>
				<tr>
				<td><input type='reset'  value='Reset' ></td>
				<td><input type='submit' value='Submit' ></td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<?
			$creditQuery = "select * from customer_super_check JOIN customer_credit using (customerSuperCheckId) where customerId = ".$invoiceInfo['customerId'];
			$credits = mysql_query($creditQuery, $conexion);
			if(mysql_num_rows($credits) > 0){
				?>
				<div class='table' >
				<form id="fromCredit" name="fromCredit" method="POST" action="submitReceiptCredit.php<?echo $additionalParams;?>" >
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
					<table class="listing form" cellpadding="0" cellspacing="0">
						<tr>
							<input type="hidden" id='invoiceId' name='invoiceId' value="<?echo$invoiceId;?>" />
							<input type='hidden' id='superCheckId' name='superCheckId' value="0" />
							<input type='hidden' id='creditAmount' name='creditAmount' value="0" />
							<input type='hidden' id='missingAmount' name='missingAmount' value="<?echo decimalPad($missing);?>" />
							<th class='first'>Number</th>
							<th>Date</th>
							<th>Credit value</th>
							<th>Amount</th>
							<th class='last'>Pay</th>
						</tr>
						<?
						while($credit = mysql_fetch_assoc($credits)){
							$creditValue = decimalPad($credit['customerCreditAmount']);
							echo "<tr>";
							echo "<td>".$credit['customerSuperCheckNumber']."</td>";
							echo "<td>".to_MDY($credit['customerSuperCheckDate'])."</td>";
							echo "<td><input type='hidden' id='max".$credit['customerSuperCheckId']."' value='$creditValue' />".$creditValue."</td>";
							echo "<td><input type='text' id='payable".$credit['customerSuperCheckId']."' value='".decimalPad( $creditValue > $missing? $missing : $creditValue)."'/></td>";
							echo "<td><img src='/trucking/img/87.png' class='submitable' width='20px' height='20px' customerSuperCheckId='".$credit['customerSuperCheckId']."' creditAmount='".$credit['customerCreditAmount']."' /></td>";
							
							echo "</tr>";
						}?>
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
