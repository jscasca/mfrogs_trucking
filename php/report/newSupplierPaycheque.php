<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");
//http://localhost/trucking/php/report/newPaycheque.php?reportId=2&brokerId=0&driverId=0&afterDate=&beforeDate=&beforeEndDate=&week=&paid=0
$reportId = $_GET['supplierInvoiceId'];

$vendorId = $_GET['vendorId'];
$supplierId = $_GET['supplierId'];

$additionalParams = "?vendorId=$vendorId&supplierId=$supplierId";

//$additionalParams = "";
#################
$title = "New Pay Cheque";
#################
$subtitle = "Pay Cheque";
$description = "Pay Cheques. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
$showstatus="show table status like 'paidcheques'";
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
function validateForm(){
	
	var amount = parseFloat($("#paidchequesAmount").val(),10);
	if(amount <= 0){
		alert("The amount to pay can not be 0 or lower.");
		document.formValidate.paidchequesAmount.focus;
		return false;
	}

	if(document.getElementById('paidchequeNumber').value.length==0){
		alert("Please type a Cheque Number.");
		document.formValidate.paidchequeNumber.focus;
		return false;
	}
	if(document.getElementById('paidchequesAmount').value.length==0){
		alert("Please type an amount.");
		document.formValidate.paidchequesAmount.focus;
		return false;
	}
	if(document.getElementById('paidchequesDate').value.length==0){
		alert("Please type a date.");
		document.formValidate.paidchequesDate.focus;
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
				<a href="reportSupplier.php<?echo $additionalParams;?>" class='returnLink'>Back<img src='/trucking/img/72.png' width='32px' height='32px' /></a>
				<a href="#" class="shellproject"></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Pay Cheque</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitSupplierPayCheque.php<?echo $additionalParams;?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Pay Cheque</th>
					</tr>
						<?
						//GET missing payment info for report
						$reportInfo = mysql_fetch_assoc(mysql_query("select * from supplierinvoice where supplierInvoiceId = $reportId",$conexion));
						
						$reportDate = $reportInfo['supplierInvoiceDate'];
						
						$paidTotal = "
							SELECT
								SUM(supplierchequeAmount) as totalPaid
							FROM
								suppliercheque
							WHERE
								supplierInvoiceId = ".$reportId."
						";
						
						$paidInfo = mysql_fetch_assoc(mysql_query($paidTotal, $conexion));
						
						$paidTotal = $paidInfo['totalPaid'] == null ? 0 : $paidInfo['totalPaid'];
						$reportTotal = ($reportInfo['supplierInvoiceAmount'] == null ? 0 : $reportInfo['supplierInvoiceAmount']);
						
						$missingAmount = $reportTotal - $paidTotal;
						?>
					<tr class="bg">
						<td class="first" width="172"><strong>Cheque ID:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" disabled value="<?echo$next_inc;?>" /></td>
					</tr>
						<input type="hidden" id='supplierInvoiceId' name='supplierInvoiceId' value="<?echo$reportId;?>" />
					<tr>
						<td class="first" width="100"><strong>Cheque Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='paidchequeNumber' name='paidchequeNumber'/></td>
					</tr>
					<tr>
						<td class="first"><strong>Cheque Date:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='paidchequesDate' name='paidchequesDate' value='<? echo to_MDY($reportDate); ?>' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Amount:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='paidchequesAmount' name='paidchequesAmount' value='<? echo decimalPad($missingAmount); ?>' /></td>
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
