<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Driver";
$description = "Add a new driver. Dates must be specified in the mm/dd/yyyy format.  Values marked with <span style='color:red;'>*</span> are mandatory.";

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
	<style media="all" type="text/css">@import "../../css/all.css";</style>
</head>
<script type="text/javascript">	
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
		
		
			<div class="top-bar">
				<a href="#" class="broker"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitDriver.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Driver</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Broker:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryTerm = "select * from broker where brokerStatus=1 order by brokerName";
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
									if($_GET['i']==$term['brokerId'])
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
						<td class="first" width="172"><strong>First Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverFirst' name='driverFirst'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Last Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverLast' name='driverLast' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>SSN:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='driverSSN' name='driverSSN'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><input type="text" class="text" id='driverTel' name='driverTel' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Mobile:</strong></td>
						<td class="last"><input type="text" class="text" id='driverMobile' name='driverMobile'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Carrier:</strong></td>
						<td class="last">
						<?
						$queryCarrier = "select * from carrier";
						$carriers = mysql_query($queryCarrier,$conexion);
						echo "<select name='carrierId' id='carrierId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected'>--Select Carrier--</option>";
						while($carrier=mysql_fetch_assoc($carriers))
						{
							echo "<option value='{$carrier['carrierId']}'>{$carrier['carrierName']}</option>";
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail:</strong></td>
						<td class="last"><input type="text" class="text" id='driverMail' name='driverMail'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity' /></td>
					</tr>
					<tr>
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
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox'/></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Pay Term:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryTerm = "select * from term";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='termId' id='termId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms > 0)
						{
							
							echo "<option selected='selected'>--Select Term--</option>";
							while($term=mysql_fetch_assoc($terms))
							{
								echo "<option value='{$term['termId']}'>{$term['termName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected'>There are no terms in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Union:</strong></td>
						<td class="last">
								<select id='driverUnion' name='driverUnion' >
									<option value="0" >No</option>
									<option value="1" >Yes</option>
									</select>
						</td>
					</tr>
					<tr  class="bg">
						<td class="first"><strong>Percentage:</strong></td>
						<td class="last"><input type="text" class="text" id='driverPercentage' name='driverPercentage' /></td>
					</tr>
					<tr>
						<td class="first"><strong>Start Date:</strong></td>
						<td class="last"><input type="text" class="text" id='driverStartDate' name='driverStartDate' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Gender:</strong></td>
						<td class="last">
							<select name="driverGender" id="driverGender" style='font-family:verdana;font-size:8pt'>
								<option value="Male" >Male</option>
								<option value="Female" >Female</option>
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
								<option value="1" >1</option>
								<option value="2" >2</option>
								<option value="3" >3</option>
								<option value="4" >4</option>
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
								echo "<option value='{$term['workId']}'>{$term['workName']}</option>";
						}
						echo "</select>";
						?>
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
			<?
			
			$queryCustomers = "select * from driver order by driverId desc limit 5";
			$terms = mysql_query($queryCustomers,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Name</th>
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
						<td class='first style2'>".$term['driverFirstName']." ".$term['driverLastName']."</td>
						<td><a href='/trucking/php/view/viewDriver.php?i=".$term['driverId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editDriver.php?i=".$term['driverId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ".$term['driverFirstName']." ".$term['driverLastName']." and all of its trucks?');\" href='/trucking/php/view/deleteDriver.php?i=".$term['driverId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
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
