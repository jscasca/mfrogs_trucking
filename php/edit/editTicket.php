<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
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

if(isset($_GET['i'])){
$queryLast="
SELECT
	*
FROM
	ticket
JOIN truck using (truckId)
JOIN broker using (brokerId)
JOIN item using (itemId)
JOIN (select projectId, projectName from project) p using (projectId)
WHERE
	ticketId=".$_GET['i'];	
	//echo $queryLast;
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);
}
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
	
	$('#projectId').change(function() {
		var project=this.value;
		getItems(project);
	});
	$('#itemId').change(function() {
		var item=this.value;
		getItemInfo(item);
	});
});

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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditTicket.php?i=<?if(isset($lastVal['ticketId']))echo$lastVal['ticketId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="7">Edit Ticket</th>
					</tr>
					<tr class='bg' >
						<td colspan='3' class='first'>
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Project--</option>";
						$queryProject = "SELECT * FROM project JOIN customer using (customerId) where projectInactive=0 order by customerName";
						$projects = mysql_query($queryProject,$conexion);
						while($project=mysql_fetch_assoc($projects)){
								if(isset($lastVal['projectId'])&&$lastVal['projectId']==$project['projectId'])
									echo "<option selected='selected' value='".$project['projectId']."' >".$project['customerName']." / ".$project['projectName']."</option>";
								else
									echo "<option value='".$project['projectId']."' >".$project['customerName']." / ".$project['projectName']."</option>";
						}
						echo "</select>";
						?><span style="color:red;">*</span>
						</td>
						<td colspan='2' >
						<?
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Broker--</option>";
						$queryBroker = "SELECT * FROM broker where brokerStatus=1";
						$brokers = mysql_query($queryBroker,$conexion);
						while($broker=mysql_fetch_assoc($brokers)){
								if(isset($lastVal['brokerId'])&&$lastVal['brokerId']==$broker['brokerId'])
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
						
						<td colspan='3' class='first'>
						<?
						echo "<select name='itemId' id='itemId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Item--</option>";
						if(isset($_GET['p'])||isset($lastVal['projectId'])){
								$queryItem = "SELECT * FROM item JOIN material using (materialId) JOIN supplier using (supplierId) WHERE projectId=".$lastVal['projectId'];
								$items = mysql_query($queryItem,$conexion);
								while($item = mysql_fetch_assoc($items)){
									$option = "(".$item['itemNumber'].") ".$item['materialName']." from/to ".$item['supplierName'];
									if((isset($_GET['it'])&&$_GET['it']==$item['itemId'])|| (isset($lastVal['itemId']) && $lastVal['itemId']==$item['itemId']))
										echo "<option selected='selected' value='".$item['itemId']."' >$option</option>";
									else
										echo "<option value='".$item['itemId']."' >$option</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						
						<td colspan='2' >
						<?
						echo "<select name='truckId' id='truckId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Truck--</option>";
						if(isset($_GET['b'])||isset($lastVal['brokerId'])){
								$queryTruck = "SELECT * FROM truck WHERE brokerId=".$lastVal['brokerId'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if((isset($_GET['tr'])&&$_GET['tr']==$truck['truckId'])||(isset($lastVal['truckId'])&&$lastVal['truckId']==$truck['truckId']) )
										echo "<option selected='selected' value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
									else
										echo "<option value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
						
						<td>
						<?
						echo "<select name='driverId' id='driverId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Driver--</option>";
						if(isset($_GET['b'])||isset($lastVal['driverId'])){
								$queryTruck = "SELECT * FROM driver WHERE brokerId=".$lastVal['brokerId'];
								$trucks = mysql_query($queryTruck,$conexion);
								while($truck = mysql_fetch_assoc($trucks)){
									if((isset($_GET['tr'])&&$_GET['tr']==$truck['driverId'])||(isset($lastVal['driverId'])&&$lastVal['driverId']==$truck['driverId']) )
										echo "<option selected='selected' value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
									else
										echo "<option value='".$truck['driverId']."' >".$truck['driverLastName'].", ".$truck['driverFirstName']."</option>";
								}
						}
						
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class='bg' >
						<td><strong>Date:</strong><span style="color:red;">*</span></td>
						<td><strong>MFI:</strong><span style="color:red;">*</span></td>
						<td><strong>Ticket:</strong></td>
						<td><strong>Quantity:</strong><span style="color:red;">*</span> <label id='amounts'>( L/T/H )</label></td>
						<td><strong>Broker Quantity:</strong><span style="color:red;">*</span></td>
						<td></td>
					</tr>
						<td><input type='text' size='10px' id='ticketDate' name='ticketDate' <?if(isset($lastVal['ticketDate']))echo "value='".to_MDY($lastVal['ticketDate'])."'"; ?>/></td>
						<td><input type='text' size='10px' id='ticketMfi' name='ticketMfi' <?if(isset($lastVal['ticketMfi']))echo "value='".$lastVal['ticketMfi']."'"; ?> /></td>
						<td><input type='text' size='10px' id='ticketNumber' name='ticketNumber' <?if(isset($lastVal['ticketNumber']))echo "value='".$lastVal['ticketNumber']."'"; ?>/></td>
						<td><input type='text' size='6px' id='ticketAmount' name='ticketAmount' <?if(isset($lastVal['ticketAmount']))echo "value='".decimalPad($lastVal['ticketAmount'])."'"; ?>/></td>
						<td><input type='text' size='6px' id='ticketBrokerAmount' name='ticketBrokerAmount' <?if(isset($lastVal['ticketBrokerAmount']))echo "value='".decimalPad($lastVal['ticketBrokerAmount'])."'"; ?>/></td>
						<td><input type='submit' value='Submit' id='sub' name='sub' /></td>
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
				JOIN (select brokerPid, truckId, truckNumber from truck JOIN broker using (brokerId)) as T using (truckId)
				JOIN material using (materialId)
				ORDER BY
					ticketId DESC
				LIMIT 20
				";
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
					$truckInfo = mysql_query("SELECT * from truck JOIN broker using (brokerId) where truckId = ".$term['truckId']);
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
