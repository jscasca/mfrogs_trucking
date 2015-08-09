<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Invoice";
$description = "Add a new invoice. Invoices contain all the tickets already created in a range of time. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
	<style media="all" type="text/css">@import "../../css/longView.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
var preview=false;
var type = "generic";
$(document).ready(function()
{
	$('#preview').click(function(){
		var startDate = $('#invoiceStartDate').val();
		var endDate = $('#invoiceEndDate').val();
		var projectId = $('#projectId').val();
		var comment = $('#invoiceComment').val();
		var materialId = $('#materialId').val();
		var itemId = $('#itemId').val();
		var options = ""
		if(type == "item" && itemId != 0)options = "&itemId=" + itemId; 
		if(type == "material" && materialId != 0)options = "&materialId=" + materialId; 
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'previewInvoice.php?startDate='+startDate+'&endDate='+endDate+'&projectId='+projectId+'&comment='+escape(comment)+options
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	
	$('#projectId').change(function() {
		var project = this.value;
		getItems(project);
		getMaterials(project);
	});
	
	$('#generalInvoiceType').change(function() {
		if($(this).attr("checked")) {
			$('#materialId').val(0).hide();
			$('#itemId').val(0).hide();
		}
	});
	
	$('#itemInvoiceType').change(function() {
		if($(this).attr("checked")) {
			$('#materialId').val(0).hide();
			$('#itemId').show();
			type = "item"
		}	
	});
	
	$('#materialInvoiceType').change(function() {
		if($(this).attr("checked")) {
			$('#itemId').val(0).hide();
			$('#materialId').show();
			type = "material";
		}	
	});
	
	$('#invoiceStartDate').blur(function(){
		var startDate = this.value;
		if(startDate != ""){
			startDate = startDate.replace(/(\d+)\/(\d+)\/(\d+)/,'$3/$1/$2');
			d =new Date(startDate);
			d.setDate((d.getDate() - d.getDay())+6);
			
		}else{
			d = new Date();
		}
		year = d.getFullYear()+'';
		month=d.getMonth()+1+'';
		day = d.getDate()+'';
		if(month.length==1)month='0'+month;
		if(day.length==1)day='0'+day;
		$('#invoiceEndDate').val(month+'/'+day+'/'+year);
	});
	
	$('#materialId').hide();
	$('#itemId').hide();
	$('#generalInvoiceType').attr('checked','checked');
	
});

function getMaterials(projectId) {
	$.ajax({
		type: "GET",
		url: "../commonUtils/getMaterialsByProject.php",
		data: "projectId="+projectId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var materialHolder = $('#materialId');
			materialHolder.children().remove();
			materialHolder.append("<option value='0' >--Select material--</option>");
			jQuery.each(obj, function(i,o){
				materialHolder.append("<option value='" + o.materialId + "' >" + o.materialName + "</option>");
			});
		},
		async: false
	});
}

function getItems(projectId) {
	$.ajax({
		type: "GET",
		url: "../commonUtils/getItemsByProject.php",
		data: "projectId="+projectId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var itemHolder = $('#itemId');
			itemHolder.children().remove();
			itemHolder.append("<option value='0' >--Select Item--</option>");
			jQuery.each(obj, function(i,o){
				itemHolder.append("<option value='" + o.itemId + "' >" + o.materialName + " @ " + o.itemMaterialPrice + " to " + o.itemDisplayTo + "</option>");
			});
		},
		async: false
	});
}

