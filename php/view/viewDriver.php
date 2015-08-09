<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
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
###############Show Customer###############
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

$(document).ready(function()
{
	$('#drivers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewDriver.php?i="+id);
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditCustomer.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Broker Information</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Broker:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerName']))echo$lastVal['brokerName'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['driverFirstName']))echo$lastVal['driverFirstName']." ".$lastVal['driverLastName'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><?if(isset($lastVal['driverTel']))echo showPhoneNumber($lastVal['driverTel']);?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Mobile:</strong></td>
						<td class="last"><?if(isset($lastVal['driverMobile']))echo showPhoneNumber($lastVal['driverMobile']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Carrier:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['carrierName'])&&$lastVal['carrierName']!=null)
							echo showPhoneNumber($lastVal['driverTel']);
						else
							echo "<span style='color:red;'>A carrier is not set</span>"
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail:</strong></td>
						<td class="last"><?if(isset($lastVal['driverMail']))echo$lastVal['driverMail'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressLine1']))echo$lastVal['addressLine1'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressLine2']))echo$lastVal['addressLine2'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressCity']))echo$lastVal['addressCity'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['addressState']))echo$lastVal['addressState'];
						?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressZip'])&&$lastVal['addressZip']!=0)echo$lastVal['addressZip'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressPOBox']))echo$lastVal['addressPOBox'];?></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Pay Term:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?if(isset($lastVal['termName']))echo$lastVal['termName'];?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Percentage:</strong></td>
						<td class="last"><?if(isset($lastVal['driverPercentage']))echo$lastVal['driverPercentage'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Start Date:</strong></td>
						<td class="last"><?if(isset($lastVal['driverStartDate']))echo to_MDY($lastVal['driverStartDate']);?></td>
					</tr>
					<tr>
						<td class="first"><strong>Union:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['driverUnion']))
						{
							if($lastVal['driverUnion'])
								echo "Yes";
							else
								echo "No";
						}
						?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Status:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['driverStatus']))
						{
							if($lastVal['driverStatus'])
								echo "<span style='color:green;'>Active</span>";
							else
								echo "<span style='color:red;'>Inactive</span>";
						}
						?></td>
					</tr>
					<tr>
						<td class="first"><strong>Gender:</strong></td>
						<td class="last"><?if(isset($lastVal['driverGender']))echo$lastVal['driverGender'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Ethnicity:</strong></td>
						<td class="last"><?if(isset($lastVal['ethnicName']))echo $lastVal['ethnicName']==null?"":$lastVal['ethnicName'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Class:</strong></td>
						<td class="last"><?if(isset($lastVal['driverClass']))echo$lastVal['driverClass'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Work:</strong></td>
						<td class="last"><?if(isset($lastVal['workName']))echo $lastVal['workName']==null?"":$lastVal['workName'];?></td>
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
