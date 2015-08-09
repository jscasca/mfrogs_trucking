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
var patternDate = new RegExp(/(\d+)\/(\d+)\/(\d+)/);
patternDate.compile(patternDate);

$(document).ready(function()
{
	$('#brokerId').change(function() {
		var broker=this.value;
		getTrucks(broker);
	});
	
	$('#fuelDate').blur(function(){
		if(evalDate($(this).val())==0){
			alert("Pleae type a valid date");
			setTimeout(function(){ $('#fuelDate').focus();},100);
		}
	});
	
	$('#fuelStart,#fuelFinish').blur(function(){
		getDifference();
	});
	
	$('#ticketAmount').blur(function(){
		var amount = this.value;
		$('#ticketBrokerAmount').attr('value',amount);
	});
	
	$('#submitFuel').click(function(){
		if(validateForm()){
			var brokerId=$('#brokerId').val();
			var truckId=$('#truckId').val();
			var fuelDate=$('#fuelDate').val();
			var fuelComment=$('#fuelComment').val();
			var fuelStart=$('#fuelStart').val();
			var fuelFinish=$('#fuelFinish').val();
			var fuelRegistered=$('#fuelRegistered').val();
			var fuelMileage=$('#fuelMileage').val();
			submit(brokerId,truckId,fuelDate,fuelComment,fuelStart,fuelFinish,fuelRegistered,fuelMileage);
		}
		else{}
			//alert('Missing Data');
	});
	
	$('.deletable').live('click',function(){
		var id = $(this).closest('tr').find('.hiddenId').val();
		deleteFuel(id);
	});
	
	$('.editable').live('click',function(){
		var id = $(this).closest('tr').find('.hiddenId').val();
		window.location = "../edit/editFuel.php?i="+id;
	});
});

function getDifference(){
	if($('#fuelFinish').val()=="")return;
	var difference = parseInt($('#fuelFinish').val())-parseInt($('#fuelStart').val());
	$('#meterDifference').text(difference);
	
}

function evalDate(date){
		if(date.match(patternDate)){
			date=date.replace(patternDate,'$3-$1-$2');
			return date;
		}else{return '0';}
}

function deleteFuel(fuelLoadId){
	$.ajax({
		type: "GET",
		url: "deleteFuel.php",
		data: "i="+fuelLoadId,
		success: function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.deletedId!=null){
					$('#removableFuel'+obj.deletedId).remove();
				}
			}
		},
		async: false
	})
}