function getProjects(customer){
	$.ajax({
		type: "GET",
		url: "getProjects.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#projectId');
			material.children().remove();
			material.append("<option value='0' >--Select Project--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}
function validateForm(){
	
	if(!preview){
		if(!confirm("Are you sure you don't want to see the preview?")){
			return false;
		}
	}
	
	if(document.getElementById('projectId').selectedIndex==0 ){
		alert("Please select a project for this invoice");
			document.formValidate.projectId.focus;
			return false;
	}
	
	if(document.getElementById('invoiceStartDate').value.length==0){
		alert("Please type a starting date");
			document.formValidate.invoiceStartDate.focus;
			return false;
	}
	
	if(document.getElementById('invoiceEndDate').value.length==0){
		alert("Please select and end date");
			document.formValidate.invoiceEndDate.focus;
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
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">New Invoice</th>
					</tr>
					<tr class='bg'>
						<td colspan='2'><strong>Project:</strong><span style="color:red;">*</span></td>
						<td><strong>Starting Date:</strong><span style="color:red;">*</span></td>
						<td><strong>End Date:</strong><span style="color:red;">*</span></td>
						<td><input type='button' size='10px' id='preview' name='prevBtn' value='Preview' /></td>
					</tr>
					<tr>
					<td colspan='2'>
						<?
						$queryTerm0 = "select * from customer order by customerName asc";
						$terms0 = mysql_query($queryTerm0,$conexion);
						$countTerms0= mysql_num_rows($terms0);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms0 > 0)
						{
							
							if(!isset($_GET['i']))
							{
								echo "<option selected='selected'>--Select Customer--</option>";
								while($term0=mysql_fetch_assoc($terms0))
								{
									echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
							else
							{	
								while($term0=mysql_fetch_assoc($terms0))
								{
									if($_GET['i']==$term0['customerId'])
										echo "<option selected='selected' value='{$term0['customerId']}'>{$term0['customerName']}</option>";
									else
										echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
						}
								else
						{
							echo "<option selected='selected'>There are no customers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						<span style="color:red;">*</span><br/>

						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
						<span style="color:red;">*</span>
						</td>
						<td><input type='text' size='10px' id='invoiceStartDate' name='invoiceStartDate' /></td>
						<td><input type='text' size='10px' id='invoiceEndDate' name='invoiceEndDate' /></td>
						<td><input type='submit' size='10px' id='submit' name='subBtn' value='Submit' /></td>
					</tr>
					<tr>
						<td><strong>Specific Invoice:</strong></td>
						<td><strong>Type:</strong></td>
						<td colspan='3'><strong>Comment:</strong></td>
					</tr>
					<tr>
						<td>
							<input type='radio' name='type' value='general' id='generalInvoiceType' checked ><label for='generalInvoiceType'>All Tickets</label><br/>
							<input type='radio' name='type' value='item' id='itemInvoiceType' ><label for='itemInvoiceType'>By Item</label><br/>
							<input type='radio' name='type' value='material' id='materialInvoiceType' ><label for='materialInvoiceType'>By Material</label><br/>
						</td>
						<td>
							<select name='materialId' id='materialId' style='font-family:verdana;font-size:8pt'>
								<option selected='selected' value='0' >--Select Material--</option>
							</select>
							
							<select name='itemId' id='itemId' style='font-family:verdana;font-size:8pt'>
								<option selected='selected' value='0' >--Select Item--</option>
							</select>
						</td>
						<td colspan='3'><textarea id='invoiceComment' name='invoiceComment' cols='42' rows='2' ></textarea></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<div class='iframes' id='previewFrame' >
			</div>
			<?
			/*
			$queryContacts = "select * from item order by itemId desc limit 5";
			$terms = mysql_query($queryContacts,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Item</th>
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
						<td class='first style2'>JOB &nbsp;&nbsp;".$term['projectId']."&nbsp;&nbsp; ITEM &nbsp;&nbsp;".$term['itemNumber']."</td>
						<td><a href='/trucking/php/view/viewItem.php?i=".$term['itemId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editItem.php?i=".$term['itemId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete item #".$term['itemNumber']."?');\" href='deleteItem.php?i=".$term['itemId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
			}*/
			?>
			<?
			$queryLastInvoices = "Select * from invoice join project using (projectId) order by invoiceId desc limit 10";
			$lastInvoices = mysql_query($queryLastInvoices,$conexion);
			$numInvoices = mysql_num_rows($lastInvoices);
			if($numInvoices>0){
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Id</th>
						<th>For</th>
						<th>Creation Date</th>
						<th colspan='2' class='last'>Date Range</th>
					</tr>";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($lastInvoices))
				{
					if($colorFlag)
					{
						echo "<tr>";
						$colorFlag=!$colorFlag;
					}
					else
					{
						echo "<tr class='bg'>";
						$colorFlag=!$colorFlag;
					}
					echo "
						<td class='first style2'>INVOICE &nbsp;&nbsp;".$term['invoiceId']."</td>
						<td>".$term['projectName']."</td>
						<td>".$term['invoiceDate']."</td>
						<td>".$term['invoiceStartDate']."</td>
						<td>".$term['invoiceEndDate']."</td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
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
