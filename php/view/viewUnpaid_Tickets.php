<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Unpaid_Tickets";
$description = ". Values marked with <span style='color:red;'>*</span> are mandatory.";

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
	$('#preview').click(function(){
		prepare('preview');
	});
	
	$('#popup').click(function(){
		prepare('popout');
	});
	
	$("input[name='type']").change(function() {
		var type = $('input[name=type]:checked').val();
		showType(type);
	});
	
});

function showType(type) {
	hideAll();
	if(type == 'report') { $('#brokerDiv').show().addClass('activeDropdown'); }
	if(type == 'invoice') { $('#customerDiv').show().addClass('activeDropdown'); }
	if(type == 'supplier') { $('#supplierDiv').show().addClass('activeDropdown'); }
}

function hideAll(){
	$('#supplierDiv').hide();
	$('#brokerDiv').hide();
	$('#customerDiv').hide();
	
	$('#supplierDiv').removeClass();
	$('#brokerDiv').removeClass();
	$('#customerDiv').removeClass();
	
}

function prepare(showFormat){
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();
	var type = $('input[name=type]:checked').val();
	var filterData = $('.activeDropdown option:selected').val()
	console.log(type + ' filter: ' + filterData);
	showTickets(fromDate, toDate, type, filterData, showFormat);
}

function showTickets(fromDate, toDate, type, filter, showFormat){
	url = "";
	if(type == 'report'){url = 'showUnreportedTickets.php?fromDate='+fromDate+'&toDate='+toDate+'&filter='+filter;}
	if(type == 'invoice'){url = 'showUninvoicedTickets.php?fromDate='+fromDate+'&toDate='+toDate+'&filter='+filter}
	if(type == 'supplier'){url = 'showUnsupplierTickets.php?fromDate='+fromDate+'&toDate='+toDate+'&filter='+filter}
	if(url == "")return false;
	if(showFormat == 'preview'){previewFrame(url);}
	if(showFormat == 'popout'){popoutPage(url);}
}

function previewFrame(source){
	$('#framePreview').remove();
		
	$('<iframe />',{
		name: 'framePreview',
		id: 'framePreview',
		src: source
	}).width('100%').height('2048px').appendTo('#previewFrame');
}

function popoutPage(source){
	var windowName = 'popUp';
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(source,windowName,windowSize);
	event.preventDefault();
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
						<th class="full" colspan="6">Brokers Weekly</th>
					</tr>
					<tr class='bg'>
						<td></td>
						<td>From Date</td>
						<td>To Date</td>
						<td>Options</td>
					</tr>
					<tr>
						<td class='last' rowspan='2'>
							<input type='radio' name='type' value='invoice' id='type_invoice' checked /><label for='type_invoice'>Invoice</label><br/>
							<input type='radio' name='type' value='report' id='type_report' /><label for='type_report'>Report</label><br/>
							<input type='radio' name='type' value='supplier' id='type_supplier' /><label for='type_supplier'>Supplier</label><br/>
						</td>
						<td><input type='text' size='10px' id='fromDate' name='fromDate' /></td>
						<td><input type='text' size='10px' id='toDate' name='toDate' /></td>
						<td>
							<!--<input type='button' size='10px' id='search' name='searchBtn' value='Search' />-->
							<input type='button' size='10px' id='preview' name='prevBtn' value='Preview' />
							<input type='button' size='10px' id='popup' name='popBtn' value='Print' />
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<div id='customerDiv'>
								<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>
									<option selected='selected' value='0'>--Select Customer--</option>
									<?
									$customers = mysql_query("SELECT * FROM customer order by customerName", $conexion);
									while($customer = mysql_fetch_assoc($customers)) {
										echo "<option value='".$customer['customerId']."'>".$customer['customerName']."</option>";
									}
									?>
								</select>
							</div>
							<div id='brokerDiv' style='display:none'>
								<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>
									<option selected='selected' value='0'>--Select Broker--</option>
									<?
									$brokers = mysql_query("SELECT * FROM broker order by brokerName", $conexion);
									while($broker = mysql_fetch_assoc($brokers)) {
										echo "<option value='".$broker['brokerId']."'>".$broker['brokerName']."</option>";
									}
									?>
								</select>
							</div>
							<div id='supplierDiv' style='display:none'>
								<select name='supplierId' id='supplierId' style='font-family:verdana;font-size:8pt'>
									<option selected='selected' value='0'>--Select Supplier--</option>
									<?
									$suppliers = mysql_query("SELECT * FROM supplier order by supplierName", $conexion);
									while($supplier = mysql_fetch_assoc($suppliers)) {
										echo "<option value='".$supplier['supplierId']."'>".$supplier['supplierName']."</option>";
									}
									?>
								</select>
							</div>
						</td>
						<td></td>
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
