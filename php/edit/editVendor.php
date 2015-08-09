<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Vendor";
$description = "Edit a vendor. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
		vendor
	JOIN address using (addressId)
	WHERE
		vendorId=".$_GET['i'];
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
var patternLetters = new RegExp(/vendor/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this ticket?');
	});
	$('#vendors tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editVendor.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});

	$('#brokerPid').change(function(){getBrokers();});
	$('#vendorId').keyup(function(){getVendors();});
	$('#brokerName').change(function(){getBrokers();});
	$('#vendorName').keyup(function(){getVendors();});

});

function getVendors(){
	
	var vendorId=$('#vendorId').val();
	var vendorName=$('#vendorName').val();
	//alert(vendorStatus);
	$.ajax({
		type: "GET",
		url: "getEditVendors.php",
		data: "vendorId="+vendorId+"&vendorName="+vendorName,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#vendors > tbody:last').remove();
					$('#vendors').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function validateForm(){

	if(document.getElementById('vendorName').value.length==0){
		alert("Please type a name for the vendor");
		document.formValidate.vendorName.focus;
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
				<a href="#" class="vendor"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditVendor.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['vendorId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Edit Vendor</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='vendorName' name='vendorName' <?if(isset($lastVal['vendorName']))echo"value='".$lastVal['vendorName']."'";?>/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Additional Information:</strong></td>
						<td class="last"><textarea rows="1" cols="43" type="text" class="text" id='vendorInfo' name='vendorInfo' >
						<?if(isset($lastVal['vendorInfo']))echo$lastVal['vendorInfo'];?>
						</textarea>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Comment:</strong></td>
						<td class="last"><input type="text" class="text" id='vendorComment' name='vendorComment' <?if(isset($lastVal['vendorComment']))echo"value='".$lastVal['vendorComment']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><input type="text" class="text" id='supplierTel' name='supplierTel'  <?if(isset($lastVal['vendorTel']))echo"value='".showPhoneNumber($lastVal['vendorTel'])."'";?>  /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"><input type="text" class="text" id='supplierFax' name='supplierFax'  <?if(isset($lastVal['vendorFax']))echo"value='".showPhoneNumber($lastVal['vendorFax'])."'";?>  /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1'  <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?>  /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2' <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?> /></td>
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
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip'  <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?>  /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox' <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?> /></td>
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
					<th class="full"  colspan='5'>Search Vendors</th>
				</tr>
				<tr>
					<td>Name</td>
					<td>Id</td>
				</tr>
				<tr>
					<td><input type='text' size='8px' id='vendorName' name='vendorName' /></td>
					<td><input type='text' size='8px' id='vendorId' name='vendorId' /></td>
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='vendors'>
				<tr>
					<th class="full"  colspan='9'>VENDORS</th>
				</tr>
				<tr>
					<th>Name</th>
					<th>Name</th>
				</tr>
				<tbody>
				<?
				$vendors = mysql_query("select * from vendor order by vendorName",$conexion);
				$numvendors=mysql_num_rows($vendors);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numvendors>0)
				{
						while($vendor=mysql_fetch_assoc($vendors))
						{
							if($actual){
								echo "<tr>";
								echo "<td ".$tdClass." id='vendor".$vendor['vendorId']."'>".$vendor['vendorName']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								echo "<td ".$tdClass." id='vendor".$vendor['vendorId']."'>".$vendor['vendorName']."</td>";
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
