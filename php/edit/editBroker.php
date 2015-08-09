<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Broker";
$description = "Edit a Broker. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
 		broker
	JOIN address USING (addressId)
	JOIN term USING (termId)
	LEFT JOIN carrier USING (carrierId)
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
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this ticket?');
	});
	$('#brokers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editBroker.php?i="+id);
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
		url: "getEditBrokers.php",
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

function validateForm(){

	if(document.getElementById('brokerName').value.length==0){
		alert("Please type a name for the customer");
		document.formValidate.brokerName.focus
		return false;
	}
	if(document.getElementById('brokerInsWc').value.length==0){
		alert("Please fill the insurance information");
		document.formValidate.brokerInsWc.focus
		return false;
	}
	if(document.getElementById('brokerWcExpire').value.length==0){
		alert("Please fill the expiration date correctly");
		document.formValidate.brokerWcExpire.focus
		return false;
	}
	if(document.getElementById('brokerInsLiability').value.length==0){
		alert("Please type a name for the customer");
		document.formValidate.brokerInsLiability.focus
		return false;
	}
	if(document.getElementById('brokerLbExpire').value.length==0){
		alert("Please fill the expiration date correctly");
		document.formValidate.brokerLbExpire.focus
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditBroker.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['brokerId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Edit Broker</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Pid (short name):</strong></td>
						<td class="last"><input type="text" class="text" id='brokerPid' name='brokerPid' <?if(isset($lastVal['brokerPid']))echo"value='".$lastVal['brokerPid']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='brokerName' name='brokerName' <?if(isset($lastVal['brokerName']))echo"value='".$lastVal['brokerName']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Contact Name:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerContactName' name='brokerContactName'  <?if(isset($lastVal['brokerContactName']))echo"value='".$lastVal['brokerContactName']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerTel' name='brokerTel' <?if(isset($lastVal['brokerTel']))echo"value='".showPhoneNumber($lastVal['brokerTel'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerFax' name='brokerFax' <?if(isset($lastVal['brokerFax']))echo"value='".showPhoneNumber($lastVal['brokerFax'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Radio:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerRadio' name='brokerRadio' <?if(isset($lastVal['brokerRadio']))echo"value='".$lastVal['brokerRadio']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Mobile:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerMobile' name='brokerMobile' <?if(isset($lastVal['brokerMobile']))echo"value='".showPhoneNumber($lastVal['brokerMobile'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Carrier:</strong></td>
						<td class="last">
						<?
						$queryCarrier = "select * from carrier";
						$carriers = mysql_query($queryCarrier,$conexion);
						echo "<select name='carrierId' id='carrierId' style='font-family:verdana;font-size:8pt'>";
						if(!(isset($lastVal['carrierName'])&&$lastVal['carrierName']!=null))
						{
							echo "<option selected='selected'>--Select Carrier--</option>";
							while($carrier=mysql_fetch_assoc($carriers))
							{
								echo "<option value='{$carrier['carrierId']}'>{$carrier['carrierName']}</option>";
							}
						}
						else
						{
							while($carrier=mysql_fetch_assoc($carriers))
							{
								if($carrier['carrierId']==$lastVal['carrierId'])
									echo "<option selected='selected' value='{$carrier['carrierId']}'>{$carrier['carrierName']}</option>";
								else
									echo "<option value='{$carrier['carrierId']}'>{$carrier['carrierName']}</option>";
							}
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerMail' name='brokerMail' <?if(isset($lastVal['brokerMail']))echo"value='".$lastVal['brokerMail']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1' <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?> /></td>
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
						$queryTerm = "select * from term";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='termId' id='termId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms > 0)
						{
							while($term=mysql_fetch_assoc($terms))
							{
								if(isset($lastVal['termId'])&& $lastVal['termId'] == $term['termId'])
									echo "<option selected='selected' value='{$term['termId']}'>{$term['termName']}</option>";
								else
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
						<td class="first"><strong>Tax:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerTax' name='brokerTax' <?if(isset($lastVal['brokerTax']))echo"value='".$lastVal['brokerTax']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>ICC:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerIccCert' name='brokerIccCert' <?if(isset($lastVal['brokerIccCert']))echo"value='".$lastVal['brokerIccCert']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Insurance WC:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='brokerInsWc' name='brokerInsWc' <?if(isset($lastVal['brokerInsuranceWc']))echo"value='".$lastVal['brokerInsuranceWc']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='brokerWcExpire' name='brokerWcExpire' <?if(isset($lastVal['brokerWcExpire']))echo"value='".to_MDY($lastVal['brokerWcExpire'])."'";?>  /></td>
					</tr>
					<tr>
						<td class="first"><strong><!--Insurance-->Automovile Liability:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='brokerInsLiability' name='brokerInsLiability' <?if(isset($lastVal['brokerInsuranceLiability']))echo"value='".$lastVal['brokerInsuranceLiability']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='brokerLbExpire' name='brokerLbExpire' <?if(isset($lastVal['brokerLbExpire']))echo"value='".to_MDY($lastVal['brokerLbExpire'])."'";?> /></td>
					</tr>
					<!--aqui cambie-->
					<tr>
						<td class="first"><strong>General Liability:</strong>
						<td class="last"><input type="text" class="text" id='brokerGeneralLiability' name='brokerGeneralLiability' <?if(isset($lastVal['brokerGeneralLiability']))echo"value='".$lastVal['brokerGeneralLiability']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Expires:</strong>
						<td class="last"><input type="text" class="text" id='brokerGlExp' name='brokerGlExp' <?if(isset($lastVal['brokerGlExp']))echo"value='".to_MDY($lastVal['brokerGlExp'])."'";?> /></td>
					</tr>
				<!--hasta aqui cambie-->
					<tr>
						<td class="first"><strong>Percentage:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerPercentage' name='brokerPercentage'  <?if(isset($lastVal['brokerPercentage']))echo"value='".$lastVal['brokerPercentage']."'";?>  /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Start Date:</strong></td>
						<td class="last"><input type="text" class="text" id='brokerStartDate' name='startupDate'  <?if(isset($lastVal['brokerStartDate']))echo"value='".to_MDY($lastVal['brokerStartDate'])."'";?>  /></td>
					</tr>
					<tr>
						<td class="first"><strong>Gender:</strong></td>
						<td class="last">
							<select name="brokerGender" id="brokerGender" style='font-family:verdana;font-size:8pt'>
								<option value="" >--Gender--</option>
								<option value="Male" <?echo ($lastVal['brokerGender']=="Male"?"selected='selected'":"");?> >Male</option>
								<option value="Female" <?echo ($lastVal['brokerGender']=="Female"?"selected='selected'":"");?> >Female</option>
							</select>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Ethnicity:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from ethnic order by ethnicName";
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
					<tr>
						<td class="first"><strong>Status:</strong></td>
						<td class="last">
							<label>
							Active<input type='radio' name='brokerStatus' id='radioActive' value='1' <?if(isset($lastVal['brokerStatus'])&&$lastVal['brokerStatus'])echo"checked";?> />
							Inactive<input type='radio' name='brokerStatus' id='radioInactive' value='0' <?if(isset($lastVal['brokerStatus'])&&!$lastVal['brokerStatus'])echo"checked";?> />
							
							</label>
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
					<th>UID</th>
					<th>Name</th>
				</tr>
				<tbody>
				<?
				$brokers = mysql_query("select * from broker order by brokerName",$conexion);
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
								echo "<td ".$tdClass." id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								echo "<td ".$tdClass.">".$broker['brokerPid']."</td>";
								echo "<td ".$tdClass." id='broker".$broker['brokerId']."'>".$broker['brokerName']."</td>";
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
