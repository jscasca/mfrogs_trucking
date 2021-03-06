<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Supplier";

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
###############Show Supplier###############
$queryLast =
	"SELECT 
		*
	FROM
		supplier
	JOIN
		address
	JOIN
		vendor
	ON
		address.addressId=supplier.addressId and
		vendor.vendorId = supplier.vendorId and
		supplierId=".$_GET['i'];
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
var patternLetters = new RegExp(/supplier/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('.delete').live('click',function(){
		return confirm('Are you sure you want to delete this supplier?');
	});
	$('#suppliers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewSupplier.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});

	$('#vendorId').change(function(){getSuppliers();});
	$('#supplierName').keyup(function(){getSuppliers();});
	$('#supplierCity').keyup(function(){getSuppliers();});
	$('#brokerStatus').change(function(){getSuppliers();});

});

function getSuppliers(){
	
	var vendorId=$('#vendorId').val();
	var supplierName=$('#supplierName').val();
	var supplierCity=$('#supplierCity').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewSuppliers.php",
		data: "vendorId="+vendorId+"&supplierName="+supplierName+"&supplierCity="+supplierCity,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#suppliers > tbody:last').remove();
					$('#suppliers').append(obj.table);
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
				
				<a href="#" class="supplier"></a>
				<a href="deleteSupplier.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editSupplier.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitSupplierCustomer.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Supplier Information</th>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Vendor Name:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['vendorName']))
						{
							echo $lastVal['vendorName'];
							echo "&nbsp;<a href='/trucking/php/view/viewVendor.php?i=".$lastVal['vendorId']."'><img src='/trucking/img/16.png' width='16' height='16' /></a>";
						}
						?> 
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong><span style="color:red;">*</span></td>
						<td class="last"> <?if(isset($lastVal['supplierName']))echo$lastVal['supplierName'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"> <?if(isset($lastVal['supplierTel']))echo showPhoneNumber($lastVal['supplierTel']);?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"> <?if(isset($lastVal['supplierFax']))echo showPhoneNumber($lastVal['supplierFax']);?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressLine1']))echo$lastVal['addressLine1'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressLine2']))echo$lastVal['addressLine2'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressCity']))echo$lastVal['addressCity'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						if(isset($lastVal['addressState']))echo$lastVal['addressState'];
						?>
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressZip'])&&$lastVal['addressZip']!=0)echo$lastVal['addressZip'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"> <?if(isset($lastVal['addressPOBox']))echo$lastVal['addressPOBox'];?></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Dump Time:</strong></td>
						<td class="last"> <?if(isset($lastVal['supplierDumptime']))echo$lastVal['supplierDumptime'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Additional Information:</strong></td>
						<td class="last"><?if(isset($lastVal['supplierInfo']))echo $lastVal['supplierInfo'];?> </td>
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
					<th class="full"  colspan='5'>Search Suppliers</th>
				</tr>
				<tr>
					<td>Vendor</td>
					<td>Name</td>
					<td>City</td>
				</tr>
				<tr>
					<td>
							<select name='vendorId' id='vendorId' style='font-family:verdana;font-size:8pt' >
							<option value='0'>--Select Vendor--</option>
							<?
							$vendors = mysql_query("select * from vendor order by vendorName",$conexion);
							while($vendor=mysql_fetch_assoc($vendors)){
								echo "<option value='".$vendor['vendorId']."' >".$vendor['vendorName']."</option>";
							}
							?>
							</select>
						</td>
					<td><input type='text' size='8px' id='supplierName' name='supplierName' /></td>
					<td><input type='text' size='8px' id='supplierCity' name='supplierCity' /></td>
				</tr>
				</table>
			</div>
			
						<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='suppliers'>
				<tr>
					<th class="full"  colspan='9'>SUPPLIERS</th>
				</tr>
				<tr>
					<th>Name</th>
					<th>Address</th>
					<th>Name</th>
					<th>Address</th>
				</tr>
				<tbody>
				<?
				$suppliers = mysql_query("select * from supplier JOIN address using (addressId) order by supplierName",$conexion);
				$numsuppliers=mysql_num_rows($suppliers);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numsuppliers>0)
				{
						while($supplier=mysql_fetch_assoc($suppliers))
						{
							if($actual){
								echo "<tr>";
								echo "<td ".$tdClass." id='supplier".$supplier['supplierId']."'>".$supplier['supplierName']."</td>";
								echo "<td ".$tdClass.">".$supplier['addressLine1']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								echo "<td ".$tdClass." id='supplier".$supplier['supplierId']."'>".$supplier['supplierName']."</td>";
								echo "<td ".$tdClass.">".$supplier['addressLine1']."</td>";
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
