<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Contact";
$description = "Edit contac. Contacts have to be assigned to a customer. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
		contact
	JOIN
		address
	ON
		address.addressId=contact.addressId and
		contactId=".$_GET['i'];
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
var patternLetters = new RegExp(/contact/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this ticket?');
	});
	$('#contacts tr td').live('dblclick',function(){
			
			var id=$(this).closest('tr').attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editContact.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});

	$('#customerId').change(function(){getContacts();});
	$('#contactName').keyup(function(){getContacts();});

});

function getContacts(){
	
	var customerId=$('#customerId').val();
	var contactName=$('#contactName').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getEditContacts.php",
		data: "customerId="+customerId+"&contactName="+contactName,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#contacts > tbody:last').remove();
					$('#contacts').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}
function validateForm(){

	if(document.getElementById('contactName').value.length==0){
		alert("Please type a name for the contact");
		document.formValidate.contactName.focus;
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
				<a href="#" class="contact"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditContact.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['contactId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Edit Contact</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryTerm = "select * from customer";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms > 0)
						{	
								while($term=mysql_fetch_assoc($terms))
								{
									if($lastVal['customerId']==$term['customerId'])
										echo "<option selected='selected' value='{$term['customerId']}'>{$term['customerName']}</option>";
									echo "<option value='{$term['customerId']}'>{$term['customerName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no customers in the DataBase</option>";
							
						}
						echo "</select>";
						?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='contactName' name='contactName' <?if(isset($lastVal['contactName']))echo"value='".$lastVal['contactName']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"><input type="text" class="text" id='contactTel' name='contactTel' <?if(isset($lastVal['contactTel']))echo"value='".showPhoneNumber($lastVal['contactTel'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"><input type="text" class="text" id='contactFax' name='contactFax' <?if(isset($lastVal['contactFax']))echo"value='".showPhoneNumber($lastVal['contactFax'])."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Mobile:</strong></td>
						<td class="last"><input type="text" class="text" id='contactMobil' name='contactMobil'  <?if(isset($lastVal['contactMobil']))echo"value='".showPhoneNumber($lastVal['contactMobil'])."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-Mail:</strong></td>
						<td class="last"><input type="text" class="text" id='contactMail' name='contactMail' <?if(isset($lastVal['contactMail']))echo"value='".$lastVal['contactMail']."'";?> /></td>
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
					<tr class="bg">
						<td class="first"><strong>Additional Information:</strong></td>
						<td class="last"><textarea rows="2" cols="43" class="text" id='contactInfo' name='contactInfo' /> <?if(isset($lastVal['contactInfo']))echo$lastVal['contactInfo'];?> </textarea></td>
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
							<div class='table' id='searchBar'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
				<tr>
					<th class="full"  colspan='5'>Search Contacts</th>
				</tr>
				<tr>
					<td>Customer</td>
					<td>Name</td>
				</tr>
				<tr>
					<td>
							<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt' >
							<option value='0'>--Select Customer--</option>
							<?
							$customers = mysql_query("select * from customer order by customerName",$conexion);
							while($customer=mysql_fetch_assoc($customers)){
								echo "<option value='".$customer['customerId']."' >".$customer['customerName']."</option>";
							}
							?>
							</select>
						</td>
					<td><input type='text' size='8px' id='contactName' name='contactName' /></td>
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='contacts'>
				<tr>
					<th class="full"  colspan='9'>CONTACTS</th>
				</tr>
				<tr>
					<th></th>
					<th>Customer</th>
					<th>Contact Name</th>
				</tr>
				<tbody>
				<?
				$contacts = mysql_query("select * from contact JOIN customer using (customerId) order by customerName",$conexion);
				$numcontacts=mysql_num_rows($contacts);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numcontacts>0)
				{
						while($contact=mysql_fetch_assoc($contacts))
						{
							if($actual){
								echo "<tr id='contact".$contact['contactId']."'>";
								echo "<td ".$tdClass.">From: </td>";
								echo "<td ".$tdClass.">".$contact['customerName']."</td>";
								echo "<td ".$tdClass.">".$contact['contactName']."</td>";
								echo "</tr>";
								
							}else{
								echo "<tr id='contact".$contact['contactId']."'>";
								echo "<td ".$tdClass.">From: </td>";
								echo "<td ".$tdClass.">".$contact['customerName']."</td>";
								echo "<td ".$tdClass.">".$contact['contactName']."</td>";
								
								echo "</tr>";
							}
							$actual=!$actual;
							$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
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
