<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Vendor Invoice";
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
var patternLetters = new RegExp(/ticket/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{	
	$('.add-ticket').live('click',function(){
		var id = $(this).closest("tr").attr('id').replace(patternLetters,'');
		console.log(id);
		if(id!=undefined){
			var invNumber = $('#invoiceNum').val();
			var suppId = $('#supplierId').val();
			if(invNumber!=""){
				if(suppId!=0){
					console.log("here");
					submitTicket(suppId,id,invNumber);
				}else{
					alert("Select a supplier");
				}
			}else{
				alert("Type an invoice number first");
			}
		}
	});
	
	$('#searchButton').click(function(){
		getTickets();
	});
	
	$('#vendorId').change(function(){
		var vendor = this.value;
		getSuppliers(vendor);
	});
	
	$('#vendorInvoiceTickets').click(function () {
		//getTickets();
	});

	$('#supplierId').change(function(){
		//getTickets();
	});
	
	$('#startDate').blur(function(){
		//getTickets();
	});
	
	$('#endDate').blur(function(){
		//getTickets();
	});
});

function submitTicket(supplierId,ticketId,invoiceNum){
	//test replace
	
	$.ajax({
		type: "GET",
		url: "submitSupplierTicket.php",
		data: "supplierId="+supplierId+"&ticketId="+ticketId+"&invoiceNum="+invoiceNum,
		success:function(data){
			var obj=jQuery.parseJSON(data);
				$('#ticket'+obj.ticketId+' img.add-ticket').replaceWith("<label class='rm-ticket'>"+obj.invoiceId+"</label>");
				console.log(obj.insertQuery);
		},
		async: false
	});
}

