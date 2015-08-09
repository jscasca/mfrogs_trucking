<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Supplier";
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

var patternInvoice = new RegExp(/invoice/);
patternInvoice.compile(patternInvoice);

$(document).ready(function()
{
	$('#suppliers tr').live('dblclick',function(){
			var invoiceId = this.id;
			invoiceId = invoiceId.replace(/invoice/,'');
			
			var url = 'showSupplier.php?i='+invoiceId;
			var windowName = 'popUp';
			var windowSize = 'width=814,heigh=514,scrollbars=yes';
			window.open(url,windowName,windowSize);
			event.preventDefault();
	});
	
	$('.deletable').live('click',function(){
		var invoiceId = $(this).attr("supplierInvoiceId");
		var invoiceNumber = $(this).attr("supplierInvoiceNumber");
		
		if(window.confirm("Are you sure you want to delete invoice "+invoiceNumber)){
			deleteInvoice(invoiceId);
		}
	});
	
	$('.payable').live('click', function(){
		var supplierInvoiceId = $(this).attr("supplierInvoiceId");
		//goToPaymentWithMetaData(supplierInvoiceId);
		goToWithMetaData(supplierInvoiceId, "newSupplierPaycheque");
	});
	
	$('.managable').live('click',function(){
		var supplierInvoiceId = $(this).attr("supplierInvoiceId");
		goToWithMetaData(supplierInvoiceId, "manageSupplierPayCheque");
	});
	
	
	$('#vendorId').change(function(){
		var vendor = $(this).val();
		getSuppliers(vendor);
		getTickets();
	});
	$('#supplierId').change(function(){
		getTickets();
	});
	$('#invoiceNumber').change(function(){getTickets();});
	
	<?if(isset($_GET['vendorId']) && $_GET['vendorId']!="" &&$_GET['vendorId']!=0)echo "getTickets();";?>
	
});

function goToWithMetaData(supplierInvoiceId, url){
	console.log("meta");
	var vendorId=$('#vendorId').val();
	var supplierId=$('#supplierId').val();
	//var afterDate=$('#invoiceNumber').val();
	//var paid=$('#paid').val();
	
	//window.location = "newPaycheque.php?reportId="+reportId+"&brokerId="+brokerId+"&driverId="+driverId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&paid="+paid;
	window.location = url+".php?supplierInvoiceId="+supplierInvoiceId+"&vendorId="+vendorId+"&supplierId="+supplierId;
}

function goToPaymentWithMetaData(supplierInvoiceId){
	console.log("meta");
	//var brokerId=$('#vendorId').val();
	//var driverId=$('#supplierId').val();
	//var afterDate=$('#invoiceNumber').val();
	//var paid=$('#paid').val();
	
	//window.location = "newPaycheque.php?reportId="+reportId+"&brokerId="+brokerId+"&driverId="+driverId+"&afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&paid="+paid;
	window.location = "newSupplierPaycheque.php?supplierInvoiceId="+supplierInvoiceId;
}

function deleteInvoice(invoiceId){
	$.ajax({
		type: "GET",
		url: "deleteSupplierInvoice.php",
		data: "supplierInvoiceId="+invoiceId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
				$("#invoice"+obj).remove();
		},
		async: false
	});
}

