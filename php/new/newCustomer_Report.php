<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Customer_Report";
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
		var brokerId = $('#brokerId').val();
		var driverId = $('#driverId').val();
		$('#framePreview').remove();
		console.log('showBrokerBalancePreview.php?fromDate='+startDate+'&toDate='+endDate+'&brokerId='+brokerId+'&driverId='+driverId);
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'showBrokerBalancePreview.php?fromDate='+startDate+'&toDate='+endDate+'&brokerId='+brokerId+'&driverId='+driverId
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#printBalanceButton').click(function(){
		var startDate = $('#fromDateInput').val();
		var endDate = $('#toDateInput').val();
		var brokerId = $('#brokerId').val();
		var driverId = $('#driverId').val();
		var url = 'showBrokerBalancePreview.php?fromDate='+startDate+'&toDate='+endDate+'&brokerId='+brokerId+'&driverId='+driverId;
		var windowName = 'popUp';
		var windowSize = 'width=814,heigh=514,scrollbars=yes';
		window.open(url,windowName,windowSize);
		event.preventDefault();
	});
	
	$('#customerId').change(function(){
	var broker=this.value;
		getProjects(broker);
	});
	
});

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
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">Broker Detailed Balance Report</th>
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
						$queryTerm = "select * from customer order by customerName";
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
						<td rowspan='2'><input type='text' name='fromDate' id='fromDateInput' /></td>
						<td rowspan='2'><input type='text' name='toDate' id='toDateInput' /></td>
						<td rowspan='2'><button type='button' name='previewBalance' id='previewBalanceButton'>Preview</button></td>
						<td rowspan='2'><button type='button' name='printBalance' id='printBalanceButton'>Print</button></td>
					</tr>
					<tr>
						<td>
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select project--</option>";
						if(isset($_GET['customerId'])){
								$queryTruck = "SELECT * FROM project WHERE brokerId=".$_GET['customerId'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if(isset($_GET['projectId'])&&$_GET['projectId']==$truck['projectId'])
										echo "<option selected='selected' value='".$truck['projectId']."' >".$truck['projectName']."</option>";
									else
										echo "<option value='".$truck['projectId']."' >".$truck['projectName']."</option>";
								}
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
