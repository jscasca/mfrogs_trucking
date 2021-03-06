<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Project";
$description = "Add a new job/project. Values marked with <span style='color:red;'>*</span> are mandatory.";

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


###############Next Autoincrement#########
$next_inc=0;
$showstatus="show table status like 'project'";
$status=mysql_query($showstatus,$conexion);
$row=mysql_fetch_assoc($status);
$next_inc=$row['Auto_increment'];
##########################################

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
$(document).ready(function()
{
	
	$('#descriptionDisplay').click(function(){
		$('#hiddenDescription').toggle();
	});
	
	$('#customer').change(function() {
		var customer=this.value;
		getContacts(customer);
	});
});
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
			document.formValidate.customer.focus;
			return false;
	}
	
	if(!$("input[name='projectUnder']:checked").val()) {
		alert("Please select if the project is covered under any Act");
		$("projectUnderNoAct").focus();
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
						<th class="full" id='descriptionDisplay' >Description</th>
					</tr>
					<tr style="display:none;" id='hiddenDescription'>
						<td class="last" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitJob.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Truck</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Project Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" disabled value="<?echo$next_inc;?>" /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='projectName' name='projectName'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Anticipated Startup Date:</strong></td>
						<td class="last"><input type="text" class="text" id='projectStartup' name='projectStartup' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Type:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from jobLand";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='jobLand' id='jobLand' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0'>--Select Land--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							echo "<option value='{$state['jobLandId']}'>{$state['jobLandName']}</option>";
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
						echo "<option selected='selected' value='0'>--Select Terrain--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							echo "<option value='{$state['jobTerrainId']}'>{$state['jobTerrainName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1' /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2'/></td>
					</tr>
					<tr>
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity' /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from state";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='addressState' id='addressState' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected'>--Select State--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							echo "<option value='{$state['stateId']}'>{$state['stateName']}</option>";
						}
						echo "</select>";
						?>
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip' /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox'/></td>
					</tr>
					<tr>
						<td class="first"><strong>County:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectCounty' name='projectCounty' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Township:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectTownship' name='projectTownship' /></td>
					</tr>
					<tr>
						<td class="first"><strong>IEPA:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectIepa' name='projectIepa' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>BOW:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBow' name='projectBow' /></td>
					</tr>
					<tr>
						<td class="first"><strong>BOA:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBoa' name='projectBoa' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Materials:</strong></td>
						<td class="last">
						<input type="checkbox" name="typemat[]" id="checkbox" value="Uncontaminated Soil"/>
						<span class="line">Uncontaminated Soil. </span><br />
						<input type="checkbox" name="typemat[]" id="checkbox" value="Brick"/>
						<span class="line">Brick.</span><br /> 
						<input type="checkbox" name="typemat[]" id="checkbox" value="Stone"/>
						<span class="line">Stone.</span><br />
						<input type="checkbox" name="typemat[]" value="Reclaimed Asphalt Paviment" />
						<span class="line">Reclaimed Asphalt Paviment. </span><br />
						<input type="checkbox" name="typemat[]" value="Other" />
						<span class="line">Other.</span><br />
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Scoop of Work:</strong></td>
						<td class="last"><textarea type="text" cols='43' class="text"  id='projectSw' name='projectSw' /></textarea></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Project Loads:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectLoads' name='projectLoads' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Project Trucks:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectTrucks' name='projectTrucks' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Enviromental Assessment:</strong></td>
						<td class="last">
							<input type="radio"  id='projectEnvYes' name='projectEnvironmental' value='1' />
							<label for='projectEnvYes'>Yes</label>
							<input type="radio"  id='projectEnvNo' name='projectEnvironmental' value='0' />
							<label for='projectEnvNo'>No</label>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Project PIN:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectPin' name='projectPin' /></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryState = "select * from customer order by customerName";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='customer' id='customer' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0'>--Select customer--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							echo "<option value='{$state['customerId']}'>{$state['customerName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Contact:</strong></td>
						<td class="last">
						<?
						//$queryState = "select * from contact";
						//$states = mysql_query($queryState,$conexion);
						echo "<select name='contact' id='contact' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select contact--</option>";
						/*while($state=mysql_fetch_assoc($states))
						{
							echo "<option value='{$state['contactId']}'>{$state['contactName']}</option>";
						}*/
						echo "</select>";
						?>
						</td>
					</tr>
					<tr class='bg'>
						<td class="first"><strong>Project Company:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectCompany' name='projectCompany' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 1:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass1PW' name='projectClass1PW' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 2:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass2PW' name='projectClass2PW' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 3:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass3PW' name='projectClass3PW' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 4:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectClass4PW' name='projectClass4PW' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Broker:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectBrokerPW' name='projectBrokerPW' /></td>
					</tr>
					<tr class="bg">
						<td class='first'><strong>Under:</strong><span style="color:red;">*</span></td>
						<td class='last'>
							<input type="radio"  id='projectUnderNoAct' name='projectUnder' value='<?echo NO_ACT; ?>' /><label for='projectUnderNoAct'>No Act/Other</label><br/>
							<input type="radio"  id='projectUnderIPW' name='projectUnder' value='<?echo ILLINOIS_PW_ACT; ?>' /><label for='projectUnderIPW'>Illinois Prevailing Wage</label><br/>
							<input type="radio"  id='projectUnderDBA' name='projectUnder' value='<?echo DAVIS_BACON_ACT; ?>' /><label for='projectUnderDBA'>Davis Bacon Act</label>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Approval Number:</strong></td>
						<td class="last"><input type="text" class="text"  id='projectApprovalNumber' name='projectApprovalNumber' /></td>
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
			
			$queryProjects = "select * from project order by projectId desc limit 5";
			$terms = mysql_query($queryProjects,$conexion);
			$numTerms = mysql_num_rows($terms);
			/*
			$querydelete = "SELECT * FROM project 
			WHERE projectId NOT IN (SELECT projectId FROM project
			JOIN item using (projectId)
			JOIN ticket using (itemId))";
			$termsdelete= mysql_query($queryProjects,$conexion);
			*/
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='18'>Id</th>
						<th>Name</th>
						<th>Add Item</th>
						<th>View</th>
						<th>Edit</th>
						<th class='last'>Delete</th>
					</tr>";
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
					
					echo "
						<td class='first style2'>".$term['projectId']."</td>
						<td class='first style2'>".$term['projectName']."</td>
						<td><a href='/trucking/php/new/newItem.php?i=".$term['projectId']."'><img src='/trucking/img/95.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/view/viewJob.php?i=".$term['projectId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editJob.php?i=".$term['projectId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						";
					$ticketCount = mysql_fetch_assoc(mysql_query("select count(*) as tickets from ticket join item using (itemId) where projectId = ".$term['projectId'],$conexion));
					if($ticketCount['tickets']<=0)
					{
					echo "<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ".$term['projectName']."?');\" href='deleteJob.php?i=".$term['projectId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
					}
					else{
					echo "<td class='last'></td>
					</tr>";
					}
					
				}
				
				
				echo "</table>";
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
