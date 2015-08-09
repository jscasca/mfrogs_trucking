<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Vendor_Report";
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
	$('#previewBalanceButton').click(function(){
		var startDate = $('#fromDateInput').val();
		var endDate = $('#toDateInput').val();
		var vendorId = $('#vendorId').val();
		var supplierId = $('#supplierId').val();
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'showVendorInvoicesPreview.php?fromDate='+startDate+'&toDate='+endDate+'&vendorId='+vendorId+'&supplierId='+supplierId
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#printBalanceButton').click(function(){
		var startDate = $('#fromDateInput').val();
		var endDate = $('#toDateInput').val();
		var vendorId = $('#vendorId').val();
		var supplierId = $('#supplierId').val();
		var url = 'showVendorInvoicesPreview.php?fromDate='+startDate+'&toDate='+endDate+'&vendorId='+vendorId+'&supplierId='+supplierId;
		var windowName = 'popUp';
		var windowSize = 'width=814,heigh=514,scrollbars=yes';
		window.open(url,windowName,windowSize);
		event.preventDefault();
	});
	
	
	$('#vendorId').change(function(){
		var vendor = this.value;
		getSuppliers(vendor);
	});
	
});

function getSuppliers(vendor){
	$.ajax({
		type: "GET",
		url: "getSuppliers.php",
		data: "vendorId="+vendor,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var supplier=$('#supplierId');
			supplier.children().remove();
			supplier.append("<option value='0' >--Select supplier--</option>");
			jQuery.each(obj, function(i,val){
				supplier.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

/*
function getReports(){
	var brokerId=$('#brokerId').val();
	var truckId=$('#truckId').val();
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
		data: "afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&brokerId="+brokerId+"&truckId="+truckId+"&driverId="+driverId+"&paid="+paid,
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
		},
		async: false
	});
}*/

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
						<td>Balance target</td>
						<td colspan='2'>Date Range</td>
						<td></td>
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
									if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
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
						<td rowspan='2'><input type='text' name='fromDate' id='fromDateInput' /></td>
						<td rowspan='2'><input type='text' name='toDate' id='toDateInput' /></td>
						<td rowspan='2'><button type='button' name='previewBalance' id='previewBalanceButton'>Preview</button></td>
						<td rowspan='2'><button type='button' name='printBalance' id='printBalanceButton'>Print</button></td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from supplier order by supplierName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='supplierId' id='supplierId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Supplier--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
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
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='iframes' id='previewFrame' >
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