function getSuppliers(vendor){
	$.ajax({
		type: "GET",
		url: "getSuppliers.php",
		data: "vendorId="+vendor,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var supplier=$('#supplierId');
			supplier.children().remove();
			supplier.append("<option value='0' >--Select supplier--</option>");
			jQuery.each(obj, function(i,val){
				supplier.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
	//getTickets();
}

function getTickets(){
	var vendor = $('#vendorId').val();
	var supplier = $('#supplierId').val();
	var startDate = $('#startDate').val();
	var endDate = $('#endDate').val();
	var invoiced = $("input[name='inInvoice']:checked").val();
	var listed = $("input[name='listedLimit']:checked").val();
	$.ajax({
		type: "GET",
		url: "getSupplierTickets.php",
		data: "vendorId="+vendor+"&supplierId="+supplier+"&startDate="+startDate+"&endDate="+endDate+"&invoiced="+invoiced+"&listed="+listed,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#tickets > tbody:last').remove();
					$('#tickets').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
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
						<th class="full" colspan="5">New Invoice</th>
					</tr>
					<tr class='bg'>
						<td><strong>Vendor:</strong><span style="color:red;">*</span></td>
						<td><strong>Supplier:</strong><span style="color:red;">*</span></td>
						<td><strong>Invoice #:</strong><span style="color:red;">*</span></td>
						<td><strong>Amount:</strong><span style="color:red;">*</span></td>
						<td></td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from vendor order by vendorName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='vendorId' id='vendorId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Vendor--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
										echo "<option selected='selected' value='{$term['vendorId']}'>{$term['vendorName']}</option>";
									else
										echo "<option value='{$term['vendorId']}'>{$term['vendorName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no vendors in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td>
						<?
						$queryTerm = "select * from supplier order by supplierName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='supplierId' id='supplierId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Supplier--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
										echo "<option selected='selected' value='{$term['supplierId']}'>{$term['supplierName']}</option>";
									else
										echo "<option value='{$term['supplierId']}'>{$term['supplierName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no suppliers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='invoiceNum' name='invoiceNum' /></td>
						<td><input type='text' size='10px' id='invoiceAmount' name='invoiceAmount' /></td>
						<td></td>
					</tr>
					<tr class='bg'>
						<td>Start date:</td>
						<td><input type='text' size='10px' id='startDate' name='startDate' /></td>
						<td>End date:</td>
						<td><input type='text' size='10px' id='endDate' name='endDate' /></td>
						<td></td>
					</tr>
					<tr>
						<td ><textarea id='invoiceComment' name='invoiceComment' cols='42' rows='2' ></textarea></td>
						
						<td>
							<input type='radio' name='listedLimit' value='20' id='twenty' checked ><label for='twenty'>20</label><br/>
							<input type='radio' name='listedLimit' value='50' id='fifty' ><label for='fifty'>50</label><br/>
							<input type='radio' name='listedLimit' value='100' id='hundred' ><label for='hundred'>100</label><br/>
							<input type='radio' name='listedLimit' value='all' id='allListed' ><label for='allListed'>All</label><br/>
						</td>
						<td>
							<input type='radio' name='inInvoice' value='no' id='noInInvoice' checked ><label for='noInInvoice'>Not in invoice</label><br/>
							<input type='radio' name='inInvoice' value='yes' id='yesInInvoice' ><label for='yesInInvoice'>In invoice</label><br/>
							<input type='radio' name='inInvoice' value='all' id='allInInvoice' ><label for='allInInvoice'>All</label><br/>
						</td>
						<td><button type='button' id='searchButton' >Search</button></td>
						<td></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<div class='iframes' id='previewFrame' >
			</div>
			<?
			
			$queryTickets = "
				SELECT
					*
				FROM
					ticket
				ORDER BY
					ticketNumber ASC
				LIMIT 45";
			$terms = mysql_query($queryTickets,$conexion);
			
			
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing form' cellpadding='0' cellspacing='0' id='tickets'>";
				echo "<thead><tr>
						<th class='first'>Job</th>
						<th>Item</th>
						<th>Date</th>
						<th>Material</th>
						<th>From</th>
						<th>MFI</th>
						<th>Ticket</th>
						<th>Material</th>
						<th colspan='2'>Customer</th>
						<th colspan='2'>Broker</th>
						<th class='last'>Add</th>
					</tr>
					</thead>
					<tbody>";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($terms))
				{
				$queryItem = "SELECT * FROM item where itemId=".$term['itemId'];
				$items = mysql_query($queryItem,$conexion);$item = mysql_fetch_assoc($items);
				$queryProject = "SELECT * FROM project where projectId=".$item['projectId'];
				$projects = mysql_query($queryProject,$conexion);$project = mysql_fetch_assoc($projects);
				$queryMaterial = "SELECT * FROM material where materialId=".$item['materialId'];
				$materials = mysql_query($queryMaterial,$conexion);$material = mysql_fetch_assoc($materials);
				
				$querySup = "SELECT * FROM supplierinvoiceticket WHERE ticketId = ".$term['ticketId'];
				$ticketInvoices = mysql_query($querySup,$conexion);
				if(mysql_num_rows($ticketInvoices)==0)$term['supplierInvoiceNumber']=0;
				else{
					$ticketInvoice=mysql_fetch_assoc($ticketInvoices);
					$supplierQuery = "SELECT * FROM supplierinvoice WHERE supplierInvoiceId=".$ticketInvoice['supplierInvoiceId'];
					$supplierInvoices = mysql_query($supplierQuery,$conexion);
					$supplierInvoice = mysql_fetch_assoc($supplierInvoices);
					$term['supplierInvoiceNumber']=$supplierInvoice['supplierInvoiceNumber'];
				}
				
				//LEFT JOIN (select supplierInvoiceId,ticketId, supplierInvoiceNumber from supplierinvoice JOIN supplierinvoiceticket using (supplierInvoiceId)) as SI using (ticketId)
					$colorFlag=!$colorFlag;
					if($colorFlag)
					{
						echo "<tr id='ticket".$term['ticketId']."' >";
					}
					else
					{
						echo "<tr id='ticket".$term['ticketId']."' class='bg'>";
					}
					echo "
						<td class='first style2'>".$project['projectName']."</td>
						<td class='first style2'>".$item['itemNumber']."</td>
						<td class='first style2'>".to_MDY($term['ticketDate'])."</td>
						<td class='first style2'>".$material['materialName']."</td>
						<td class='first style2'>".$item['itemDisplayFrom']."</td>
						<td class='first style2'>".$term['ticketMfi']."</td>
						<td class='first style2'>".$term['ticketNumber']."</td>
						<td class='number'>$".decimalPad($item['itemMaterialPrice'])."</td>
						<td class='number'>".decimalPad($term['ticketAmount'])."</td>
						<td class='number'>$".decimalPad($item['itemCustomerCost'])."</td>
						<td class='number'>".decimalPad($term['ticketBrokerAmount'])."</td>
						<td class='number'>$".decimalPad($item['itemBrokerCost'])."</td>
						<td>".($term['supplierInvoiceNumber']==0?"<img src='/trucking/img/23.png' class='add-ticket' width='20px'>":"<label class='rm-ticket'>".$term['supplierInvoiceNumber']."</label>")."</td>
							";
					echo "</tr>";
				}
				
				
				echo "</tbody></table>";
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