function submit(broker,truck,fDate,comment,start,finish,registered,mileage){
	$.ajax({
		type: "GET",
		url: "submitFuel.php",
		data: "brokerId="+broker+"&truckId="+truck+"&fdate="+fDate+"&comment="+escape(comment)+"&start="+start+"&finish="+finish+"&registered="+registered+"&mileage="+mileage,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				console.log(obj.line);
				if(obj.line!=null){
					console.log($('#fuels'));
					$('#fuelsHeader').after(obj.line);
					$('#fuelStart').val($('#fuelFinish').val());
					$('#fuelFinish').val("");
					$('#fuelRegistered').val("");
					$('#fuelMileage').val("");
				}
			}else{alert('Error: '+obj.error);}
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


function validateForm(){
	if($('#fuelDate').val()==""){alert("Please type a date");$('#fuelDate').focus();return false;}
	if($('#truckId').val()==0){alert("Please select a truck");$('#truckId').focus();return false;}
	
	if($('#fuelStart').val()==""){alert("Please type a starting meter value");$('#fuelStart').focus();return false;}
	if($('#fuelFinish').val()==""){alert("Please type a finishing meter value");$('#fuelFinish').focus();return false;}
	if($('#fuelRegistered').val()==""){alert("Please type the registered value");$('#fuelRegistered').focus();return false;}
	if($('#fuelMileage').val()==""){alert("Please type the mileage");$('#fuelMileage').focus();return false;}
	
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
			<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="10">New Ticket</th>
					</tr>
					<tr class='bg' >
						<td>Date:</td>
						<td>Broker:</td>
						<td>Truck:</td>
						<td>Driver:</td>
						<td>Start</td>
						<td>Finish</td>
						<td>Total</td>
						<td>Registered</td>
						<td>Miles</td>
						<td></td>
						
					</tr>
					<tr>
						<td><input type='text' size='6px' id='fuelDate' name='fuelDate' /></td>
						<td colspan='2'>
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
						<br/>
							<?
							echo "<select name='truckId' id='truckId' style='font-family:verdana;font-size:8pt'>";
							echo "<option selected='selected' value='0' >--Select Truck--</option>";
							if(isset($_GET['b'])){
									$queryTruck = "SELECT * FROM truck WHERE brokerId=".$_GET['b'];
									$trucks = mysql_query($queryTruck,$conexion);
									while($truck = mysql_fetch_assoc($trucks)){
											echo "<option value='".$truck['truckId']."' >".$truck['truckNumber']."</option>";
									}
							}
							
							echo "</select>";
							?>
						</td>
						<td><input type='text' size='5px' id='fuelComment' name='fuelComment' /></td>
						<td><input type='text' size='4px' id='fuelStart' name='fuelStart' /></td>
						<td><input type='text' size='4px' id='fuelFinish' name='fuelFinish' /></td>
						<td><label id='meterDifference'></label></td>
						<td><input type='text' size='3px' id='fuelRegistered' name='fuelRegistered' /></td>
						<td><input type='text' size='5px' id='fuelMileage' name='fuelMileage' /></td>
						<td><button id='submitFuel' >Submit</button></td>
					</tr>
					<tr class='bg'>
						<td colspan='10'><label id='fuelInfo'></label></td>
					</tr>
				</table>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
			$queryTickets = "
			SELECT
					*
				FROM
					fuel_load
				ORDER BY
					fuelLoadDate DESC
				LIMIT 20";
			$terms = mysql_query($queryTickets,$conexion);
			$numTerms = mysql_num_rows($terms);
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0' id='fuels'>";
				echo "<tr id='fuelsHeader'>
						<th class='first'>Date</th>
						<th>Truck</th>
						<th>Driver</th>
						<th>Start</th>
						<th>Finish</th>
						<th>Total</th>
						<th>Registered</th>
						<th>Mileage</th>
						<th>Edit</th>
						<th class='last'>Delete</th>
					</tr>
					";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($terms))
				{
					if($colorFlag) {
						$colorClass = "";
					} else {
						$colorClass = "class='bg'";
					}
					$broker = mysql_fetch_assoc(mysql_query("select brokerPid from broker where brokerId=".$term['brokerId'],$conexion));
					$truck = mysql_fetch_assoc(mysql_query("select truckNumber from truck where truckId=".$term['truckId'],$conexion));
	
					
					echo"<tr id='removableFuel".$term['fuelLoadId']."' $colorClass >";
					echo"<td class=fisrt'>".to_MDY($term['fuelLoadDate'])."<input type='hidden' value='".$term['fuelLoadId']."' class='hiddenId' /></td>";
					echo"<td >".$broker['brokerPid']."-".$truck['truckNumber']."</td>";
					echo"<td >".$term['fuelLoadCommet']."</td>";
					echo"<td >".$term['fuelLoadStart']."</td>";
					echo"<td >".$term['fuelLoadFinish']."</td>";
					echo"<td >".($term['fuelLoadFinish']-$term['fuelLoadStart'])."</td>";
					echo"<td >".$term['fuelLoadRegistered']."</td>";
					echo"<td >".$term['fuelLoadMileage']."</td>";
					echo"<td><img src='/trucking/img/13.png' width='20' height='20' class='editable' /></td>";
					echo"<td class='last'><img src='/trucking/img/118.png' width='20' height='20' class='deletable' /></td>";
					echo"</tr>";
					$colorFlag = !$colorFlag;
				}
				
				
				echo "</table>";
				echo "</div>";
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
