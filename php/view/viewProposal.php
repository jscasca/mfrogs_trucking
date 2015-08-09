<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Proposal";

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
###############Show Item###############
$queryLast =
	"SELECT 
		*
	FROM
		fakeitem
	JOIN fakeproject ON fakeitem.projectId=fakeproject.fakeprojectId
	JOIN supplier using (supplierId)
	JOIN material using (materialId)
	JOIN (select addressId as fromAddressId, addressLine1 as fromAddress from address) as F using (fromAddressId)
	JOIN (select addressId as toAddressId, addressLine1 as toAddress from address) as T using (toAddressId)
	WHERE
		itemId=".$_GET['i'];
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
var patternLetters = new RegExp(/item/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('#items tr td').live('dblclick',function(){
			
			var id=$(this).closest('tr').attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewProposal.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});
	
	$('#sfakeprojectId').change(function(){getItems();});
	$('#sitemNumber').change(function(){getItems();});
	$('#smaterialId').change(function(){getItems();});
	$('#sfrom').keyup(function(){getItems();});
	$('#sto').keyup(function(){getItems();});
});
function getItems(){
	var sfakeprojectId = $('#sfakeprojectId').val();
	var sitemNumber = $('#sitemNumber').val();
	var smaterialId = $('#smaterialId').val();
	var sfrom = $('#sfrom').val();
	var sto = $('#sto').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewFakeItems.php",
		data: "sfakeprojectId="+sfakeprojectId+"&sitemNumber="+sitemNumber+"&smaterialId="+smaterialId+"&sfrom="+sfrom+"&sto="+sto,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#items > tbody:last').remove();
					$('#items').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function validateForm(){

	if(document.getElementById('customerName').value.length==0){
		alert("Please type a name for the customer");
		document.formValidate.customerName.focus
		return false;
	}
	if(document.getElementById('termId').selectedIndex==0 ){
		alert("Please select a payment term");
			document.formValidate.termId.focus
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
				
				<a href="#" class="project"></a>
				<?
				?>
				<a onclick="return confirm('Are you sure you want to delete the item <?echo$lastVal['itemNumber'];?>?');"  href="deleteProposal.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editProposal.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				<?
				?>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitEditProposal.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
				<tr>
						<th class="full" colspan="2">View Proposal</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Item Number:</strong><span style="color:red;">*</span></td>
						<td class="last">
							 <?if(isset($lastVal['itemNumber']))echo$lastVal['itemNumber'];?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Shell Project:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['fakeprojectId']))
						{
							echo$lastVal['fakeprojectId']." ".$lastVal['fakeprojectName'];
							echo "&nbsp;<a href='/trucking/php/view/viewShellProject.php?i=".$lastVal['fakeprojectId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Supplier:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						if(isset($lastVal['supplierId']))
						{
							echo$lastVal['supplierName'];
							echo "&nbsp;<a href='/trucking/php/view/viewSupplier.php?i=".$lastVal['supplierId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Material:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?if(isset($lastVal['materialName']))echo$lastVal['materialName'];?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>From:</strong></td>
						<td class="last">
							<?if(isset($lastVal['itemDisplayFrom']))echo$lastVal['itemDisplayFrom'];?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>To:</strong></td>
						<td class="last">
							<?if(isset($lastVal['itemDisplayTo']))echo$lastVal['itemDisplayTo'];?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Material Price:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['itemMaterialPrice']))echo decimalPad($lastVal['itemMaterialPrice']);?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Broker Cost:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['itemBrokerCost']))echo decimalPad($lastVal['itemBrokerCost']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Customer Cost:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['itemCustomerCost']))echo decimalPad($lastVal['itemCustomerCost']);?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Type:</strong></td>
						<td class="last">
						<?if(isset($lastVal['itemType']))echo$LTH[$lastVal['itemType']];?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Description:</strong></td>
						<td class="last"><?if(isset($lastVal['itemDescription']))echo$lastVal['itemDescription'];?>
						</td>
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
					<th class="full"  colspan='5'>Search Proposals</th>
				</tr>
				<tr>
					<td>ShellProject Id</td>
					<td>Item Number</td>
					<td>Material</td>
					<td>From</td>
					<td>To</td>
				</tr>
				<tr>
					<td><input type='text' size='8px' id='sfakeprojectId' name='sfakeprojectId' /></td>
					<td><input type='text' size='8px' id='sitemNumber' name='sitemNumber' /></td>
					<td>
						<select name='smaterialId' id='smaterialId' style='font-family:verdana;font-size:8pt' >
							<option value='0'>--Select Material--</option>
							<?
							$materials = mysql_query("select * from material order by materialName",$conexion);
							while($material=mysql_fetch_assoc($materials)){
								echo "<option value='".$material['materialId']."' >".$material['materialName']."</option>";
							}
							?>
							</select>
					</td>
					<td><input type='text' size='8px' id='sfrom' name='sfrom' /></td>
					<td><input type='text' size='8px' id='sto' name='sto' /></td>
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='items'>
				<tr>
					<th class="full"  colspan='9'>PROPOSALS</th>
				</tr>
				<tr>
					<th>ShellProject</th>
					<th>Item #</th>
					<th>Material</th>
					<th>From</th>
					<th>To</th>
				</tr>
				<tbody>
				<?
				$query = "
					SELECT
						*
					FROM
						fakeitem
						JOIN material using (materialId)
						JOIN (SELECT addressLine1 as fromAddress, addressId as fromAddressId from address) as F using (fromAddressId)
						JOIN (SELECT addressLine1 as toAddress, addressId as toAddressId from address) as T using (toAddressId)
						order by itemId desc
						";
				$items = mysql_query($query,$conexion);
				$numitems=mysql_num_rows($items);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numitems>0)
				{
						while($item=mysql_fetch_assoc($items))
						{
								echo "<tr id='item".$item['itemId']."'>";
								echo "<td ".$tdClass.">".$item['projectId']."</td>";
								echo "<td ".$tdClass.">".$item['itemNumber']."</td>";
								echo "<td ".$tdClass.">".$item['materialName']."</td>";
								echo "<td ".$tdClass.">".$item['fromAddress']."</td>";
								echo "<td ".$tdClass.">".$item['toAddress']."</td>";
								echo "</tr>";
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
