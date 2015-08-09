<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Driver";
$description = "Driver Information.";

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

if(isset($_GET['i']))
{
###############To Edit###############
$queryLast =
	"SELECT 
		* 
	FROM
 		driver
 	JOIN (select brokerName, brokerPid, brokerId from broker) as b using (brokerId)
	JOIN address USING (addressId)
	JOIN term USING (termId)
	LEFT JOIN carrier USING (carrierId)
	LEFT JOIN ethnic USING (ethnicId)
	LEFT JOIN work USING (workId)
	WHERE
		driverId=".$_GET['i'];
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);
##########################################
}

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
var patternLetters = new RegExp(/driver/);
patternLetters.compile(patternLetters);

var driverDec = false;


$(document).ready(function()
{
	$('#drivers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editDriver.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});
	
	$('#brokerId').change(function(){getDrivers();});
	$('#brokerPid').change(function(){getDrivers();});
	$('#brokerPid').keyup(function(){getDrivers();});
	$('#driverName').change(function(){getDrivers();});
	$('#driverName').keyup(function(){getDrivers();});
	$('#driverStatus').change(function(){getDrivers();});
	
		$('#driverDeclar').dblclick(function(){
		if(driverDec){
			$('#uploaddriDeclar').remove();
			driverDec = false;
		}else{
			$('#driverDeclar tr:first').after(
				"<tr id='uploaddriDeclar'><td><div id='framedriDeclar'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameDriDeclar',
				id: 'frameDriDeclar',
				src: 'idriverDeclaration.php?driverId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#framedriDeclar');
			driverDec = true;
		}
	});

});


function getDrivers(){
	
	var brokerId=$('#brokerId').val();
	var brokerPid=$('#brokerPid').val();
	var driverName=$('#driverName').val();
	var driverStatus=$('#driverStatus').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewDrivers.php",
		data: "brokerId="+brokerId+"&brokerPid="+brokerPid+"&driverName="+driverName+"&driverStatus="+driverStatus,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#drivers > tbody:last').remove();
					$('#drivers').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function validateForm(){

	if(document.getElementById('driverFirst').value.length==0){
		alert("Please type a name for the driver");
		document.formValidate.driverFirst.focus
		return false;
	}
	if(document.getElementById('driverLast').value.length==0){
		alert("Please type a name for the driver");
		document.formValidate.driverLast.focus
		return false;
	}
	if(document.getElementById('termId').selectedIndex==0 ){
		alert("Please select a payment term");
			document.formValidate.termId.focus
			return false;
	}
	if(document.getElementById('brokerId').selectedIndex==0 ){
		alert("Please select a broker");
			document.formValidate.brokerId.focus
			return false;
	}
	if(document.getElementById('driverSSN').value.length==0){
		alert("Please fill the SSN information");
		document.formValidate.driverSSN.focus
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
		<?
		if(isset($_GET['i']))
		{
		?>
		
			<div class="top-bar">
				
				<a href="#" class="broker"></a>
				<a onclick="return confirm('Are you sure you want to delete <?echo$lastVal['driverLastName'];?>?');"  href="deleteDriver.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editDriver.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitEditDriver.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['driverId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Driver Information</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Broker:</strong></td>
						<td class="last">
						<?
						$queryTerm = "select * from broker where brokerStatus=1";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms > 0)
						{
							
							if(!isset($_GET['i']))
							{
								echo "<option selected='selected'>--Select broker--</option>";
								while($term=mysql_fetch_assoc($terms))
								{
									echo "<option value='{$term['brokerId']}'>{$term['brokerName']}</option>";
								}
							}
							else
							{	
								while($term=mysql_fetch_assoc($terms))
								{
									if($lastVal['brokerId']==$term['brokerId'])
										echo "<option selected='selected' value='{$term['brokerId']}'>{$term['brokerName']}</option>";
									else
										echo "<option value='{$term['brokerId']}'>{$term['brokerName']}</option>";
								}
							}
						}
						else
						{
							echo "<option selected='selected'>There are no brokers in the DataBase</option>";
							
						}
						echo "</select>";
						?></td>
					</tr>
					<tr>
						<td class="first"><strong>First Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverFirstName' name='driverFirstName' <?if(isset($lastVal['driverFirstName']))echo"value='".$lastVal['driverFirstName']."'";?> /></td>
					</tr>
					<tr class='bg'>
						<td class="first"><strong>Last Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverLastName' name='driverLastName' <?if(isset($lastVal['driverLastName']))echo"value='".$lastVal['driverLastName']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>SSN:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverSSN' name='driverSSN' <?if(isset($lastVal['driverSSN']))echo"value='".$lastVal['driverSSN']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><input type="text" class="text" id='driverTel' name='driverTel' <?if(isset($lastVal['driverTel']))echo "value='".showPhoneNumber($lastVal['driverTel'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Mobile:</strong></td>
						<td class="last"><input type="text" class="text" id='driverMobile' name='driverMobile' <?if(isset($lastVal['driverMobile']))echo "value='".showPhoneNumber($lastVal['driverMobile'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Carrier:</strong></td>
						<td class="last">
						<?
						$queryTerm = "select * from carrier order by carrierName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='carrierId' id='carrierId' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select carrier--</option>";
						if($countTerms > 0)
						{
							
							if(!isset($_GET['i']))
							{
								while($term=mysql_fetch_assoc($terms))
								{
									echo "<option value='{$term['carrierId']}'>{$term['carrierName']}</option>";
								}
							}
							else
							{	
								while($term=mysql_fetch_assoc($terms))
								{
									if($lastVal['carrierId']==$term['carrierId'])
										echo "<option selected='selected' value='{$term['carrierId']}'>{$term['carrierName']}</option>";
									else
										echo "<option value='{$term['carrierId']}'>{$term['carrierName']}</option>";
								}
							}
						}
						else
						{
							echo "<option selected='selected'>There are no carriers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail:</strong></td>
						<td class="last"><input type="text" class="text" id='driverMail' name='driverMail' <?if(isset($lastVal['driverMail']))echo "value='".$lastVal['driverMail']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1' <?if(isset($lastVal['addressLine1']))echo "value='".$lastVal['addressLine1']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2' <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity' <?if(isset($lastVal['addressCity']))echo"value='".$lastVal['addressCity']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from state";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='addressState' id='addressState' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select State--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['addressState'])&& $lastVal['addressState'] ==$state['stateId'])
								echo "<option selected='selected' value='{$state['stateId']}'>{$state['stateName']}</option>";
							else
								echo "<option value='{$state['stateId']}'>{$state['stateName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip' <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox' <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?>/></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Pay Term:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryState = "select * from term";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='termId' id='termId' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select Term--</option>";
						while($term=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['termId'])&& $lastVal['termId'] ==$term['termId'])
								echo "<option selected='selected' value='{$term['termId']}'>{$term['termName']}</option>";
							else
								echo "<option value='{$term['termId']}'>{$term['termName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Percentage:</strong></td>
						<td class="last"><input type="text" class="text" id='driverPercentage' name='driverPercentage' <?if(isset($lastVal['driverPercentage']))echo"value='".$lastVal['driverPercentage']."'";?>/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Start Date:</strong></td>
						<td class="last"><input type="text" class="text" id='driverStartDate' name='driverStartDate' <?if(isset($lastVal['driverStartDate']))echo"value='".to_MDY($lastVal['driverStartDate'])."'";?>/></td>
					</tr>
					<tr>
						<td class="first"><strong>Status:</strong></td>
						<td class="last">
							<label>
							Active<input type='radio' name='driverStatus' id='radioActive' value='1' <?if(isset($lastVal['driverStatus'])&&$lastVal['driverStatus'])echo"checked";?> />
							Inactive<input type='radio' name='driverStatus' id='radioInactive' value='0' <?if(isset($lastVal['driverStatus'])&&!$lastVal['driverStatus'])echo"checked";?> />
							
							</label>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Gender:</strong></td>
						<td class="last">
							<select name="driverGender" id="driverGender" style='font-family:verdana;font-size:8pt'>
								<option value="Male" <?echo ($lastVal['driverGender']=="Male"?"selected='selected'":"");?> >Male</option>
								<option value="Female" <?echo ($lastVal['driverGender']=="Female"?"selected='selected'":"");?> >Female</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Ethnicity:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from ethnic";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='ethnicId' id='ethnicId' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select Ethnicity--</option>";
						while($term=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['ethnicId'])&& $lastVal['ethnicId'] ==$term['ethnicId'])
								echo "<option selected='selected' value='{$term['ethnicId']}'>{$term['ethnicName']}</option>";
							else
								echo "<option value='{$term['ethnicId']}'>{$term['ethnicName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Class:</strong></td>
						<td class="last">
							<select name="driverClass" id="driverClass" style='font-family:verdana;font-size:8pt'>
								<option value="1" <?echo ($lastVal['driverClass']=="1"?"selected='selected'":"");?> >1</option>
								<option value="2" <?echo ($lastVal['driverClass']=="2"?"selected='selected'":"");?> >2</option>
								<option value="3" <?echo ($lastVal['driverClass']=="3"?"selected='selected'":"");?> >3</option>
								<option value="4" <?echo ($lastVal['driverClass']=="4"?"selected='selected'":"");?> >4</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Work:</strong></td>
						<td class="last">
							<?
						$queryState = "select * from work";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='workId' id='workId' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select workity--</option>";
						while($term=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['workId'])&& $lastVal['workId'] ==$term['workId'])
								echo "<option selected='selected' value='{$term['workId']}'>{$term['workName']}</option>";
							else
								echo "<option value='{$term['workId']}'>{$term['workName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Union:</strong></td>
						<td class="last">
							<?
						$queryState = "select * from worker_union";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='unionId' id='unionId' style='font-family:verdana;font-size:8pt'>";
						echo "<option value='0' >NO UNION</option>";
						while($term=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['unionId'])&& $lastVal['unionId'] ==$term['unionId'])
								echo "<option selected='selected' value='{$term['unionId']}'>{$term['unionName']}</option>";
							else
								echo "<option value='{$term['unionId']}'>{$term['unionName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Driver Rate:</strong></td>
						<td class="last"><input type="text" class="text" id='driverPW' name='driverPW' <?if(isset($lastVal['driverPW']))echo"value='".decimalPad($lastVal['driverPW'])."'";?>/></td>
					</tr>
					<tr>
						<td class="first"><strong>Driver Remaninig working time (731):</strong></td>
						<td class="last">
							<input type="text" class="text" 
							id='driverRemaining731' name='driverRemaining731' 
							<?
							$firstRemaining = mysql_fetch_assoc(mysql_query("select * from remainings_731 where driverId=".$lastVal['driverId']." and remainingStartDate='0000-00-00' limit 1",$conexion));
							
							echo"value='".($firstRemaining==null?"0.00": decimalPad($firstRemaining['remainingValue']))."'";
							?>/>
							
							</td>
					</tr>
				</table>
				<table>
				<tr>
				<td><input type='reset' value="Reset" ></td>
				<td><input type='submit' value="Submit" ></td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from driverdeclaration JOIN filepath where driverId = ".$_GET['i']." ORDER BY driverDeclarationDate desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last driver Declaration date is:<Strong>".to_MDY($result['driverDeclarationDate'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no driver declarations";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='driverDeclar'>
					<tr>
						<th class='full'>Driver Declaration</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
		<?
		}
		else
		{
		?>	
							<div class='table' id='searchBar'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
				<tr>
					<th class="full"  colspan='5'>Search Drivers</th>
				</tr>
				<tr>
					<td>Broker</td>
					<td>Unique Identifier</td>
					<td>Name</td>
					<td>Status</td>
				</tr>
				<tr>
					<td class="last">
						<?
						$queryTerm = "select * from broker where brokerStatus=1 order by brokerName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option value='0'>--Select Broker--</option>";
						if($countTerms > 0)
						{
							while($term=mysql_fetch_assoc($terms))
							{
								echo "<option value='{$term['brokerId']}'>{$term['brokerName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected'>There are no brokers in the DataBase</option>";
							
						}
						echo "</select>";
						?></td>
					<td><input type='text' size='8px' id='brokerPid' name='brokerPid' /></td>
					<td><input type='text' size='8px' id='driverName' name='driverName' /></td>
					<td>
							<select name='driverStatus' id='driverStatus' style='font-family:verdana;font-size:8pt' >
								<option value='0' >All</option>
								<option value='1' >Active</option>
								<option value='2' >Inactive</option>
							</select>
						</td>
				</tr>
				
				</table>
			</div>
			
		<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='drivers'>
				<tr>
					<th class="full"  colspan='9'>DRIVERS</th>
				</tr>
				<tr>
					<th>UID</th>
					<th>Name</th>
					<th>UID</th>
					<th>Name</th>
				</tr>
				<tbody>
				<?
				$brokers = mysql_query("select * from driver JOIN broker using (brokerId) order by brokerPid,driverLastName",$conexion);
				$numBrokers=mysql_num_rows($brokers);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numBrokers>0)
				{
						while($broker=mysql_fetch_assoc($brokers))
						{
							if($actual){
								echo "<tr>";
								echo "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								echo "<td ".$tdClass." id='driver".$broker['driverId']."'>".$broker['driverLastName'].", ".$broker['driverFirstName']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								echo "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								echo "<td ".$tdClass." id='driver".$broker['driverId']."'>".$broker['driverLastName'].", ".$broker['driverFirstName']."</td>";
								echo "</tr>";
							}
							$actual=!$actual;
								
						}
						if(!$actual){
							echo"<td ".$tdClass." colspan='2'></td></tr>";
						}
				}
				?>
				</tbody>
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
