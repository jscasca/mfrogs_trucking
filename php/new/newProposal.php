<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Proposal";
$description = "Add a new item. Items have to be assigned to a project and a supplier. Addresses used on the item refer to the project address and the supplier address. Materials have to be provided by the supplier and by default the material price is the supplier price for the choosen material. Values marked with <span style='color:red;'>*</span> are mandatory.";

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

###############Next Autoincrement#########
$next=1;
if(isset($_GET['i']))
{
$getNextNumber="select * from fakeitem where projectId=".$_GET['i']." order by itemNumber desc limit 1";
$nextNumber=mysql_query($getNextNumber,$conexion);
if(mysql_num_rows!=0){$row=mysql_fetch_assoc($nextNumber);$next=$row['itemNumber']+1;}
}
##########################################

###############Project's Address#########
if(isset($_GET['i']))
{
$getAddress="select addressId,addressLine1 from fakeproject JOIN address using (addressId) where fakeprojectId=".$_GET['i'];
$address=mysql_query($getAddress,$conexion);
$addressRow=mysql_fetch_assoc($address);
}
##########################################

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
$(document).ready(function()
{
	$('#supplierId').change(function() {
		var supplier=this.value;
		getMaterials(supplier);
		getSupplierAddress(supplier);
	});
	
	$('#fakeprojectId').change(function() {
		var project=this.value;
		getItemNumber(project);
	});
	
	$('#material').change(function() {
		var material=this.value;
		var supplier=$('#supplierId').val();
		getMaterialPrice(supplier,material);
	});
	
	$('#reverse').click(function() {
		var aux=$('#fromAddress').val();
		$('#fromAddress').val($('#toAddress').val());
		$('#toAddress').val(aux);
		var aux=$('#fromAddressId').val();
		$('#fromAddressId').val($('#toAddressId').val());
		$('#toAddressId').val(aux);
	});
});
function getMaterials(supplier){
	$.ajax({
		type: "GET",
		url: "getMaterials.php",
		data: "supplierId="+supplier,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#material');
			material.children().remove();
			material.append("<option value='0' >--Select material--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}
function getSupplierAddress(supplier){
	$.ajax({
		type: "GET",
		url: "getSupplierAddress.php",
		data: "supplierId="+supplier,
		success:function(data){
			var jsonObj=jQuery.parseJSON(data);
			if(jsonObj!=null){
				if(jsonObj.addressLine1!=null)
					$('#toAddress').val(jsonObj.addressLine1);
				else
					$('#toAddress').val('');
				if(jsonObj.addressId!=null)
					$('#toAddressId').val(jsonObj.addressId);
				else
					$('#toAddressId').val('');
					
			}
		},
		async: true
	});
}
function getItemNumber(project){
	$.ajax({
		type: "GET",
		url: "getFakeItemNumber.php",
		data: "fakeprojectId="+project,
		success:function(data){
			var jsonObj=jQuery.parseJSON(data);
			if(jsonObj!=null){
				if(jsonObj.itemNumber!=null){
					$('#itemNumber').val(jsonObj.itemNumber);
					$('#itemNumberH').val(jsonObj.itemNumber);
				}
				if(jsonObj.addressLine1!=null)
					$('#fromAddress').val(jsonObj.addressLine1);
				else
					$('#fromAddress').val('');
				if(jsonObj.addressId!=null)
					$('#fromAddressId').val(jsonObj.addressId);
				else
					$('#fromAddressId').val('');
			}else{
				$('#fromAddressId').val('');
				$('#fromAddress').val('');
				$('#itemNumber').val('1');
			}
		},
		async: false
	});
}
function getMaterialPrice(supplier,material){
	$.ajax({
		type: "GET",
		url: "getPrice.php",
		data: "supplierId="+supplier+"&materialId="+material,
		success:function(data){
			var jsonObj=jQuery.parseJSON(data);
			if(jsonObj!=null){
				if(jsonObj.price!=null)
					$('#materialPrice').val(jsonObj.price);
				else
					$('#materialPrice').val('');
				if(jsonObj.lastModified!=null)
					$('#lastModified').text(jsonObj.lastModified);
				else
					$('#lastModified').text('');
			}else{
				$('#materialPrice').val('');
				$('#lastModified').text('');
			}
		},
		async: false
	});
}


function validateForm(){
	<? 
	if(!isset($_GET['i']))
	{
	?>
	if(document.getElementById('fakeprojectId').selectedIndex==0 ){
		alert("Please select a shellproject for this item");
			document.formValidate.fakeprojectId.focus;
			return false;
	}
	
	<?
	}
	?>
	if(document.getElementById('supplierId').selectedIndex==0 ){
		alert("Please select a supplier for this item");
			document.formValidate.supplierId.focus;
			return false;
	}
	
	if(document.getElementById('material').selectedIndex==0 ){
		alert("Please select a material for this item");
			document.formValidate.material.focus;
			return false;
	}
	
	if(document.getElementById('materialPrice').value.length==0){
		alert("Please type a material price");
			document.formValidate.materialPrice.focus;
			return false;
	}
	
	if(document.getElementById('itemClientCost').value.length==0){
		alert("Please type the client cost");
			document.formValidate.itemClientCost.focus;
			return false;
	}
	
	if(document.getElementById('itemCustomerCost').value.length==0){
		alert("Please type the customer cost");
			document.formValidate.itemCustomerCost.focus;
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
			<form id="formValidate" name="formValidate" method="POST" action="submitProposal.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Proposal</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Item Number:</strong><span style="color:red;">*</span></td>
						<td class="last">
							<input type="text" disabled value="<?echo$next;?>" id='itemNumber' name='itemNumber' />
							<input type="hidden" value="<?echo$next;?>" id='itemNumberH' name='itemNumber' />
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryTerm = "select * from fakeproject order by fakeprojectName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='fakeprojectId' id='fakeprojectId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms > 0)
						{
							
							if(!isset($_GET['p']))
							{
								echo "<option selected='selected'>--Select ShellProject--</option>";
								while($term=mysql_fetch_assoc($terms))
								{
									echo "<option value='{$term['fakeprojectId']}'>{$term['fakeprojectName']}</option>";
								}
							}
							else
							{	
								while($term=mysql_fetch_assoc($terms))
								{
									if($_GET['p']==$term['fakeprojectId'])
										echo "<option selected='selected' value='{$term['fakeprojectId']}'>{$term['fakeprojectName']}</option>";
									else
										echo "<option value='{$term['fakeprojectId']}'>{$term['fakeprojectName']}</option>";
								}
							}
						}
						else
						{
							echo "<option selected='selected'>There are no projects in the DataBase</option>";
							
						}
						echo "</select>";
						?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Supplier:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryState = "select * from supplier order by supplierName asc";
						$suppliers = mysql_query($queryState,$conexion);
						echo "<select name='supplierId' id='supplierId' style='font-family:verdana;font-size:8pt'>";
						if(isset($_GET['s'])){
							while($supplier=mysql_fetch_assoc($suppliers))
							{
								if($_GET['s']==$supplier['supplierId'])echo "<option selected='selected' value='{$supplier['supplierId']}'>{$supplier['supplierName']}</option>";
								else echo "<option value='{$supplier['supplierId']}'>{$supplier['supplierName']}</option>";
							}
						}else{
							echo "<option selected='selected' value='0'>--Select Supplier--</option>";
							while($supplier=mysql_fetch_assoc($suppliers))
							{
								echo "<option value='{$supplier['supplierId']}'>{$supplier['supplierName']}</option>";
							}
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Material:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						echo "<select name='material' id='material' style='font-family:verdana;font-size:8pt'>";
						if(isset($_GET['s'])){
							$queryMaterials = "SELECT
													*
												FROM
													suppliermaterial
												JOIN material using (materialId)
												WHERE
													supplierId = '".$_GET['s']."'
												ORDER BY
													materialName asc";
							$materialResult = mysql_query($queryMaterials,$conexion);
							if(isset($_GET['m'])){
								while($material = mysql_fetch_assoc($materialResult)){
									if($_GET['m']==$material['materialId'])echo "<option selected='selected' value='".$material['materialId']."' >".$material['materialName']."</option>";
									else echo "<option value='".$material['materialId']."' >".$material['materialName']."</option>";
								}
							}else{
								echo "<option selected='selected' value=''>--Select material--</option>";
								while($material=mysql_fetch_assoc($materialResult)){
									echo "<option value='".$material['materialId']."' >".$material['materialName']."</option>";
								}
							}
						}else{
							echo "<option selected='selected' value=''>--Select material--</option>";
						}
						echo "</select>";
						?>
						</td
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>From:</strong></td>
						<td class="last">
							<input type="text" class="text" id='fromAddress' name='fromAddress' value='<?if(isset($_GET['pA']))echo$_GET['pA'];?>' />
							<input type='hidden' id='fromAddressId' name='fromAddressId' value='<?if(isset($_GET['pAI']))echo$_GET['pAI'];?>' />
							<img id='reverse' src='/trucking/img/48.png' width='18px'/>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>To:</strong></td>
						<td class="last">
							<input type="text" class="text" id='toAddress' name='toAddress'  value='<?if(isset($_GET['sA']))echo$_GET['sA'];?>'/>
							<input type='hidden' id='toAddressId' name='toAddressId' value='<?if(isset($_GET['sAI']))echo$_GET['sAI'];?>' />
						</td>
					</tr>
					
					<tr class="bg">
						<td class="first"><strong>Material Price:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='materialPrice' name='materialPrice'  value='<?if(isset($_GET['mP']))echo$_GET['mP'];?>' /><label id='lastModified'></label></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Broker Cost:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='itemClientCost' name='itemClientCost'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Customer Cost:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='itemCustomerCost' name='itemCustomerCost' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Type:</strong></td>
						<td class="last">
							<input type="radio"  id='load' name='itemType' value='0' />
							<label for='load'>Loads</label>
							<input type="radio"  id='tons' name='itemType' value='1' />
							<label for='tons'>Tons</label>
							<input type="radio"  id='hours' name='itemType' value='2' />
							<label for='hours'>Hours</label>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Description:</strong></td>
						<td class="last"><textarea rows="2" cols="43" class="text" id='itemDescription' name='itemDescription' /></textarea></td>
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
			
			$queryContacts = "select * from fakeitem order by fakeitemId desc limit 5";
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
						<td class='first style2'>SHELLPROJECT &nbsp;&nbsp;".$term['fakeprojectId']."&nbsp;&nbsp; ITEM &nbsp;&nbsp;".$term['itemNumber']."</td>
						<td><a href='/trucking/php/view/viewProposal.php?i=".$term['itemId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editProposal.php?i=".$term['itemId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						";
					
						echo "<td class='last'><a onclick=\"return confirm('Are you sure you want to delete item #".$term['itemNumber']."?');\" href='deleteProposal.php?i=".$term['itemId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>";
					echo "</tr>";
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
