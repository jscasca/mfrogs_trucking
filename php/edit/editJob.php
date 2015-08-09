<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Project";
$description = "Edit job/project. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
 		project
	JOIN address USING (addressId)
	LEFT JOIN jobland USING (jobLandId)
	LEFT JOIN jobterrain USING (jobTerrainId)
	LEFT JOIN (SELECT customerId, customerName FROM customer) as c USING (customerId)
	WHERE
		projectId=".$_GET['i'];
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
var patternLetters = new RegExp(/project/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('#customer').change(function() {
		var customer=this.value;
		getContacts(customer);
	});
	
	$('#projects tr td').live('dblclick',function(){
		var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editJob.php?i="+id);
			}
	});
	
	$('#searchId').keyup(function(){getProjects();});
	$('#searchName').keyup(function(){getProjects();});
	$('#searchAddress').keyup(function(){getProjects();});
	$('#searchCity').keyup(function(){getProjects();});
	$('#searchActive').change(function(){getProjects();});
});

function getProjects(){
	var projectId=$('#searchId').val();
	var projectName=$('#searchName').val();
	var projectActive=$('#searchActive').val();
	var addressLine=$('#searchAddress').val();
	var addressCity=$('#searchCity').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getEditProjects.php",
		data: "projectId="+projectId+"&projectName="+projectName+"&projectActive="+projectActive+"&addressLine="+addressLine+"&addressCity="+addressCity,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#projects > tbody:last').remove();
					$('#projects').append(obj.table);
					//$($('#projects tbody')[1]).remove();
					//$('#projects').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}
function getContacts(customer){
	$.ajax({
		type: "GET",
		url: "getContacts.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var contacts=$('#contact');
			contacts.children().remove();
			jQuery.each(obj, function(i,val){
				contacts.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}
function validateForm(){

	if(document.getElementById('projectName').value.length==0){
		alert("Please type a name for this project");
		document.formValidate.projectName.focus;
		return false;
	}
	if(document.getElementById('customer').selectedIndex==0 ){
		alert("Please select a customer for this contact");
			document.formValidate.customer.focus
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditJob.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['projectId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Truck</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Project Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" disabled <?if(isset($lastVal['projectId']))echo"value='".$lastVal['projectId']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='projectName' name='projectName' <?if(isset($lastVal['projectName']))echo"value='".$lastVal['projectName']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Anticipated Startup Date:</strong></td>
						<td class="last"><input type="text" class="text" id='projectStartup' name='projectStartup' <?if(isset($lastVal['projectStartup']))echo"value='".to_MDY($lastVal['projectStartup'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Type:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from jobLand";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='jobLand' id='jobLand' style='font-family:verdana;font-size:8pt'>";
						if(isset($lastVal['jobLandId'])&&$lastVal['jobLandName']!=null)
						{
							while($state=mysql_fetch_assoc($states))
							{
								if($state['jobLandId']==$lastVal['jobLandId'])
									echo "<option selected='selected'  value='{$state['jobLandId']}'>{$state['jobLandName']}</option>";
								else
									echo "<option value='{$state['jobLandId']}'>{$state['jobLandName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected' value='0'>--Select Land--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								echo "<option value='{$state['jobLandId']}'>{$state['jobLandName']}</option>";
							}
						}
						
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Project Terrain:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from jobTerrain";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='jobTerrain' id='jobTerrain' style='font-family:verdana;font-size:8pt'>";
						if(isset($lastVal['jobTerrainId'])&&$lastVal['jobTerrainName']!=null)
						{
							while($state=mysql_fetch_assoc($states))
							{
								if($state['jobTerrainId']==$lastVal['jobTerrainId'])
									echo "<option selected='selected'  value='{$state['jobTerrainId']}'>{$state['jobTerrainName']}</option>";
								else
									echo "<option value='{$state['jobTerrainId']}'>{$state['jobTerrainName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected' value='0'>--Select Terrain--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								echo "<option value='{$state['jobTerrainId']}'>{$state['jobTerrainName']}</option>";
							}
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1'  <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2'  <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity'  <?if(isset($lastVal['addressCity']))echo"value='".$lastVal['addressCity']."'";?> /></td>
					</tr>
					<tr class="bg">
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
					<tr>
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip'  <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?>  /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox'  <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?>/></td>
					</tr>
					<tr>
						<td class="first"><strong>County:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectCounty' name='projectCounty' <?if(isset($lastVal['projectCounty']))echo"value='".$lastVal['projectCounty']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Township:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectTownship' name='projectTownship' <?if(isset($lastVal['projectTownship']))echo"value='".$lastVal['projectTownship']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>IEPA:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectIepa' name='projectIepa' <?if(isset($lastVal['projectIepa']))echo"value='".$lastVal['projectIepa']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>BOW:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBow' name='projectBow' <?if(isset($lastVal['projectBow']))echo"value='".$lastVal['projectBow']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>BOA:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBoa' name='projectBoa' <?if(isset($lastVal['projectBoa']))echo"value='".$lastVal['projectBoa']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Materials:</strong></td>
						<td class="last">
						<input type="checkbox" name="typemat[]" id="checkbox" value="Uncontaminated Soil" <?if(isset($lastVal['projectMaterial'])&&!(strpos($lastVal['projectMaterial'],'Uncontamined Soil')===false))echo"checked";?>/>
						<span class="line">Uncontaminated Soil. </span><br />
						<input type="checkbox" name="typemat[]" id="checkbox" value="Brick" <?if(isset($lastVal['projectMaterial'])&&!(strpos($lastVal['projectMaterial'],'Brick')===false))echo"checked";?>/>
						<span class="line">Brick.</span><br /> 
						<input type="checkbox" name="typemat[]" id="checkbox" value="Stone" <?if(isset($lastVal['projectMaterial'])&&!(strpos($lastVal['projectMaterial'],'Uncontamined Stone'))===false)echo"checked";?>/>
						<span class="line">Stone.</span><br />
						<input type="checkbox" name="typemat[]" value="Reclaimed Asphalt Paviment" <?if(isset($lastVal['projectMaterial'])&&!(strpos($lastVal['projectMaterial'],'Asphalt'))===false)echo"checked";?> />
						<span class="line">Reclaimed Asphalt Paviment. </span><br />
						<input type="checkbox" name="typemat[]" value="Other" <?if(isset($lastVal['projectMaterial'])&&!(strpos($lastVal['projectMaterial'],'Other')===false))echo"checked";?> />
						<span class="line">Other.</span><br />
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Scoop of Work:</strong></td>
						<td class="last"><textarea type="text" cols='43' class="text"  id='projectSw' name='projectSw' /><?if(isset($lastVal['projectSw']))echo$lastVal['projectSw'];?></textarea></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Project Loads:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectLoads' name='projectLoads' <?if(isset($lastVal['projectLoads']))echo"value='".$lastVal['projectLoads']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Project Trucks:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectTrucks' name='projectTrucks' <?if(isset($lastVal['projectTrucks']))echo"value='".$lastVal['projectTrucks']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Enviromental Assessment:</strong></td>
						<td class="last">
							<input type="radio"  id='projectEnvYes' name='projectEnvironmental' value='1' <?if(isset($lastVal['projectEnviromental'])&&$lastVal['projectEnviromental'])echo"checked";?> />
							<label for='projectEnvYes'>Yes</label>
							<input type="radio"  id='projectEnvNo' name='projectEnvironmental' value='0' <?if(isset($lastVal['projectEnviromental'])&&!$lastVal['projectEnviromental'])echo"checked";?> />
							<label for='projectEnvNo'>No</label>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Project PIN:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectPin' name='projectPin' <?if(isset($lastVal['projectPin']))echo"value='".$lastVal['projectPin']."'";?> /></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryState = "select * from customer";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='customer' id='customer' style='font-family:verdana;font-size:8pt'>";
						if(isset($lastVal['customerId'])&&$lastVal['customerName']!=null)
						{
							echo "<option value='0'>--Select customer--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								if($state['customerId']==$lastVal['customerId'])
									echo "<option selected='selected'  value='{$state['customerId']}'>{$state['customerName']}</option>";
								else
									echo "<option value='{$state['customerId']}'>{$state['customerName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected' value='0'>--Select customer--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								echo "<option value='{$state['customerId']}'>{$state['customerName']}</option>";
							}
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Contact:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from contact where customerId=".$lastVal['customerId'];
						$states = mysql_query($queryState,$conexion);
						echo "<select name='contact' id='contact' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select contact--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['contactId'])&& $lastVal['contactName']!=null && $lastVal['contactId']==$state['contactId'])
							echo "<option value='{$state['contactId']}'>{$state['contactName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class='bg'>
						<td class="first"><strong>Project Company:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectCompany' name='projectCompany' <?if(isset($lastVal['projectCompany']))echo"value='".$lastVal['projectCompany']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 1:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass1PW' name='projectClass1PW' <?echo "value='". decimalPad($lastVal['projectClass1PW'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 2:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass2PW' name='projectClass2PW' <?echo "value='". decimalPad($lastVal['projectClass2PW'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 3:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass3PW' name='projectClass3PW' <?echo "value='". decimalPad($lastVal['projectClass3PW'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 4:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass4PW' name='projectClass4PW' <?echo "value='". decimalPad($lastVal['projectClass4PW'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Broker:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBrokerPW' name='projectBrokerPW' <?echo "value='". decimalPad($lastVal['projectClassBrokerPW'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Project Under:</strong></td>
						<td class="last">
							<input type="radio"  id='projectUnderNoAct' name='projectUnder' value='<?echo NO_ACT; ?>' <?if($lastVal['projectUnder'] == NO_ACT)echo " checked ";?> /><label for='projectUnderNoAct'>No Act/Other</label><br/>
							<input type="radio"  id='projectUnderIPW' name='projectUnder' value='<?echo ILLINOIS_PW_ACT; ?>' <?if($lastVal['projectUnder'] == ILLINOIS_PW_ACT)echo " checked ";?>/><label for='projectUnderIPW'>Illinois Prevailing Wage</label><br/>
							<input type="radio"  id='projectUnderDBA' name='projectUnder' value='<?echo DAVIS_BACON_ACT; ?>' <?if($lastVal['projectUnder'] == DAVIS_BACON_ACT)echo " checked ";?>/><label for='projectUnderDBA'>Davis Bacon Act</label>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Approval Number:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectApprovalNumber' name='projectApprovalNumber' <?if(isset($lastVal['projectApprovalNumber']))echo"value='".$lastVal['projectApprovalNumber']."'";?> /></td>
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
			
		
		<?
		}
		else
		{
		?>
			<div class="table" id="search-bar">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
					<tr>
						<th class="full" colspan="5">Search Project</th>
					</tr>
					<tr class="bg">
						<td>Id</td>
						<td>Name</td>
						<td>Address</td>
						<td>City</td>
						<td></td>
					</tr>
					<tr>
						<td><input type="text"  size='8px' name="searchId" id="searchId" /></td>
						<td><input type="text"  size='8px' name="searchName" id="searchName" /></td>
						<td><input type="text"  size='8px' name="searchAddress" id="searchAddress" /></td>
						<td><input type="text"  size='8px' name="searchCity" id="searchCity" /></td>
						<td><select name='searchActive' id="searchActive" >
							<option value="-1">All</option>
							<option value="0">Active</option>
							<option value="1">Inactive</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id="projects">
					<tr><th class="full" colspan="4" >Projects</th></tr>
					<tr>
						<th>Id</th><th>Name</th>
						<th>Id</th><th>Name</th>
					</tr>
					<tbody>
					<?
					$queryProjects = "select * from project order by projectName";
					$projects = mysql_query($queryProjects,$conexion);
					$numProjects = mysql_num_rows($projects);
					$first =true;
					$class = " class='bg' ";
					while($project = mysql_fetch_assoc($projects)){
						if($first){
							echo "<tr>";
							echo "<td $class>".$project['projectId']."</td>";
							echo "<td id='project".$project['projectId']."' $class>".$project['projectName']."</td>";
							if($class=="")$class=" class='bg' ";
							else $class="";
						}else{
							echo "<td $class>".$project['projectId']."</td>";
							echo "<td id='project".$project['projectId']."' $class>".$project['projectName']."</td>";
							echo "</tr>";
						}
						$first = !$first;
					}
					if(!$first){echo "<td colspan='2' $class></td></tr>";}
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
