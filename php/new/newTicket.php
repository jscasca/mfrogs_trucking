<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Ticket";
$description = "Add a new ticket. Tickets are assigned to project items. Tickets that have been assigned to an invoice can not be deleted or modified. Materials have to be provided by the supplier and by default the material price is the supplier price for the choosen material. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
var patternDigits = new RegExp(/[0-9]+/);
patternDigits.compile(patternDigits);

$(document).ready(function()
{
	$('#brokerId').change(function() {
		var broker=this.value;
		getTrucks(broker);
		getDrivers(broker);
	});
	
	$('#truckId').change(function(){
		var truck = this.value;
		getDriver(truck);
	});
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	$('#projectId').change(function() {
		var project=this.value;
		getItems(project);
	});
	
	$('#itemId').change(function() {
		var item=this.value;
		getItemInfo(item);
	});
	
	$('#ticketAmount').blur(function(){
		var amount = this.value;
		$('#ticketBrokerAmount').attr('value',amount);
	});
	
	$('#sub').click(function(){
		if(validateForm()){
			var itemId=$('#itemId').val();
			var truckId=$('#truckId').val();
			var driverId=$('#driverId').val();
			var ticketDate=$('#ticketDate').val();
			var ticketAmount=$('#ticketAmount').val();
			var ticketBrokerAmount=$('#ticketBrokerAmount').val();
			var ticketNumber=$('#ticketNumber').val();
			var ticket=$('#ticketMfi').val();
			submit(itemId,truckId,driverId,ticketDate,ticketAmount,ticketBrokerAmount,ticketNumber,ticket);
			var intPos = ticket.search(patternDigits);
			var prefix = ticket.substring(0,intPos);
			var numeric = ticket.substring(intPos);
			var newTicket = parseInt(numeric) + 1;
			$('#ticketMfi').val(prefix + newTicket);
		}
		else
			alert('Missing Data');
	});
});
function submit(itemId,truckId,driverId,ticketDate,ticketAmount,ticketBrokerAmount,ticketNumber,ticket){
	$.ajax({
		type: "GET",
		url: "submitTicket.php",
		data: "itemId="+itemId+"&truckId="+truckId+"&driverId="+driverId+"&ticketDate="+ticketDate+"&ticketAmount="+ticketAmount+"&ticketBrokerAmount="+ticketBrokerAmount+"&ticketNumber="+ticketNumber+"&ticket="+ticket,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				/*
				if(obj.table!=null){
					//$('#tickets > tbody:last').remove();
					$('#tickets').prepend(obj.table);
				}*/
				if(obj.line!=null){
					$('#tickets').prepend(obj.line);
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
	$('#ticketMfi').focus();
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

function getItems(project){
	$.ajax({
		type: "GET",
		url: "getItems.php",
		data: "projectId="+project,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#itemId');
			material.children().remove();
			material.append("<option value='0' >--Select Item--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function getDriver(truck){
	$.ajax({
		type: "GET",
		url: "getDriver.php",
		data: "truckId="+truck,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			$("#driverId").val(obj.driverId); 
		},
		async: false
	});
}
function getTrucks(broker){
	$.ajax({
		type: "GET",
		url: "getTrucks.php",
		data: "brokerId="+broker,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#truckId');
			material.children().remove();
			material.append("<option value='0' >--Select Truck--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

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
function getItemInfo(item){
	$.ajax({
		type: "GET",
		url: "getItemInfo.php",
		data: "itemId="+item,
		success:function(data){
			var jsonObj=jQuery.parseJSON(data);
			if(jsonObj!=null){
				if(jsonObj.itemInfo!=null)
					$('#itemInfo').text(jsonObj.itemInfo);
				else
					$('#itemInfo').text('No item information');
				if(jsonObj.amounts!=null)
					$('#amounts').text(jsonObj.amounts);
				else
					$('#amounts').text('( L/T/H )');
					
			}
		},
		async: true
	});
}


function validateForm(){

	if(document.getElementById('itemId').selectedIndex==0 ){
		alert("Please select an item");
			document.formValidate.itemId.focus;
			return false;
	}
	
	if(document.getElementById('truckId').selectedIndex==0 ){
		alert("Please select a truck number");
			document.formValidate.truckId.focus;
			return false;
	}
	
	if(document.getElementById('ticketDate').value.length==0){
		alert("Please type a date");
			document.formValidate.ticketDate.focus;
			return false;
	}
	
	if(document.getElementById('ticketAmount').value.length==0){
		alert("Please type the amount");
			document.formValidate.ticketAmount.focus;
			return false;
	}
	
	if(document.getElementById('ticketBrokerAmount').value.length==0){
		alert("Please type the broker amount");
			document.formValidate.ticketBrokerAmount.focus;
			return false;
	}
	
	if(document.getElementById('ticketMfi').value.length==0){
		alert("Please type the ticket Number for MFI");
			document.formValidate.ticketMfi.focus;
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
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Description</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitTicket.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="7">New Ticket</th>
					</tr>
					<tr class='bg' >
					<td class='first' colspan="3">
						<?
						$queryTerm0 = "select * from customer order by customerName asc";
						$terms0 = mysql_query($queryTerm0,$conexion);
						$countTerms0= mysql_num_rows($terms0);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms0 > 0)
						{
							
							if(!isset($_GET['i']))
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
									if($_GET['i']==$term0['customerId'])
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
						<span style="color:red;">*</span>
						
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
						<span style="color:red;">*</span>
						</td>
						<td colspan="2">
						<?
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Broker--</option>";
						$queryBroker = "SELECT * FROM broker where brokerStatus=1 order by brokerName";
						$brokers = mysql_query($queryBroker,$conexion);
						while($broker=mysql_fetch_assoc($brokers)){
								if(isset($_GET['b'])&&$_GET['b']==$broker['brokerId'])
									echo "<option selected='selected' value='".$broker['brokerId']."' >".$broker['brokerName']."</option>";
								else
									echo "<option value='".$broker['brokerId']."' >".$broker['brokerName']."</option>";
						}
						echo "</select>";
						?><span style="color:red;">*</span>
						</td>
						<td></td>
					</tr>
					<tr>
						
						<td class='first' colspan="3">
						<?
						echo "<select name='itemId' id='itemId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Item--</option>";
						if(isset($_GET['p'])){
								$queryItem = "SELECT * FROM item WHERE projectId=".$_GET['p'];
								$items = mysql_query($queryItem,$conexion);
								while($item = mysql_fetch_assoc($items)){
									if(isset($_GET['i'])&&$_GET['i']==$item['itemId'])
										echo "<option selected='selected' value='".$item['itemId']."' >".$item['itemNumber']."</option>";
									else
										echo "<option value='".$item['itemId']."' >".$item['itemNumber']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						
						<td colspan="1">
						<?
						echo "<select name='truckId' id='truckId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Truck--</option>";
						if(isset($_GET['b'])){
								$queryTruck = "SELECT * FROM truck WHERE brokerId=".$_GET['b'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if(isset($_GET['i'])&&$_GET['i']==$truck['itemId'])
										echo "<option selected='selected' value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
									else
										echo "<option value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						<td colspan="1">
						<?
						echo "<select name='driverId' id='driverId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Driver--</option>";
						if(isset($_GET['b'])){
								$queryTruck = "SELECT * FROM driver WHERE brokerId=".$_GET['b'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if(isset($_GET['i'])&&$_GET['i']==$truck['itemId'])
										echo "<option selected='selected' value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
									else
										echo "<option value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						<td></td>
					</tr>
					<tr class='bg' >
						<td><strong>Date:</strong><span style="color:red;">*</span></td>
						<td><strong>MFI:</strong><span style="color:red;">*</span></td>
						<td><strong>Dump/Material Ticket:</strong></td>
						<td><strong>Quantity:</strong><span style="color:red;">*</span> <label id='amounts'>( L/T/H )</label></td>
						<td><strong>Broker Quantity:</strong><span style="color:red;">*</span></td>
						<td></td>
					</tr>
					<tr>
						<td><input type='text' size='10px' id='ticketDate' name='ticketDate' /></td>
						<td><input type='text' size='10px' id='ticketMfi' name='ticketMfi' /></td>
						<td><input type='text' size='10px' id='ticketNumber' name='ticketNumber' /></td>
						<td><input type='text' size='6px' id='ticketAmount' name='ticketAmount' /></td>
						<td><input type='text' size='6px' id='ticketBrokerAmount' name='ticketBrokerAmount' /></td>
						<td><input type='button' value='Submit' id='sub' name='sub' /></td>
					</tr>
					<tr class='bg'>
						<td colspan='7'><label id='itemInfo'>No item information</label></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
			$queryTickets = "
			SELECT
					*
				FROM
					ticket
				JOIN item using (itemId)
				JOIN material using (materialId)
				ORDER BY
					ticketId DESC
				LIMIT 20";
			$terms = mysql_query($queryTickets,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0' id='tickets'>";
				echo "<thead><tr>
						<th class='first'>Job</th>
						<th>Item</th>
						<th>Date</th>
						<th>Truck</th>
						<th>Material</th>
						<th>From</th>
						<th>To</th>
						<th>Ticket</th>
						<th>View</th>
						<th>Edit</th>
						<th class='last'>Delete</th>
					</tr>
					</thead>
					<tbody>";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($terms))
				{
					if($colorFlag)
					{
						echo "<tr>";
						!$colorFlag;
					}
					else
					{
						echo "<tr class='bg'>";
						!$colorFlag;
					}
					$truckInfo = mysql_fetch_assoc(mysql_query("SELECT * from truck JOIN broker using (brokerId) where truckId = ".$term['truckId']));
					echo "
						<td class='first style2'>".$term['projectId']."</td>
						<td class='first style2'>".$term['itemNumber']."</td>
						<td class='first style2'>".to_MDY($term['ticketDate'])."</td>
						<td class='first style2'>".$truckInfo['brokerPid']."-".$truckInfo['truckNumber']."</td>
						<td class='first style2'>".$term['materialName']."</td>
						<td class='first style2'>".$term['itemDisplayFrom']."</td>
						<td class='first style2'>".$term['itemDisplayTo']."</td>
						<td class='first style2'>".$term['ticketMfi']."</td>
						<td><a href='/trucking/php/view/viewTicket.php?i=".$term['ticketId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
							";
					$invoiceTicket = mysql_query("select * from invoiceticket where ticketId = ".$term['ticketId']);
					$reportTicket = mysql_query("select * from reportticket where ticketId = ".$term['ticketId']);
					$supplierInvoiceTicket = mysql_query("select * from supplierinvoiceticket where ticketId = ".$term['ticketId']);
					if(mysql_num_rows($invoiceTicket)==0 && mysql_num_rows($reportTicket)==0 && mysql_num_rows($supplierInvoiceTicket)==0){
					//if($term['docId']==null){
						echo "
							<td><a href='/trucking/php/edit/editTicket.php?i=".$term['ticketId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
							<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ticket #".$term['ticketMfi']."?');\" href='deleteTicket.php?i=".$term['ticketId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
							";
					}else{
						echo "
							<td><a href='/trucking/php/edit/editTicket.php?i=".$term['ticketId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
							<td>--</td>
							";
					}
					echo "</tr>";
				}
				
				
				echo "</tbody></table>";
				echo "</div>";
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
