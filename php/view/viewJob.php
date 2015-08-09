<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Project";
$description = "Add a new job/project. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
 		project
	JOIN address USING (addressId)
	LEFT JOIN jobland USING (jobLandId)
	LEFT JOIN jobterrain USING (jobTerrainId)
	LEFT JOIN customer USING (customerId)
	WHERE
		projectId=".$_GET['i'];
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);
##########################################
	$projectId = $lastVal['projectId'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?echo$title." -".$subtitle;?></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" href="/trucking/img/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="/trucking/css/nyroModal.css" type="image/x-icon" />
	<style media="all" type="text/css">@import "../../css/all.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript" src="/trucking/js/jquery.nyroModal-1.6.2.js" ></script>
<script type="text/javascript">	
var patternLetters = new RegExp(/project/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('.popable').nyroModal({
			callbacks: {
				afterClose: function() {
					alert("cerrado");
				},
				close: function() {
					alert("cerrando");
				}
			}
	});
	
	$('.clickable').click(function() {
		$(this).parents('table').find('.displayable').toggle();
	});
	
	$('#customer').change(function() {
		var customer=this.selectedIndex;
		getContacts(customer);
	});
	
		$('#projects tr td').live('dblclick',function(){
		var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewJob.php?i="+id);
				
			}
	});
	
	$('#searchId').keyup(function(){getProjects();});
	$('#searchName').keyup(function(){getProjects();});
	$('#searchAddress').keyup(function(){getProjects();});
	$('#searchCity').keyup(function(){getProjects();});
	$('#searchActive').change(function(){getProjects();});
	$('#searchCustomerId').change(function(){
			getProjects();
	});
});
function getContacts(customer){
	$.ajax({
		type: "GET",
		url: "getContacts.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var contacts=$('#contact');
			contacts.children().remove();
			jQuery.each(obj, function(i,val){
				contacts.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function getProjects(){
	var projectId=$('#searchId').val();
	var projectName=$('#searchName').val();
	var projectActive=$('#searchActive').val();
	var addressLine=$('#searchAddress').val();
	var addressCity=$('#searchCity').val();
	var customerId=$('#searchCustomerId').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewProjects.php",
		data: "projectId="+projectId+"&projectName="+projectName+"&projectActive="+projectActive+"&addressLine="+addressLine+"&addressCity="+addressCity+"&customerId="+customerId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#projects > tbody:last').remove();
					$('#projects').append(obj.table);
					//$($('#projects tbody')[1]).remove();
					//$('#projects').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function popUpProposal(projectId){
	var url = '/trucking/php/commonUtils/showProjectProposal.php?i='+projectId;
	var windowName = 'popUp';
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(url,windowName,windowSize);
	event.preventDefault();
}

function validateForm(){

	if(document.getElementById('projectName').value.length==0){
		alert("Please type a name for this project");
		document.formValidate.projectName.focus;
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
			$projectItemsQuery = "SELECT count(*) as totalItems FROM item WHERE projectId = ".$projectId;
			$projectItemInfo = mysql_fetch_assoc(mysql_query($projectItemsQuery, $conexion));
			$totalItems = $projectItemInfo['totalItems'];
			
			$projectInvoicesQuery = "SELECT count(*) as totalInvoices FROM invoice WHERE projectId = ".$projectId;
			$projectInvoiceInfo = mysql_fetch_assoc(mysql_query($projectInvoicesQuery, $conexion));
			$totalInvoices = $projectInvoiceInfo['totalInvoices'];
			
			$projectTicketsQuery = "SELECT count(*) as totalTickets FROM item JOIN ticket USING (itemId) WHERE projectId = ".$projectId;
			$projectTicketInfo = mysql_fetch_assoc(mysql_query($projectTicketsQuery, $conexion));
			$totalTickets = $projectTicketInfo['totalTickets'];
			
			$projectItemProposalsQuery = "SELECT count(*) as totalItemProposals FROM item_proposal WHERE projectId = ".$projectId;
			$projectItemProposalInfo = mysql_fetch_assoc(mysql_query($projectItemProposalsQuery, $conexion));
			$totalItemProposals = $projectItemProposalInfo['totalItemProposals'];
		?>
		
			<div class="top-bar">
				<a href="#" class="project"></a>
				<a onclick="return confirm('Are you sure you want to delete <?echo$lastVal['projectName'];?> and all its tickets?');"  href="deleteJob.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editJob.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				
				
				<a href="/trucking/php/new/newItem.php?c=<?echo$lastVal['customerId'];?>&p=<?echo$lastVal['projectId'];?>&pA=<?echo urlencode($lastVal['addressLine1']);?>&pAI=<?echo$lastVal['addressId'];?>" >
					<img src='/trucking/img/95.png' title='Add item' />
				</a>
				<a class='popable' href="/trucking/php/popUps/newItemProposalByProject.php?projectId=<?echo$lastVal['projectId'];?>" >
					<img src='/trucking/img/8.png' title='Add proposal' />
				</a>
				<?
				if($totalItemProposals > 0) {
				?>
					<a href="#" onclick="popUpProposal(<?echo$projectId;?>);" >
					<img src='/trucking/img/2.png' title='Print proposal' />
					</a>
				<?
				}
				?>
				
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
			
			<div class='table'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Job Basic Information</th>
					</tr>
					<tr>
						<td class="first" width="172">Project Number:</td>
						<td class="last"><strong><?echo$lastVal['projectId'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Project Name:</td>
						<td class="last"><strong><?echo$lastVal['projectName'];?></strong></td>
					</tr>
					<tr>
						<td class="first" width="172">Address:</td>
						<td class="last"><strong><?echo$lastVal['addressLine1']."<br/>".$lastVal['addressCity'].", ".$lastVal['addressState'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Customer:</td>
						<td class="last">
							<strong><?echo$lastVal['customerName'];?></strong>
							<a href='/trucking/php/view/viewCustomer.php?i=<?echo$lastVal['customerId'];?>'>
								<img src='/trucking/img/16.png' width='12' height='12' />
							</a>
						</td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitJob.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr class="clickable">
						<th class="full" colspan="2">View Job</th>
					</tr>
					<tbody style="display:none;" class="displayable">
					<tr class="bg">
						<td class="first" width="172"><strong>Project Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['projectId']))echo$lastVal['projectId'];?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><?if(isset($lastVal['projectName']))echo$lastVal['projectName'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Anticipated Startup Date:</strong></td>
						<td class="last"><?if(isset($lastVal['projectStartup']))echo to_MDY($lastVal['projectStartup']);?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Type:</strong></td>
						<td class="last">
						<?if(isset($lastVal['jobLandName'])&&$lastVal['jobLandName']!=null)echo$lastVal['jobLandName'];?>
						</td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Project Terrain:</strong></td>
						<td class="last">
						<?if(isset($lastVal['jobTerrainName'])&&$lastVal['jobTerrainName']!=null)echo$lastVal['jobTerrainName'];?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><?if(isset($lastVal['addressLine1']))echo$lastVal['addressLine1'];?> </td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressLine2']))echo$lastVal['addressLine2'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>City:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressCity']))echo$lastVal['addressCity'];?> </td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['addressState']))echo$lastVal['addressState'];
						?>
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><?if(isset($lastVal['addressZip'])&&$lastVal['addressZip']!=0)echo$lastVal['addressZip'];?></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><?if(isset($lastVal['addressPOBox']))echo$lastVal['addressPOBox'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>County:</strong></td>
						<td class="last"><?if(isset($lastVal['projectCounty']))echo$lastVal['projectCounty'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Township:</strong></td>
						<td class="last"><?if(isset($lastVal['projectTownship']))echo$lastVal['projectTownship'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>IEPA:</strong></td>
						<td class="last"><?if(isset($lastVal['projectIepa']))echo$lastVal['projectIepa'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>BOW:</strong></td>
						<td class="last"><?if(isset($lastVal['projectBow']))echo$lastVal['projectBow'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>BOA:</strong></td>
						<td class="last"><?if(isset($lastVal['projectBoa']))echo$lastVal['projectBoa'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Materials:</strong></td>
						<td class="last"><?if(isset($lastVal['projectMaterial']))echo$lastVal['projectMaterial'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Scoop of Work:</strong></td>
						<td class="last"><?if(isset($lastVal['projectSw']))echo$lastVal['projectSw'];?></textarea></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Project Loads:</strong></td>
						<td class="last"><?if(isset($lastVal['projectLoads']))echo$lastVal['projectLoads'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Project Trucks:</strong></td>
						<td class="last"><?if(isset($lastVal['projectTrucks']))echo$lastVal['projectTrucks'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Enviromental Assessment:</strong></td>
						<td class="last"><?if(isset($lastVal['project']))echo$lastVal['project'];?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Project PIN:</strong></td>
						<td class="last"><?if(isset($lastVal['projectPin']))echo$lastVal['projectPin'];?></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Customer:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['customerName'])&&$lastVal['customerName']!=null)echo$lastVal['customerName'];
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Contact:</strong></td>
						<td class="last">
						<?
						if($lastVal['contactId']!=0){
							$contactQuery = "select * from contact where contactId=".$lastVal['contactId'];
							$contactResult = mysql_query($contactQuery,$conexion);
							$contactInfo = mysql_fetch_assoc($contactResult);
							echo $contactInfo['contactName'];
						}
						?>
						</td>
					</tr>
					<tr class='bg'>
						<td class="first"><strong>Project Company:</strong></td>
						<td class="last"><?if(isset($lastVal['projectCompany']))echo$lastVal['projectCompany'];?></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 1:</strong></td>
						<td class="last"><?echo decimalPad($lastVal['projectClass1PW']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 2:</strong></td>
						<td class="last"><?echo decimalPad($lastVal['projectClass2PW']);?></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Class 3:</strong></td>
						<td class="last"><?echo decimalPad($lastVal['projectClass3PW']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Prevailing Wage for Class 4:</strong></td>
						<td class="last"><?echo decimalPad($lastVal['projectClass4PW']);?></td>
					</tr>
					<tr>
						<td class="first"><strong>Prevailing Wage for Broker:</strong></td>
						<td class="last"><?echo decimalPad($lastVal['projectBrokerPW']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Under:</strong></td>
						<td class="last">
							<?
								switch($lastVal['projectUnder']) {
									case NO_ACT:
										echo "No Act/Other";
									break;
									case ILLINOIS_PW_ACT:
										echo "Illinois Prevailing Wage Act";
									break;
									case DAVIS_BACON_ACT:
										echo "Davis Bacon Act";
									break;
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Approval Number:</strong></td>
						<td class="last"><?if(isset($lastVal['projectApprovalNumber']))echo$lastVal['projectApprovalNumber'];?></td>
					</tr>
					</tbody>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr class="clickable">
						<th class="full" colspan="2">Job Metrics</th>
					</tr>
					<tbody style="display:none;" class="displayable">
						<tr class="bg">
							<td class='first'>Items:</td>
							<td class='last'>
							<?
							
							if($totalItems == 0) {
								echo "<span style='color:red;'>No Items associated with this project</span>";
							} else {
								echo $totalItems;
							}
							?>
							</td>
						</tr>
						<tr>
							<td class='first'>
								Invoices:
								<a href="" class='popable'>
									<img src='/trucking/img/16.png' width='12' height='12' />
								</a>
							</td>
							<td class='last'>
							<?
							
							if($totalInvoices == 0) {
								echo "<span style='color:red;'>No Invoices associated with this project</span>";
							} else {
								echo $totalInvoices;
							}
							?>
							</td>
						</tr>
						<tr class="bg">
							<td class='first'>Tickets:</td>
							<td class='last'>
							<?
							if($totalTickets == 0) {
								echo "<span style='color:red;'>No Tickets associated with this project</span>";
							} else {
								echo $totalTickets;
							}
							?>
							</td>
						</tr>
						<tr>
							<td class='first'>Item Proposals:</td>
							<td class='last'>
							<?
							
							if($totalItemProposals == 0) {
								echo "<span style='color:green;'>There are no pending proposals</span>";
							} else {
								echo $totalItemProposals;
							}
							?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<?if($totalItems>0) {?>
				<div class='table'>
					<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
					<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
					<table class="listing form" cellpadding="0" cellspacing="0">
						<tr class="clickable">
							<th class="full" colspan="3">Items</th>
						</tr>
						<tbody style="display:none;" class="displayable">
							<tr>
								<th>Number</th>
								<th>Description</th>
								<th>Tickets</th>
							</tr>
						<?
						$itemsQuery = "SELECT * FROM item WHERE projectId = ".$projectId;
						$items = mysql_query($itemsQuery, $conexion);
						$flag = true;
						while($item = mysql_fetch_assoc($items)) {
							$ticketCount = mysql_fetch_assoc(mysql_query("SELECT count(*) as ticketsPerItem FROM ticket WHERE itemId = ".$item['itemId'], $conexion));
							echo "<tr ".($flag?"class='bg'":"").">";
							echo "<td>".$item['itemNumber']."</td>";
							echo "<td>".$item['itemDescription']."</td>";
							echo "<td>".$ticketCount['ticketsPerItem']."</td>";
							echo "</tr>";
							$flag = !$flag;
						}
						?>
						</tbody>
					</table>
				</div>
			<? } ?>
			
			<?if($totalItemProposals>0) {?>
				<div class='table'>
					<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
					<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
					<table class="listing form" cellpadding="0" cellspacing="0">
						<tr class="clickable">
							<th class="full" colspan="2">Item Proposals</th>
						</tr>
						<tbody style="display:none;" class="displayable">
							<tr>
								<th>Last Updated</th>
								<th>Description</th>
							</tr>
						<?
						$itemsQuery = "SELECT * FROM item_proposal WHERE projectId = ".$projectId." ORDER BY itemProposalCreationDate desc";
						$items = mysql_query($itemsQuery, $conexion);
						$flag = true;
						while($item = mysql_fetch_assoc($items)) {
							echo "<tr ".($flag?"class='bg'":"").">";
							echo "<td>".to_MDY($item['itemProposalCreationDate'])."</td>";
							echo "<td>".$item['itemProposalDescription']."</td>";
							echo "</tr>";
							$flag = !$flag;
						}
						?>
						</tbody>
					</table>
				</div>
			<? } ?>
			
		<?
		}else{
		?>	
			<div class="table" id="search-bar">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
					<tr>
						<th class="full" colspan="6">Search Project</th>
					</tr>
					<tr class="bg">
						<td>Id</td>
						<td>Name</td>
						<td>Address</td>
						<td>City</td>
						<td></td>
						<td>Customer</td>
					</tr>
					<tr>
						<td><input type="text"  size='8px' name="searchId" id="searchId" /></td>
						<td><input type="text"  size='8px' name="searchName" id="searchName" /></td>
						<td><input type="text"  size='8px' name="searchAddress" id="searchAddress" /></td>
						<td><input type="text"  size='8px' name="searchCity" id="searchCity" /></td>
						<td><select name='searchActive' id="searchActive" >
							<option value="-1">All</option>
							<option value="0">Active</option>
							<option value="1">Inactive</option>
							</select>
						</td>
						<td class="last">
						<?
						$queryTerm = "select * from customer order by customerName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='searchCustomerId' id='searchCustomerId' style='font-family:verdana;font-size:8pt;width: 100px' >";
						echo "<option value='0'>--Select--</option>";
						if($countTerms > 0)
						{
							while($term=mysql_fetch_assoc($terms))
							{
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
				</table>
			</div>
			
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id="projects">
					<tr><th class="full" colspan="5" >Projects</th></tr>
					<tr>
						<th>Id</th><th>Name</th><th>Address</th><th>Approval Numbers</th>
					</tr>
					<tbody>
					<?
					$queryProjects = "select * from project order by projectId desc";
					$projects = mysql_query($queryProjects,$conexion);
					$numProjects = mysql_num_rows($projects);
					$first =true;
					$class = " class='bg' ";
					while($project = mysql_fetch_assoc($projects)){
							
							$addressInfo = mysql_fetch_assoc(mysql_query("select * from address where addressId=".$project['addressId'],$conexion));
							echo "<tr $class>";
							echo "<td >".$project['projectId']."</td>";
							echo "<td id='project".$project['projectId']."' $class>".$project['projectName']."</td>";
							echo "<td >".$addressInfo['addressLine1']."</td>";
							echo "<td >".$project['projectApprovalNumber']."</td>";
							echo "</tr>";
						if($class=="")$class=" class='bg' ";
						else $class="";
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
