<?php
include_once '../conexion.php';
include_once '../commons.php';

$projectId = $_GET['projectId'];
if(isset($_GET['projectId'])) {
	$projectInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM project WHERE projectId = $projectId", $conexion));
	$customerId = $projectInfo['customerId'];
	$projectAddressId = $projectInfo['addressId'];
}
?>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">
$(document).ready(function(){
	alert("hola");
	
	$('#holabutton').click(function(){
		alert("butotn");
	});
});
</script>
<style media="all" type="text/css">@import "../../css/popups.css";</style>
<div id='formDiv' class='table'>
	<form id="formValidate" name="formValidate" method="POST" action="submitItemProposal.php" onsubmit="return validateForm();" >
		<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
		<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
		<table class="listing form" cellpadding="0" cellspacing="0">
			<tr>
				<th class="full" colspan="2">New Item Proposal</th>
			</tr>
			<tr class='bg'>
				<td class='first'><strong>Customer:</strong></td>
				<td class='last'>
				<select name='customer' id='customer' style='font-family:verdana;font-size:8pt'>
					<option value=''>--Select Customer--</option>
					<?
					if(isset($_GET['customerId'])) {
						$customerInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM customer WHERE customerId = $customerId", $conexion));
						echo "<option value='".$supplier['supplierId']."'>".$supplier['supplierName']."</option>";
					} else {
						$suppliers = mysql_query("SELECT * FROM supplier ORDER BY supplierName desc", $conexion);
						while($supplier = mysql_fetch_assoc($suppliers)) {
							echo "<option value='".$supplier['supplierId']."'>".$supplier['supplierName']."</option>";
						}
					}
					?>
				</select>
				</td>
			</tr>
			<tr>
				<td class='first'><strong>Project:</strong></td>
				<td class='last'></td>
			</tr>
			<tr class='bg'>
				<td class='first'><strong>Supplier:</strong></td>
				<td class='last'>
				<select name='supplier' id='supplier' style='font-family:verdana;font-size:8pt'>
					<option value=''>--Select Supplier--</option>
					<?
					if(isset($_GET['supplierId'])) {
						
					} else {
						$suppliers = mysql_query("SELECT * FROM supplier ORDER BY supplierName desc", $conexion);
						while($supplier = mysql_fetch_assoc($suppliers)) {
							echo "<option value='".$supplier['supplierId']."'>".$supplier['supplierName']."</option>";
						}
					}
					?>
				</select>
				</td>
			</tr>
			<tr>
				<td class='first'><strong>Material:</strong></td>
				<td class='last'>
				<select name='supplier' id='supplier' style='font-family:verdana;font-size:8pt'>
					<option value=''>--Select Material--</option>
					<?
					if(isset($_GET['supplierId'])) {
						
					} else {
						$suppliers = mysql_query("SELECT * FROM suppliers ORDER BY supplierName desc", $conexion);
						while($supplier = mysql_fetch_assoc($suppliers)) {
							echo "<option value='".$supplier['supplierId']."'>".$supplier['supplierName']."</option>";
						}
					}
					?>
				</select>
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
					<input type="text" class="text" id='toAddress' name='toAddress' value='<?if(isset($_GET['sA']))echo$_GET['sA'];?>'/>
					<input type='hidden' id='toAddressId' name='toAddressId' value='<?if(isset($_GET['sAI']))echo$_GET['sAI'];?>' />
				</td>
			</tr>
			<tr class="bg">
				<td class="first"><strong>Material Price:</strong><span style="color:red;">*</span></td>
				<td class="last"><input type="text" class="text" id='materialPrice' name='materialPrice' value='<?echo"$materialPrice"?>' /><label id='lastModified'></label></td>
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
				<td><button type='button'  value='Reset' id='holabutton'>Hola</button></td>
				<td><input type='reset'  value='Reset' ></td>
				<td><input type='submit' value='Submit' ></td>
				<a href='/trucking/php/popUps/newItemProposalByProject.php'>link</a>
			</tr>
		</table>
	</form>
</div>
<?
$itemProposals = mysql_query("SELECT * FROM item_proposal WHERE projectId = $projectId", $conexion);
if(mysql_num_rows($itemProposals) > 0) {
?>
	<div id='last' class='table'>
		
	</div>
<?
} else {
?>
	<div id='last'>
	
	</div>
<?
}
?>
