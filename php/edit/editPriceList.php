<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Price List";
$description = "Edit the prices set for a supplier. Double click the price you want to change and input the new value, afterwards just click outside the textbox or press the TAB key. To delete a material from the supplier list click on the delete icon at the end of each row. To add a new material select it from the dropdown box";

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
		supplier
	WHERE
		supplier.supplierId=".$_GET['i'];
$Last = mysql_query($queryLast,$conexion);
//echo $queryLast;
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
	var supplier = <? if(isset($lastVal['supplierId']))echo $lastVal['supplierId'];else echo "0";?>;
	var id="";
	
	$('table#tblMaterialList td img').click(function()
	{
		var element = $(this);
		var name = element.attr("name");
		//console.log("delete");
		$.ajax({
				type: "GET",
				url: "deletePrice.php",
				data: "supplierId="+supplier+"&materialId="+name,
				success:function(data){
					var parent = $(element.closest("tr"));
					parent.remove();
				},
				async: false
			});
		
	});
	
	$('table#tblMaterialList td p').dblclick(function()
	{
		var element = $(this);
		var name = element.attr("name");
		var value = element.text();
		id = "material"+name;
		console.log(value);
		console.log(name);
		element.replaceWith("<input type='text' id='"+id+"'  name='"+name+"' value='"+value+"' />");
		$('#'+id).focus(
			function(){
				$(this).select();
			}
		);
		$('#'+id).focus();
		$('#'+id).mouseup(
			function(e){
				e.preventDefault();
			}
		);
	});
	
	$('#newMaterial').change(function()
	{
		var element = $(this);
		var name = element.val();
		console.log(name);
		var materialName = $('#newMaterial option:selected').text();
		console.log(materialName);
		$.ajax({
				type: "GET",
				url: "setPrice.php",
				data: "supplierId="+supplier+"&materialId="+name,
				success:function(data){
					console.log(data);
					var jsonObj = JSON.parse(data);
					console.log(jsonObj);
					if(jsonObj.lastModified!=null)
					{
						console.log(jsonObj.lastModified);
						$('td[name=lastModified'+name+']').text(jsonObj.lastModified);
						$('p[name='+name+']').text(jsonObj.newPrice);
					}
				},
				async: false
			});
		id = "material"+name;
		var newRow = $("<tr><td>"+materialName+"</td><td><input type='text' id='"+id+"'  name='"+name+"' /></td><td name='lastModified"+name+"'></td><td><img name='"+name+"' src='/trucking/img/116.png' width='20px' /></td></tr>");
		console.log(element);
		var parent = $($(this).closest("tr"));
		console.log(parent);
		newRow.insertBefore(parent);
		$('#'+id).focus();
	});
	
	$('table#tblMaterialList td p').live('dblclick',function()
	{
		var element = $(this);
		var name = element.attr("name");
		var value = element.text();
		id = "material"+name;
		console.log(value);
		console.log(name);
		element.replaceWith("<input type='text' id='"+id+"'  name='"+name+"' value='"+value+"' />");
		$('#'+id).focus(
			function(){
				$(this).select();
			}
		);
		$('#'+id).focus();
		$('#'+id).mouseup(
			function(e){
				e.preventDefault();
			}
		);
	});
	
	$('#suppliers tr td').live('dblclick',function(){
			
			var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editPriceList.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
			
	});

	$('#vendorId').change(function(){getSuppliers();});
	$('#supplierName').keyup(function(){getSuppliers();});
	$('#supplierCity').keyup(function(){getSuppliers();});
	$('#brokerStatus').change(function(){getSuppliers();});
	
	$(':text').live('blur',function(){
			console.log("blur");
			var element = $(this);
			console.log(element);
			var name = element.attr("name");
			console.log(name);
			var value = element.attr("value");
			console.log(value);
			element.replaceWith("<p name="+name+" ></p>");
			$.ajax({
				type: "GET",
				url: "updatePrice.php",
				data: "supplierId="+supplier+"&materialId="+name+"&price="+value,
				success:function(data){
					console.log(data);
					var jsonObj = JSON.parse(data);
					console.log(jsonObj);
					if(jsonObj.lastModified!=null)
					{
						console.log(jsonObj.lastModified);
						$('td[name=lastModified'+name+']').text(jsonObj.lastModified);
						$('p[name='+name+']').text(jsonObj.newPrice);
					}
				},
				async: false
			});
			
	});
});

function getSuppliers(){
	
	var vendorId=$('#vendorId').val();
	var supplierName=$('#supplierName').val();
	var supplierCity=$('#supplierCity').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getEditSuppliers.php",
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

	if(document.getElementById('supplierName').value.length==0){
		alert("Please type a name for the supplier");
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
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='tblMaterialList' >
					<tr>
						<th class="full" colspan="4">Edit Supplier Price List</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Supplier:</strong></td>
						<td class="last" colspan='3'><?if(isset($lastVal['supplierName']))echo$lastVal['supplierName'];?></td>
					</tr>
					<?
					$queryMaterial = 
					"SELECT 
						*
					FROM
						suppliermaterial
					JOIN
						material using (materialId)
					WHERE
						supplierId=".$_GET['i'];
					echo "
					<tr>
						<th>Material</th>
						<th>Price</th>
						<th>Last Modified</th>
						<th></th>
					</tr>
					";	
					$materials = mysql_query($queryMaterial, $conexion);
					while($material = mysql_fetch_assoc($materials))
					{
							echo "<tr >";
						echo "<td>".$material['materialName']."</td>";
						//echo "<td><input type='text' name='' id='' value='".$material['supplierMaterialPrice']."'><img /></td>";
						echo "<td><p name='".$material['materialId']."'>".decimalPad($material['supplierMaterialPrice'])."</p></td>";
						echo "<td name='lastModified".$material['materialId']."' >".to_MDY($material['supplierMaterialLastModified'])."</td><td><img name='".$material['materialId']."' src='/trucking/img/116.png' width='20px' /></td>";
						echo "</tr>";
					}
					
					$queryNewMaterial = "SELECT * FROM material";
					$newMaterials = mysql_query($queryNewMaterial,$conexion);
					echo "<tr>";
					echo "<td><select id='newMaterial' name='newMaterial' style='font-family:verdana;font-size:8pt'><option selected='selected'>-- New Material --</option>";
					while($newMaterial=mysql_fetch_assoc($newMaterials))
					{
						echo "<option value='{$newMaterial['materialId']}'>{$newMaterial['materialName']}</option>";
					}
					echo "</select></td>";
					echo "<td colspan='3'></td>";
					echo "</tr>";
					
					?>
					
				</table>
				<!--<table>
				<tr>
				<td><input type='reset'  value='Reset' ></td>
				<td><input type='submit' value='Submit' ></td>
				</tr>
				</table>-->
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
							$vendors = mysql_query("select * from vendor",$conexion);
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
				$suppliers = mysql_query("select * from supplier JOIN address using (addressId)",$conexion);
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
