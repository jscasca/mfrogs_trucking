<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Truck";
$description = "Add a new truck. Trucks have to be assigned to a broker. Values marked with <span style='color:red;'>*</span> are mandatory. To upload files doble click the table header of the file you want to upload.";

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
		truck
	JOIN address using(addressId)
	WHERE
		truckId=".$_GET['i'];
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
var patternLetters = new RegExp(/truck/);
patternLetters.compile(patternLetters);

var truckIcc = false;
var truckIns = false;
var trailerPlates = false;

$(document).ready(function()
{
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this ticket?');
	});
	$('#trucks tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editTruck.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});

	$('#brokerId').change(function(){getTrucks();});
	$('#truckNumber').keyup(function(){getTrucks();});
	
	$('#truckICC').dblclick(function(){
		if(truckIcc){
			$('#uploadICC').remove();
			truckIcc = false;
		}else{
			$('#truckICC tr:first').after(
				"<tr id='uploadICC'><td><div id='frameICC'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTICC',
				id: 'frameTICC',
				src: 'itruckICC.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frameICC');
			truckIcc = true;
		}
		
	});
	
	$('#trailerPlate').dblclick(function(){
		if(truckIcc){
			$('#uploadtrailerPlate').remove();
			truckIcc = false;
		}else{
			$('#trailerPlate tr:first').after(
				"<tr id='uploadtrailerPlate'><td><div id='frametraP'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTraPl',
				id: 'frameTraPl',
				src: 'itrailerPlates.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametraP');
			truckIcc = true;
		}
		
	});
	
	$('#truckPlate').dblclick(function(){
		if(truckIcc){
			$('#uploadtruckPlate').remove();
			truckIcc = false;
		}else{
			$('#truckPlate tr:first').after(
				"<tr id='uploadtruckPlate'><td><div id='frametruP'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTruPl',
				id: 'frameTrupl',
				src: 'itruckPlates.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametruP');
			truckIcc = true;
		}
		
	});
	
	$('#truckIns').dblclick(function(){
		if(truckIns){
			$('#uploadIns').remove();
			truckIns = false;
		}else{
			$('#truckIns tr:first').after(
				"<tr id='uploadIns'><td><div id='frameIns'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTIns',
				id: 'frameTIns',
				src: 'itruckInsurance.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frameIns');
			truckIns = true;
		}
	});
	
	$('#appCard').dblclick(function(){
		if(truckIns){
			$('#uploadAppCard').remove();
			truckIns = false;
		}else{
			$('#appCard tr:first').after(
				"<tr id='uploadAppCard'><td><div id='frameAppCard'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameCard',
				id: 'frameCard',
				src: 'iappCard.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frameAppCard');
			truckIns = true;
		}
	});
	
	$('#swp').dblclick(function(){
		if(truckIns){
			$('#uploadSwp').remove();
			truckIns = false;
		}else{
			$('#swp tr:first').after(
				"<tr id='uploadSwp'><td><div id='frameswp'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameSwp',
				id: 'frameSwp',
				src: 'iswp.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frameswp');
			truckIns = true;
		}
	});
	
	$('#trailerInspec').dblclick(function(){
		if(truckIns){
			$('#uploadtraInspec').remove();
			truckIns = false;
		}else{
			$('#trailerInspec tr:first').after(
				"<tr id='uploadtraInspec'><td><div id='frametraInspec'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTraInspec',
				id: 'frameTraInspec',
				src: 'itrailerInspection.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametraInspec');
			truckIns = true;
		}
	});
	
	$('#truckInspec').dblclick(function(){
		if(truckIns){
			$('#uploadtruInspec').remove();
			truckIns = false;
		}else{
			$('#truckInspec tr:first').after(
				"<tr id='uploadtruInspec'><td><div id='frametruInspec'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTruInspec',
				id: 'frameTruInspec',
				src: 'itruckInspection.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametruInspec');
			truckIns = true;
		}
	});
	
	$('#trailerVin').dblclick(function(){
		if(truckIns){
			$('#uploadtraVin').remove();
			truckIns = false;
		}else{
			$('#trailerVin tr:first').after(
				"<tr id='uploadtraVin'><td><div id='frametraVin'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTraVin',
				id: 'frameTraVin',
				src: 'itrailerVin.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametraVin');
			truckIns = true;
		}
	});
	
	$('#truckVin').dblclick(function(){
		if(truckIns){
			$('#uploadtruVin').remove();
			truckIns = false;
		}else{
			$('#truckVin tr:first').after(
				"<tr id='uploadtruVin'><td><div id='frametruVin'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameTruVin',
				id: 'frameTruVin',
				src: 'itruckVin.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frametruVin');
			truckIns = true;
		}
	});
	
	$('#fuelCard').dblclick(function(){
		if(truckIns){
			$('#uploadfuelCard').remove();
			truckIns = false;
		}else{
			$('#fuelCard tr:first').after(
				"<tr id='uploadfuelCard'><td><div id='framefuelCard'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameFuelCard',
				id: 'frameFuelCard',
				src: 'ifuelCard.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#framefuelCard');
			truckIns = true;
		}
	});
	
	$('#ucr').dblclick(function(){
		if(truckIns){
			$('#uploaducr').remove();
			truckIns = false;
		}else{
			$('#ucr tr:first').after(
				"<tr id='uploaducr'><td><div id='frameucr'></div></td></tr>"
			);
			$('<iframe />',{
				name: 'frameUcr',
				id: 'frameUcr',
				src: 'iucr.php?truckId='+<? echo (isset($_GET['i'])?$_GET['i']: "0"); ?>
			}).width('100%').height('50px').appendTo('#frameucr');
			truckIns = true;
		}
	});

});

