<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Broker";
$description = "Broker Information.";

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
 		broker
	JOIN address USING (addressId)
	JOIN term USING (termId)
	LEFT JOIN carrier USING (carrierId)
	LEFT JOIN ethnic USING (ethnicId)
	WHERE
		brokerId=".$_GET['i'];
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
var patternLetters = new RegExp(/broker/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('#brokers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewBroker.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});
	
	$('#brokerPid').change(function(){getBrokers();});
	$('#brokerPid').keyup(function(){getBrokers();});
	$('#brokerName').change(function(){getBrokers();});
	$('#brokerName').keyup(function(){getBrokers();});
	$('#brokerStatus').change(function(){getBrokers();});

});

function getBrokers(){
	
	var brokerPid=$('#brokerPid').val();
	var brokerName=$('#brokerName').val();
	var brokerStatus=$('#brokerStatus').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewBrokers.php",
		data: "brokerPid="+brokerPid+"&brokerName="+brokerName+"&brokerStatus="+brokerStatus,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#brokers > tbody:last').remove();
					$('#brokers').append(obj.table);
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
				<a onclick="return confirm('Are you sure you want to delete <?echo$lastVal['brokerName'];?> and all its trucks?');"  href="deleteBroker.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editBroker.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitEditCustomer.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Broker Information</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Pid (short name):</strong></td>
						<td class="last"><?if(isset($lastVal['brokerPid']))echo$lastVal['brokerPid'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['brokerName']))echo$lastVal['brokerName'];?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Contact Name:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerContactName']))echo$lastVal['brokerContactName'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerTel']))echo showPhoneNumber($lastVal['brokerTel']);?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerFax']))echo showPhoneNumber($lastVal['brokerFax']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Radio:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerRadio']))echo$lastVal['brokerRadio'];?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Mobile:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerMobile']))echo showPhoneNumber($lastVal['brokerMobile']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Carrier:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['carrierName'])&&$lastVal['carrierName']!=null)
							echo showPhoneNumber($lastVal['brokerTel']);
						else
							echo "<span style='color:red;'>A carrier is not set</span>"
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerMail']))echo$lastVal['brokerMail'];?></td>
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
						<td class="first"><strong>Tax:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerTax']))echo$lastVal['brokerTax'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>ICC:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerIccCert']))echo$lastVal['brokerIccCert'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Insurance WC:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['brokerInsuranceWc']))echo$lastVal['brokerInsuranceWc'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['brokerWcExpire']))
						{
							$expired=strtotime(date("Y-m-d"));
							$warning=strtotime('+30 day',strtotime(date('Y-m-d')));
							$insWc=strtotime($lastVal['brokerWcExpire']);
							if($insWc<$expired) //Already expired mark red
							{
								$color='red';
							}
							else
							{
								if($insWc<$warning) //About to expire mark yellow
								{
									$color='orange';
								}
								else //Is ok mark green
								{
									$color='green';
								}
							}
							echo "<span style='color:".$color.";'>".to_MDY($lastVal['brokerWcExpire'])."</span>";
						}
						
						?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong><!--Insurance-->Automovile Liability:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['brokerInsuranceLiability']))echo$lastVal['brokerInsuranceLiability'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['brokerLbExpire']))
						{
							$expired=strtotime(date("Y-m-d"));
							$warning=strtotime('+30 day',strtotime(date('Y-m-d')));
							$insWc=strtotime($lastVal['brokerLbExpire']);
							if($insWc<$expired) //Already expired mark red
							{
								$color='red';
							}
							else
							{
								if($insWc<$warning) //About to expire mark yellow
								{
									$color='orange';
								}
								else //Is ok mark green
								{
									$color='green';
								}
							}
							echo "<span style='color:".$color.";'>".to_MDY($lastVal['brokerLbExpire'])."</span>";
						}
						
						?>
						</td>
					</tr>
					<!-- aqui cambie -->
					<tr>
						<td class="first"><strong>General Liability:</strong>
						<td class="last"><?if(isset($lastVal['brokerGeneralLiability']))echo$lastVal['brokerGeneralLiability'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong>
						<td class="last">
						<?
						if(isset($lastVal['brokerGlExp']))
						{
							$expired=strtotime(date("Y-m-d"));
							$warning=strtotime('+30 day',strtotime(date('Y-m-d')));
							$insWc=strtotime($lastVal['brokerGlExp']);
							if($insWc<$expired) //Already expired mark red
							{
								$color='red';
							}
							else
							{
								if($insWc<$warning) //About to expire mark yellow
								{
									$color='orange';
								}
								else //Is ok mark green
								{
									$color='green';
								}
							}
							echo "<span style='color:".$color.";'>".to_MDY($lastVal['brokerGlExp'])."</span>";
						}
						
						?>
						</td>
					</tr>
					<!-- hasta aqui cambie -->
					<tr>
						<td class="first"><strong>Percentage:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerPercentage']))echo$lastVal['brokerPercentage'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Start Date:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerStartDate']))echo to_MDY($lastVal['brokerStartDate']);?></td>
					</tr>
					<tr>
						<td class="first"><strong>Gender:</strong></td>
						<td class="last"><?if(isset($lastVal['brokerGender']))echo$lastVal['brokerGender'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Ethnicity:</strong></td>
						<td class="last"><?if(isset($lastVal['ethnicName']))echo $lastVal['ethnicName'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Status:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['brokerStatus']))
						{
							if($lastVal['brokerStatus'])
								echo "<span style='color:green;'>Active</span>";
							else
								echo "<span style='color:red;'>Inactive</span>";
						}
						?></td>
					</tr>
				</table>
				<table>
				<tr>
					<td>
						<a href="/trucking/php/new/newTruck.php?i=<?echo$lastVal['brokerId'];?>" >
							<strong>Add a truck</strong>
							<img src='/trucking/img/95.png' width='20' height='20' />
						</a>
					</td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
			$queryContacts = "select * from truck where brokerId=".$lastVal['brokerId'];
			$terms = mysql_query($queryContacts,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Trucks</th>
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
						<td class='first style2'>".$term['truckNumber']."</td>
						<td><a href='/trucking/php/view/viewTruck.php?i=".$term['truckId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editTruck.php?i=".$term['truckId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ".$term['truckNumber']."?');\" href='deleteTruck.php?i=".$term['truckId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
			}
			?>
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
					<th class="full"  colspan='5'>Search Brokers</th>
				</tr>
				<tr>
					<td>Unique Identifier</td>
					<td>Name</td>
					<td>Status</td>
				</tr>
				<tr>
					<td><input type='text' size='8px' id='brokerPid' name='brokerPid' /></td>
					<td><input type='text' size='8px' id='brokerName' name='brokerName' /></td>
					<td>
							<select name='brokerStatus' id='brokerStatus' style='font-family:verdana;font-size:8pt' >
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
				<table class="listing form" cellpadding="0" cellspacing="0" id='brokers'>
				<tr>
					<th class="full"  colspan='9'>BROKERS</th>
				</tr>
				<tr>
					<th>UID</th>
					<th>Name</th>
					<th>Tel</th>
					<th>Mobile</th>
				</tr>
				<tbody>
				<?
				$brokers = mysql_query("select * from broker order by brokerPid asc",$conexion);
				$numBrokers=mysql_num_rows($brokers);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numBrokers>0)
				{
						while($broker=mysql_fetch_assoc($brokers))
						{
								echo "<tr>";
								echo "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								echo "<td ".$tdClass." id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								echo "<td ".$tdClass.">".showPhoneNumber($broker['brokerTel'])."</td>";
								echo "<td ".$tdClass.">".showPhoneNumber($broker['brokerMobile'])."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";echo "</tr>";
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
