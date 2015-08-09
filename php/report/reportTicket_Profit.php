<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Ticket_Profit";
$description = "";

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
	
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	
	$('#projectId').change(function(){
		var project = this.value;
			getDates(project);
	});
	
	$('#previewGraph').click(function(){
		var startDate = $('#startDate').val();
		var endDate = $('#endDate').val();
		var projectId = $('#projectId').val();
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'graphProjects.php?startDate='+startDate+'&endDate='+endDate+'&projectId='+projectId
		}).width('100%').height('2048px').appendTo('#graphFrame');
		preview = true;
	});
	
});
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

function getDates(project){
	$.ajax({
		type: "GET",
		url: "getPossibleDates.php",
		data: "projectId="+project,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				$('#startDate').val(obj.startingDate);
				$('#endDate').val(obj.endingDate);
			}else{
				
			}
		}
	});
}

function getInvoices(){
	var projectId=$('#projectId').val();
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
		url: "getInvoices.php",
		data: "afterDate="+afterDate+"&beforeDate="+beforeDate+"&beforeEndDate="+beforeEndDate+"&week="+invoiceWeek+"&projectId="+projectId+"&paid="+paid,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#invoices > tbody:last').remove();
					$('#invoices').append(obj.table);
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
						<td>Customer</td>
						<td>Project</td>
						<td>Start Date</td>
						<td>End Date</td>
						<td>Graph</td>
					</tr>
					<tr>
						<td>
						<?
							$queryCustomer = "select * from customer order by customerName asc";
							$customers = mysql_query($queryCustomer,$conexion);
							$countCustomers = mysql_num_rows($customers);
							echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
							if($countCustomers > 0 ){
								if(!isset($_GET['c'])){
									
									echo "<option>--Select Customer--</option>";
									while($customer = mysql_fetch_assoc($customers)){
										if($customer['customerId'] == $_GET['c'])
										echo "<option value='".$customer['customerId']."' selected='selected'>".$customer['customerId']."</option>";
										else 
										echo "<option value='".$customer['customerId']."'>".$customer['customerName']."</option>";;
									}
								}else{
									echo "<option selected='selected'>--Select Customer--</option>";
									while($customer = mysql_fetch_assoc($customers)){
										echo "<option value='".$customer['customerId']."'>".$customer['customerName']."</option>";
									}
								}
							}else{
								echo "There are no customers in the database";
							}
							echo "</select>";
						?>
						</td>
						<td>
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='startDate' name='startDate' /></td>
						<td><input type='text' size='10px' id='endDate' name='endDate' /></td>
						<td>
							<input type='button' id="previewGraph" value='Graph' >
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='iframes' id='graphFrame' >
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
