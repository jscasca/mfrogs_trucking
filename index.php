<?php

include("php/conexion.php");
include("php/commons.php");

session_start();

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

if(isset($_SESSION["user"]))
{
	$var="Homepage";
	
	$querySections = 
		"SELECT
			*
		FROM
			section";
	
	$sections = mysql_query($querySections,$conexion);
	
}else
{
	$var="Login";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Homepage</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<style media="all" type="text/css">@import "css/all.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
var patternLetters = new RegExp(/broker/);
patternLetters.compile(patternLetters);

$(document).ready(function() {
	$('table tr').dblclick(function() {
		var brokerId = $(this).attr('brokerId');
		if(brokerId != undefined) {
			window.location.href = "./php/edit/editBroker.php?i="+brokerId;
		}
		
		/*var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./php/edit/editBroker.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}*/
	});
});

</script>
<body>
<div id="main">
	<div id="header">
		<a href="/trucking/index.php" class="logo"><img src="img/logo.gif" width="118" height="62" alt="" /></a>
		
		<?
		if(isset($_SESSION['user']))
		{
		?>
		<a href="/trucking/php/logout.php" class="logout">Logout</a>
		<?
		}
		?>
		<ul id="top-navigation">
		<?
		echo "<li class='active'><span><span>".$var."</span></span></li>";
		if(isset($_SESSION["user"]))
		{
			//$path='';
			//$results = scandir("php");
			$results = "php/*";
			foreach(glob($results) as $result)
			{
				if(file_exists("./".$result."/menu.php"))
				{
					$name=ucfirst(substr($result,strpos($result,'/')+1));
					echo "<li><span><span><a href='$result/menu.php'>".$name." Menu</a></span></span></li>" ;
				}
			}
			
			echo "</ul>";	
		}else
		{
		}
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
			
			
			
			if(isset($_SESSION["user"]))
			{
				
			
				echo "<h3></h3>";	
			
			}else
			{
				echo "<form id='fvalida' name='fvalida' method='post' action='php/commonPlace.php' >";
				echo "<h3>Log In</h3>";
				echo "<ul class='nav'>";
				if(isset($_GET['t']))
					echo "<li>Usuario Incorrecto!</li>";
					echo "<li>User</li>";
					echo "<li><input type='text' size='15' name='user' id='user' /></li>";
					echo "<li>Password</li>";
					echo "<li><input type='password' size='15' name='pass' id='pass' /></li>";
					echo "<li class='last'><input type='submit' name='Continuar' id='Continue' value='Continue' /></li>";
				echo "</ul>";
				echo "</form>";
			}
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
			<!--<div class="top-bar">
				<a href="#" class="button">ADD NEW </a>
				<h1>Contents</h1>
				<div class="breadcrumbs"><a href="#">Homepage</a> / <a href="#">Contents</a></div>
			</div><br />-->
		  <!--<div class="select-bar">
		    <label>
		    <input type="text" name="textfield" />
		    </label>
		    <label>
			<input type="submit" name="Submit" value="Search" />
			</label>
		  </div>-->
			<!--<div class="table">
				<img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing" cellpadding="0" cellspacing="0">
					<tr>
						<th class="first" width="177">Header Here</th>
						<th>Header</th>
						<th>Head</th>
						<th>Header</th>
						<th>Header</th>
						<th>Head</th>
						<th>Header</th>
						<th class="last">Head</th>
					</tr>
					<tr>
						<td class="first style1">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style2">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style3">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style1">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style2">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style3">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style4">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
				</table>
				<div class="select">
					<strong>Other Pages: </strong>
					<select>
						<option>1</option>
					</select>
			  </div>
			</div>-->
		  <!--<div class="table">
				<img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Header Here</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr>
						<td class="first""><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
				</table>
	        <p>&nbsp;</p>
		  </div>-->
			<?
			if(isset($_SESSION["user"]))
			{
				
				$needToEdit = "SELECT * FROM broker LEFT JOIN ethnic USING (ethnicId) WHERE ethnicName IS NULL OR brokerGender = '' order by brokerName";
				$brokersToEdit = mysql_query($needToEdit, $conexion);
				if(mysql_num_rows($brokersToEdit) > 0){
					?>
					<div class='table' id='brokersToEditDiv'>
						<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
						<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
						<table class="listing form" cellpadding="0" cellspacing="0" id='brokersToEditTable'>
							<tr>
								<th class="full"  colspan='9'>EDIT THIS BROKERS! Update each broker gender and ethnicity</th>
							</tr>
							<tr>
								<th>UID</th>
								<th>Name</th>
								<th>Gender</th>
								<th>Ethnicity</th>
							</tr>
							<?
							$odd = true;
							while($broker = mysql_fetch_assoc($brokersToEdit)) {
								echo "<tr class='".($odd ? "odd" : "even")."Warning' brokerId='".$broker['brokerId']."'>";
								echo "<td>".$broker['brokerPid']."</td>";
								echo "<td>".$broker['brokerName']."</td>";
								echo "<td>".$broker['brokerGender']."</td>";
								echo "<td>".$broker['EthnicName']."</td>";
								echo "</tr>";
								$odd = !$odd;
							}
							?>
						</table>
					</div>
					<?
					
				}
				
			$brokers = mysql_query("select * from broker where brokerWcExpire between '".date("Y-m-d")."' and '".date("Y-m-d",strtotime('+30 day',strtotime(date('Y-m-d'))))."' and brokerStatus='1'",$conexion);
				$numbrokers=mysql_num_rows($brokers);
				$actual=true;
				if($numbrokers>0)
				{
					?>
			<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='almostExpired'>
				<tr>
					<th class="full"  colspan='9'>ABOUT TO EXPIRE</th>
				</tr>
				<tr>
					
					<th>UID</th>
					<th>Name</th>
					<th>WC</th>
					<th>Expire date</th>
				</tr>
				<tbody>
				<?
				
						while($broker=mysql_fetch_assoc($brokers))
						{
							if($actual){
								echo "<tr class='oddWarning' brokerId='".$broker['brokerId']."'>";
								echo "<td>".$broker['brokerPid']."</td>";
								echo "<td id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								echo "<td>".$broker['brokerInsuranceWc']."</td>";
								echo "<td>".to_MDY($broker['brokerWcExpire'])."</td>";
								echo "</tr>";
							}else{
								echo "<tr class='evenWarning' brokerId='".$broker['brokerId']."'>";
								echo "<td>".$broker['brokerPid']."</td>";
								echo "<td id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								echo "<td>".$broker['brokerInsuranceWc']."</td>";
								echo "<td>".to_MDY($broker['brokerWcExpire'])."</td>";
								echo "</tr>";
							}
							$actual=!$actual;
						}
				?>
				</tbody>
			</table>
			</div>
			<?
			}
			?>
			
			<?
			$brokers = mysql_query("select * from broker where brokerWcExpire <'".date("Y-m-d")."' and brokerStatus='1'",$conexion);
				$numbrokers=mysql_num_rows($brokers);
				$actual=true;
				if($numbrokers>0)
				{
					?>
			<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='expired'>
				<tr>
					<th class="full"  colspan='9'>EXPIRED</th>
				</tr>
				<tr>
					
					<th>UID</th>
					<th>Name</th>
					<th>WC</th>
					<th>Expire date</th>
				</tr>
				<tbody>
				<?
				
						while($broker=mysql_fetch_assoc($brokers))
						{
							if($actual){
								echo "<tr class='oddUnpaid'  brokerId='".$broker['brokerId']."'>";
								echo "<td>".$broker['brokerPid']."</td>";
								echo "<td id='broker".$broker['brokerId']."'><strong>".$broker['brokerName']."</strong></td>";
								echo "<td>".$broker['brokerInsuranceWc']."</td>";
								echo "<td>".to_MDY($broker['brokerWcExpire'])."</td>";
								echo "</tr>";
							}else{
								echo "<tr class='evenUnpaid'  brokerId='".$broker['brokerId']."'>";
								echo "<td>".$broker['brokerPid']."</td>";
								echo "<td id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								echo "<td>".$broker['brokerInsuranceWc']."</td>";
								echo "<td>".to_MDY($broker['brokerWcExpire'])."</td>";
								echo "</tr>";
							}
							$actual=!$actual;
						}
				?>
				</tbody>
			</table>
			</div>
			<?
			}
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
