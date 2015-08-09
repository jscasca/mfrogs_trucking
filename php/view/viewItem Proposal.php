<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Item";

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
		item_proposal
	JOIN project using (projectId)
	JOIN customer using (customerId)
	JOIN supplier using (supplierId)
	JOIN material using (materialId)
	WHERE
		itemProposalId=".$_GET['i'];
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
				window.location.replace("./viewItem Proposal.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});
	
	$('#ticketListing tr td').live('dblclick',function(){
		var id = $(this).closest('tr').attr('ticketId');
		window.location = "viewTicket.php?i="+id;
	});
	
	$('#sprojectId').change(function(){getItems();});
	$('#smaterialId').change(function(){getItems();});
});
function getItems(){
	var sprojectId = $('#sprojectId').val();
	var smaterialId = $('#smaterialId').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewItemProposals.php",
		data: "sprojectId="+sprojectId+"&smaterialId="+smaterialId,
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
				if($lastVal['ticketId']==null){
				?>
				<a onclick="return confirm('Are you sure you want to delete the item proposal?');"  href="deleteItemProposal.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editItem Proposal.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				<?
				}
				?>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitEditItem.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
				<tr>
						<th class="full" colspan="2">View Item</th>
					</tr>
					<tr>
						<td class="first" width="172">Project:<span style="color:red;">*</span></td>
						<td class="last"><strong>
						<?
						if(isset($lastVal['projectId']))
						{
							echo$lastVal['projectId']." - ".$lastVal['projectName'];
							echo "&nbsp;<a href='/trucking/php/view/viewJob.php?i=".$lastVal['projectId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</strong></td>
					</tr>
					<tr class="bg">
						<td class="first">Customer:</td>
						<td class="last"><strong>
							<?if(isset($lastVal['customerName']))echo$lastVal['customerName'];?>
						</strong></td>
					</tr>
					<tr>
						<td class="first">Supplier:<span style="color:red;">*</span></td>
						<td class="last"><strong>
						<?
						if(isset($lastVal['supplierId']))
						{
							echo$lastVal['supplierName'];
							echo "&nbsp;<a href='/trucking/php/view/viewSupplier.php?i=".$lastVal['supplierId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Material:<span style="color:red;">*</span></td>
						<td class="last"><strong>
						<?echo$lastVal['materialName'];?>
						</strong></td>
					</tr>
					<tr>
						<td class="first">From:</td>
						<td class="last"><strong>
							<?echo$lastVal['itemProposalDisplayFrom'];?>
						</strong></td>
					</tr>
					<tr  class="bg">
						<td class="first" width="172">To:</td>
						<td class="last"><strong>
							<?echo$lastVal['itemProposalDisplayTo'];?>
						</strong></td>
					</tr>
					<tr>
						<td class="first">Material Price:<span style="color:red;">*</span></td>
						<td class="last"><strong><?echo decimalPad($lastVal['itemProposalMaterialPrice']);?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Broker Cost:<span style="color:red;">*</span></td>
						<td class="last"><strong><?echo decimalPad($lastVal['itemProposalBrokerCost']);?></strong></td>
					</tr>
					<tr>
						<td class="first">Customer Cost:<span style="color:red;">*</span></td>
						<td class="last"><strong><?echo decimalPad($lastVal['itemProposalCustomerCost']);?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Type:</td>
						<td class="last">
						<strong><?echo$LTH[$lastVal['itemProposalType']];?></strong>
						</td>
					</tr>
					<tr>
						<td class="first">Description:</td>
						<td class="last"><strong><?echo$lastVal['itemProposalDescription'];?></strong>
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
					<th class="full"  colspan='5'>Search Items</th>
				</tr>
				<tr>
					<td>Job Id</td>
					<td>Material</td>
				</tr>
				<tr>
					<td><input type='text' size='8px' id='sprojectId' name='sprojectId' /></td>
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
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='items'>
				<tr>
					<th class="full"  colspan='9'>ITEMS</th>
				</tr>
				<tr>
					<th>Job</th>
					<th>Item #</th>
					<th>Material</th>
					<th>From</th>
					<th>To</th>
					<th>Customer</th>
					<th>Broker</th>
					<th>Material</th>
				</tr>
				<tbody>
				<?
				$query = "
					SELECT
						*
					FROM
						item_proposal
						JOIN material using (materialId)
						order by itemProposalId desc
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
								echo "<tr id='item".$item['itemProposalId']."'>";
								echo "<td ".$tdClass.">".$item['projectId']."</td>";
								echo "<td ".$tdClass.">".$item['itemProposalId']."</td>";
								echo "<td ".$tdClass.">".$item['materialName']."</td>";
								echo "<td ".$tdClass.">".$item['itemProposalDisplayFrom']."</td>";
								echo "<td ".$tdClass.">".$item['itemProposalDisplayTo']."</td>";
								echo "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalCustomerCost'])."</td>";
								echo "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalBrokerCost'])."</td>";
								echo "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalMaterialPrice'])."</td>";
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