$(function(){
	function callToUpdate(){
		alert("call to update");
	}
	
	callParentUpdate=function(){callToUpdate();};
});

function getTrucks(){
	
	var brokerId=$('#brokerId').val();
	var truckNumber=$('#truckNumber').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getEditTrucks.php",
		data: "brokerId="+brokerId+"&truckNumber="+truckNumber,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#trucks > tbody:last').remove();
					$('#trucks').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}	

function validateForm(){
	<? 
	if(!isset($_GET['i']))
	{
	?>
	if(document.getElementById('brokerId').selectedIndex==0 ){
		alert("Please select a broker for this truck");
			document.formValidate.brokerId.focus;
			return false;
	}
	
	<?
	}
	?>
	if(document.getElementById('truckNumber').value.length==0){
		alert("Please type a number for this truck");
		document.formValidate.truckNumber.focus;
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
				<a href="#" class="truck"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditTruck.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['truckId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Truck</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Broker:</strong><span style="color:red;">*</span></td>
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
						<td class="first" width="172"><strong>Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='truckNumber' name='truckNumber'  <?if(isset($lastVal['truckNumber']))echo"value='".$lastVal['truckNumber']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Driver:</strong></td>
						<td class="last"><input type="text" class="text" id='truckDriver' name='truckDriver' <?if(isset($lastVal['truckDriver']))echo"value='".$lastVal['truckDriver']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Plates:</strong></td>
						<td class="last"><input type="text" class="text" id='truckPlates' name='truckPlates' <?if(isset($lastVal['truckPlates']))echo"value='".$lastVal['truckPlates']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1' <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2'  <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?>/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity' <?if(isset($lastVal['addressCity']))echo"value='".$lastVal['addressCity']."'";?>  /></td>
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
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip'  <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?>  /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox'  <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?>  /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Additional Information:</strong></td>
						<td class="last"><textarea rows="2" cols="43" class="text" id='truckInfo' name='truckInfo' /><?if(isset($lastVal['truckInfo']))echo$lastVal['truckInfo'];?> </textarea></td>
					</tr>
					<tr>
						<td class="first"><strong>Features</strong></td>
						<td class="last">
							<?
							$getFeatures="SELECT * FROM feature LEFT JOIN (SELECT * FROM truckfeature where truckId=".$lastVal['truckId'].") as tf using (featureId) ";
							$features=mysql_query($getFeatures,$conexion);
							while($feature=mysql_fetch_assoc($features))
							{
								echo "<input type='checkbox' id='".$feature['featureName']."' value='".$feature['featureId']."' name='truckFeatures[]'";
								if($feature['truckId']!=null) echo "checked";
								echo" ><label for='".$feature['featureName']."' >".$feature['featureName']."</label><br />";
							}
							?>
						</td>
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
			
			<div class='table'>
				<?
				$queryICC = "select * from truckicc JOIN filepath where truckId = ".$_GET['i']." ORDER BY truckICCId desc limit 1";
				$results = mysql_query($queryICC,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIcc = "Last ICC Number:<Strong>".$result['truckICCNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIcc = "there are no ICC files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckICC'>
					<tr>
						<th class='full'>ICC</th>
					</tr>
					<tr>
						<td><?echo $lastIcc;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from insurance JOIN filepath where truckId = ".$_GET['i']." ORDER BY insuranceId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last Insurance Date/Expiration:<Strong>".to_MDY($result['insuranceDate'])."-".to_MDY($result['insuranceExp'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no Insurance files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckIns'>
					<tr>
						<th class='full'>Insurance</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from trailerplates JOIN filepath where truckId = ".$_GET['i']." ORDER BY trailerPlatesId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last trailer plates Number:<Strong>".$result['trailerPlatesNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no trailer plates files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='trailerPlate'>
					<tr>
						<th class='full'>Trailer Plates</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from truckplates JOIN filepath where truckId = ".$_GET['i']." ORDER BY truckPlatesId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last truck plates Number:<Strong>".$result['truckPlatesNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no truck plates files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckPlate'>
					<tr>
						<th class='full'>Truck Plates</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from appidcard JOIN filepath where truckId = ".$_GET['i']." ORDER BY appCardId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last application card expires in:<Strong>".to_MDY($result['appCardExp'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no truck application cards for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='appCard'>
					<tr>
						<th class='full'>Application Card</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from swp JOIN filepath where truckId = ".$_GET['i']." ORDER BY swpId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last Special Waste Permit expires in:<Strong>".to_MDY($result['swpExp'])."</Strong> with number <Strong>".$result['swpNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no speacial waste permits for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='swp'>
					<tr>
						<th class='full'>Special Waste Permit</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from trailerinspection JOIN filepath where truckId = ".$_GET['i']." ORDER BY trailerInspection desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last trailer Inspection date is:<Strong>".to_MDY($result['trailerInspectionDate'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no trailer inspections for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='trailerInspec'>
					<tr>
						<th class='full'>Trailer Inspection</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from trailerregistration JOIN filepath where truckId = ".$_GET['i']." ORDER BY trailerRegistrationId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "<Strong>".($result['trailerRegistrationAct']?"Active trailer Resgitration":"")."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no trailer registration files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='trailerReg'>
					<tr>
						<th class='full'>Trailer Registration</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from trailervin JOIN filepath where truckId = ".$_GET['i']." ORDER BY trailerVinId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last trailer VIN #:<Strong>".$result['trailerVinNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no trailer VIN files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='trailerVin'>
					<tr>
						<th class='full'>Trailer VIN</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from trailerinspection JOIN filepath where truckId = ".$_GET['i']." ORDER BY trailerInspection desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last truck Inspection date is:<Strong>".to_MDY($result['truckInspectionDate'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no truck inspections for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckInspec'>
					<tr>
						<th class='full'>Truck Inspection</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from truckregistration JOIN filepath where truckId = ".$_GET['i']." ORDER BY truckRegistrationId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "<Strong>".($result['truckRegistrationAct']?"Active Truck Resgitration":"")."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no truck registration files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckReg'>
					<tr>
						<th class='full'>Truck Registration</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from truckvin JOIN filepath where truckId = ".$_GET['i']." ORDER BY truckVinId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last truck VIN #:<Strong>".$result['truckVinNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no truck VIN files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='truckVin'>
					<tr>
						<th class='full'>Truck VIN</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from fuelcard JOIN filepath where truckId = ".$_GET['i']." ORDER BY fuelCardId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "Last Fuel Card #:<Strong>".$result['fuelCardNumber']."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no fuel Cards files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='fuelCard'>
					<tr>
						<th class='full'>Fuel Card</th>
					</tr>
					<tr>
						<td><?echo $lastIns;?></td>
					</tr>
				</table>
			</div>
			
			<div class='table'>
				<?
				$queryIns = "select * from ucr JOIN filepath where truckId = ".$_GET['i']." ORDER BY ucrId desc limit 1";
				$results = mysql_query($queryIns,$conexion);
				if(mysql_num_rows($results)>0){
					$result = mysql_fetch_assoc($results);
					$lastIns = "The last UCR expiration date is:<Strong>".to_MDY($result['ucrExp'])."</Strong> uploaded on ".to_MDY($result['filepathDate']);
				}else{
					$lastIns = "there are no UCR files for this truck";
				}
				?>
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing' cellpadding='0' cellspacing='0' id='ucr'>
					<tr>
						<th class='full'>UCR</th>
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
					<th class="full"  colspan='5'>Search Trucks</th>
				</tr>
				<tr>
					<td>Broker</td>
					<td>Number</td>
					
				</tr>
				<tr>
					<td>
							<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt' >
							<option value='0'>--Select Broker--</option>
							<?
							$brokers = mysql_query("select * from broker",$conexion);
							while($broker=mysql_fetch_assoc($brokers)){
								echo "<option value='".$broker['brokerId']."' >".$broker['brokerName']."</option>";
							}
							?>
							</select>
						</td>
					<td><input type='text' size='8px' id='truckNumber' name='truckNumber' /></td>
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='trucks'>
				<tr>
					<th class="full"  colspan='9'>TRUCKS</th>
				</tr>
				<tr>
					<th>Number</th>
					<th>Driver</th>
					<th>Number</th>
					<th>Driver</th>
				</tr>
				<tbody>
				<?
				$trucks = mysql_query("select * from truck join broker using (brokerId)",$conexion);
				$numtrucks=mysql_num_rows($trucks);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numtrucks>0)
				{
						while($truck=mysql_fetch_assoc($trucks))
						{
							if($actual){
								echo "<tr>";
								echo "<td ".$tdClass." id='truck".$truck['truckId']."'>".$truck['brokerPid']."-".$truck['truckNumber']."</td>";
								echo "<td>".$truck['truckDriver']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								echo "<td ".$tdClass." id='truck".$truck['truckId']."'>".$truck['brokerPid']."-".$truck['truckNumber']."</td>";
								echo "<td>".$truck['truckDriver']."</td>";
								echo "</tr>";
							}
							$actual=!$actual;
						}
						if(!$actual){
							echo"<td ".$tdClass." colspan='1'></td></tr>";
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
