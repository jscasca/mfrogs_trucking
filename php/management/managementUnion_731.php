<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Management";
#################
$subtitle = "Union_731";
$description = "Add new union information. This information will be valid while there is no newer union information. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
$(document).ready(function()
{
	
});

function validateForm(){
	
	
	if(document.getElementById('unionStart').value.length==0){
		alert("Please type the start date");
			document.formValidate.unionStart.focus;
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
			<form id="formValidate" name="formValidate" method="POST" action="submitUnion_731.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Union Values</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Start date:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionStart' name='unionStart' />
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>End:</td>
						<td class="last">
							<input type="text" id='unionEnd' name='unionEnd' />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Class 1 Hourly Rate:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionClass1HR' name='unionClass1HR' value="0.00" />
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Class 2 Hourly Rate:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionClass2HR' name='unionClass2HR' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Class 3 Hourly Rate:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionClass3HR' name='unionClass3HR' value="0.00" />
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Class 4 Hourly Rate:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionClass4HR' name='unionClass4HR' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Welfare:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionWelfare' name='unionWelfare' value="0.00" />
						</td>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Pension:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionPension' name='unionPension' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>CCSC:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionCCSC' name='unionCCSC' value="0.00" />
						</td>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>CISC:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionCISC' name='unionCISC' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>MIAF:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionMIAF' name='unionMIAF' value="0.00" />
						</td>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>ITETF:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionITETF' name='unionITETF' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>LTF:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionLTF' name='unionLTF' value="0.00" />
						</td>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>SF:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionSF' name='unionSF' value="0.00" />
						</td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Monthly dues:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" id='unionMonthlyDues' name='unionMonthlyDues' value="0.00" />
						</td>
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
			<?
			
			$queryContacts = "select * from union731 order by unionId desc limit 1";
			$terms = mysql_query($queryContacts,$conexion);
			$numTerms = mysql_num_rows($terms);
			echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='full' colspan='2' width='177'>Last Union Information</th>
					</tr>";
			if($numTerms>0)
			{
				while($term = mysql_fetch_assoc($terms))
				{
					echo "<tr class='bg'><td class='first'>Start</td><td class='last'>".to_MDY($term['unionStartDate'])."</td></tr>";
					echo "<tr ><td class='first'>End</td><td class='last'>".to_MDY($term['unionEndDate'])."</td></tr>";
					echo "<tr class='bg'><td class='first'>Welfare</td><td class='last'>".decimalPad($term['unionWelfare'])."</td></tr>";
					echo "<tr ><td class='first'>Pension</td><td class='last'>".decimalPad($term['unionPension'])."</td></tr>";
					echo "<tr class='bg'><td class='first'>CCSC</td><td class='last'>".decimalPad($term['unionCCSC'])."</td></tr>";
					echo "<tr ><td class='first'>CISC</td><td class='last'>".decimalPad($term['unionCISC'])."</td></tr>";
					echo "<tr class='bg'><td class='first'>MIAF</td><td class='last'>".decimalPad($term['unionMIAF'])."</td></tr>";
					echo "<tr ><td class='first'>ITETF</td><td class='last'>".decimalPad($term['unionITETF'])."</td></tr>";
					echo "<tr class='bg'><td class='first'>LTF</td><td class='last'>".decimalPad($term['unionLTF'])."</td></tr>";
					echo "<tr ><td class='first'>SF</td><td class='last'>".decimalPad($term['unionSF'])."</td></tr>";
					echo "<tr class='bg'><td class='first'>MonthlyDues</td><td class='last'>".decimalPad($term['unionMonthlyDues'])."</td></tr>";
				}
			}else{
				echo "<tr><td colspan='2'>NO OLDER UNION INFORMATION</td></tr>";
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