function getSuppliers(vendor){
	$.ajax({
		type: "GET",
		url: "getSuppliers.php",
		data: "vendorId="+vendor,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var suppliers=$('#supplierId');
			suppliers.children().remove();
			suppliers.append("<option value='0' >--Select Supplier--</option>");
			jQuery.each(obj, function(i,val){
				suppliers.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function getTickets(){
	var vendorId=$('#vendorId').val();
	var supplierId=$('#supplierId').val();
	var invoiceNum=$('#invoiceNumber').val();
	
	$.ajax({
		type: "GET",
		url: "getSupplierReports.php",
		data: "vendor="+vendorId+"&supplier="+supplierId+"&invoiceNum="+invoiceNum,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#suppliers > tbody:last').remove();
					$('#suppliers').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function evalDate(date){
		if(date.match(patternDate)){
			date=date.replace(patternDate,'$3-$1-$2');
			return date;
		}else{return '0000-00-00';}
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
				<a href="reportSupplier.php" class="returnLink"><img src='/trucking/img/48.png' /></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">Suppliers Report</th>
					</tr>
					<tr class='bg'>
						<td>Vendor</td>
						<td>Supplier</td>
						<td>Suppplier Invoice #</td>
						<td></td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from vendor order by vendorName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='vendorId' id='vendorId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Vendor--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['vendorId'])&&$_GET['vendorId']==$term['vendorId'])
										echo "<option selected='selected' value='{$term['vendorId']}'>{$term['vendorName']}</option>";
									else
										echo "<option value='{$term['vendorId']}'>{$term['vendorName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no vendors in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td>
						<?
						$queryTerm = "select * from supplier ";
						if(isset($_GET['vendorId']) && $_GET['vendorId']!="" && $_GET['vendorId']!=0)$queryTerm.=" WHERE vendorId = ".$_GET['vendorId'];
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='supplierId' id='supplierId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Supplier--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['supplierId'])&&$_GET['supplierId']==$term['supplierId'])
										echo "<option selected='selected' value='{$term['supplierId']}'>{$term['supplierName']}</option>";
									else
										echo "<option value='{$term['supplierId']}'>{$term['supplierName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no suppliers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='invoiceNumber' name='invoiceNumber' /></td>
						<td><input type='button' value='Search' />
							<!--<select name='paid' id='paid' style='font-family:verdana;font-size:8pt' >
								<option value='0' >All</option>
								<option value='1' >Paid</option>
								<option value='2' >Unpaid</option>
							</select>-->
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
			$queryInvoices = "
				SELECT
					*
				FROM
					supplierinvoice
					JOIN supplier using (supplierId)
					JOIN vendor using (vendorId)
				ORDER BY
					supplierInvoiceDate desc
				LIMIT 40
			";
			
			$invoices = mysql_query($queryInvoices, $conexion);
			if(mysql_num_rows($invoices) > 0){
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0' id='suppliers'>";
				echo "<thead><tr>
						<th class='first'>Supplier</th>
						<th>Supplier Invoice #</th>
						<th>Date</th>
						<th>Amount</th>
						<th>To Pay</th>
						<th>Paid</th>
						<th class='last' colspan='2'></th>
					</tr>
					</thead>
					<tbody>";
				$colorFlag=true;
				$paid='Unpaid';
				
				while($invoice = mysql_fetch_assoc($invoices)){
					
					$paidTotal = "
						SELECT
							SUM(supplierchequeAmount) as totalPaid,
							COUNT(*) as number
						FROM
							suppliercheque
						WHERE
							supplierInvoiceId = ".$invoice['supplierInvoiceId']."
					";
					$paidInfo = mysql_fetch_assoc(mysql_query($paidTotal, $conexion));
					
					$paidTotal = decimalPad($paidInfo['totalPaid'] == null ? 0 : $paidInfo['totalPaid']);
					$chequeTotal = $paidInfo['number'] == null ? 0 : $paidInfo['number'];
					$reportTotal = decimalPad($invoice['supplierInvoiceAmount'] );
					
					if($paidTotal == null || $paidTotal <= 0 || $chequeTotal == 0 ) $paid = 'Unpaid';
					if($paidTotal != null && $paidTotal >= $reportTotal && $chequeTotal != 0) $paid = 'Paid';
					if($paidTotal != null && $paidTotal > 0 && $paidTotal < $reportTotal) $paid = 'Warning';
					if($paidTotal != null && $paidTotal > $reportTotal) $paid = 'Overpaid';
					
					if($colorFlag){echo "<tr class='even".$paid."' id='invoice".$invoice['supplierInvoiceId']."'>";}
					else{echo "<tr class='odd".$paid."' id='invoice".$invoice['supplierInvoiceId']."'>";}
					$colorFlag=!$colorFlag;
					
						echo "<td>".$invoice['supplierName']."</td>";
						echo "<td>".$invoice['supplierInvoiceNumber']."</td>";
						echo "<td>".to_MDY($invoice['supplierInvoiceDate'])."</td>";
						echo "<td>".decimalPad($reportTotal)."</td>";
						echo "<td>".decimalPad($reportTotal - $paidTotal)."</td>";
						echo "<td>".decimalPad($paidTotal)."</td>";
						
						if($paid == 'Unpaid' || $paid == 'Warning') echo "<td class='number' ><img src='/trucking/img/87.png' width='24' height='22' class='payable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' /></td>";
						else echo "<td></td>";
						
						if($paid == 'Unpaid')echo "<td><img src='/trucking/img/118.png' width='20' height='20' class='deletable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' supplierInvoiceNumber='".$invoice['supplierInvoiceNumber']."' /></td>";
						else echo "<td><img src='/trucking/img/2.png' width='24' height='22' class='managable' supplierInvoiceId='".$invoice['supplierInvoiceId']."' /></td>";
						
					echo "</tr>";
				}
				
				echo "</tbody></table>";
				echo "</div>";
			}
			/*
			$queryTickets = "
				SELECT
					*
				FROM
					supplierinvoice
				JOIN supplier using (supplierId)
				JOIN vendor using (vendorId)
				LEFT JOIN (select MAX(ticketDate) as maxT, supplierInvoiceId from supplierinvoiceticket join ticket using (ticketId) group by supplierInvoiceId) as MaxT using (supplierInvoiceId)
				LEFT JOIN (select MIN(ticketDate) as minT, supplierInvoiceId from supplierinvoiceticket join ticket using (ticketId) group by supplierInvoiceId) as MinT using (supplierInvoiceId)
				ORDER BY
					supplierInvoiceId DESC
				";
			$terms = mysql_query($queryTickets,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0' id='suppliers'>";
				echo "<thead><tr>
						<th class='first'>Vendor</th>
						<th>Supplier</th>
						<th>Supplier Invoice #</th>
						<th colspan='2'>Date Range</th>
						<th>Total</th>
						<th class='last'>View</th>
					</tr>
					</thead>
					<tbody>";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($terms))
				{	
				$queryInvoice="
				SELECT 
					*
				FROM
					supplierinvoiceticket
						WHERE
					supplierInvoiceId=".$term['supplierInvoiceId'];
					$invoices = mysql_query($queryInvoice,$conexion);
					$total=0;
					while($invoicesInfo = mysql_fetch_assoc($invoices))
					{
					$queryInvoice2="
					SELECT 
					*
					FROM
					ticket
					WHERE
					ticketId=".$invoicesInfo['ticketId'];
					$invoices2 = mysql_query($queryInvoice2,$conexion);
					if(mysql_num_rows($invoices2)==0)continue;
					$invoices2Info = mysql_fetch_assoc($invoices2);

					$queryInvoice3="
					SELECT 
						*
							FROM
							item
						WHERE
					itemId=".$invoices2Info['itemId'];

					$invoices3 = mysql_query($queryInvoice3,$conexion);
						$invoices3Info = mysql_fetch_assoc($invoices3);
						$total+=$invoices2Info['ticketAmount']*$invoices3Info['itemMaterialPrice'];
				}
					if($colorFlag)
					{
						echo "<tr id='invoice".$term['supplierInvoiceId']."'>";
						!$colorFlag;
					}
					else
					{
						echo "<tr class='bg' id='invoice".$term['supplierInvoiceId']."'>";
						!$colorFlag;
					}
					echo "
						<td class='first style2'>".$term['vendorName']."</td>
						<td class='first style2'>".$term['supplierName']."</td>
						<td class='first style2'>".$term['supplierInvoiceNumber']."</td>
						<td class='first style2'>".($term['minT']!="" ? to_MDY($term['minT']) : "--" )."</td>
						<td class='first style2'>".($term['maxT']!="" ? to_MDY($term['maxT']) : "--" )."</td>
						<td class='first style2'>$ ".decimalPad($total)."</td>
						<td><a onclick=\"return confirm('Are you sure you want to delete Invoice #".$term['supplierInvoiceId']."?');\" href='deleteSupplier.php?reportId=".$term['supplierInvoiceId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
							";
					echo "</tr>";
				}
				
				
				echo "</tbody></table>";
				echo "</div>";
			}*/
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
