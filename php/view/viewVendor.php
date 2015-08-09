<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Vendor";

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
		vendor
	JOIN address using (addressId)
	WHERE
		vendorId=".$_GET['i'];
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);

$querySuppliers =
"
SELECT
	*
FROM
	supplier
JOIN address using (addressId)
WHERE
	vendorId=".$_GET['i'];
	
	$suppliers = mysql_query($querySuppliers,$conexion);
$numSuppliers = mysql_num_rows($suppliers);
##########################################
}

$queryVendors=
"
SELECT
	*
FROM
	vendor
ORDER BY vendorName
";

$vendors = mysql_query($queryVendors,$conexion);
$numVendors = mysql_num_rows($vendors);



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
<?//echo $googleMapsScript;?>
<?echo $gMapsV3;?>
<script type="text/javascript">	
var preview=false;
var patternLetters = new RegExp(/vendor/);
patternLetters.compile(patternLetters);

var patternDate = new RegExp(/(\d+)\/(\d+)\/(\d+)/);
patternDate.compile(patternDate);

var centerLat = 41.911098233333;
var centerLong = -87.640749033333;
var centerDeep = 13;

var latitudes;
var longitudes;
var jIsSet=false;

var markersArray = [];

var map;

$(document).ready(function()
{
	$('.delete').live('click',function(){
		initializeMap();
		return confirm('Are you sure you want to delete this Vendor?');
	});
	$('.deleteAnchor').live('click',function(){
		return confirm('Are you sure you want to delete this Supplier?');
	});
	$('#vendors tr td').live('dblclick',function(){
			var vendorId=$(this).attr('id');
			if(vendorId!=undefined){
				vendorId=vendorId.replace(patternLetters,'');
				getVendor(vendorId);
			}
			
	});

});

function getSuppliers(){
		$.ajax({
			type: "GET",
			url: "getAllSuppliers.php",
			data: "",
			success: function(data){
				var obj = jQuery.parseJSON(data);
				console.log(obj);
				if(obj.point!=null){
					deleteOverLays();
					var i=0;
					for(i=0;i<obj.point.id.length;i++){
						newMarker(obj.point.addressLat[i],obj.point.addressLng[i]);
					}
				}
			},
			async: true
		});
}
function getVendor(vendorId){
	$.ajax({
		type: "GET",
		url: "getVendor.php",
		data: "vendorId="+vendorId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				var count = $('.top-bar > a').length;
					if(count>1){
						$('.top-bar > a:last').remove();
						$('.top-bar > a:last').remove();
					}
				if(obj.table!=null){
					if($('#viewVendor').length==0){
						$('.top-bar').after(obj.table);
					}else{
						$('#viewVendor').replaceWith(obj.table);
					}
				}
				if(obj.tableSuppliers!=null){
					if($('#suppliers').length==0){
						$('#viewVendor').after(obj.tableSuppliers);
					}else{
						$('#suppliers').replaceWith(obj.tableSuppliers);
					}
				}
				if(obj.edit!=null){
					
					$('.top-bar').append(obj.edit);
				}
				var lat=obj.cLat;
				var lng=obj.cLong;
				var deep=obj.cDeep;
				//initializeMap(lat,lng,deep);
				if(obj.point!=null){
					deleteOverLays();
					var i=0;
					for(i=0;i<obj.point.id.length;i++){
						//console.log(obj.point.addressLat[i]);
						//console.log(obj.point.addressLng[i]);
						//map.addOverlay(new GMarker(new GLatLng(obj.point.addressLat[i],obj.point.addressLng[i])));
						newMarker(obj.point.addressLat[i],obj.point.addressLng[i],obj.point.id[i],obj.point.supplierName[i]);
					}
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function deleteOverLays(){
		if(markersArray){
			for(i in markersArray){
				markersArray[i].setMap(null);
			}
			markersArray.length = 0;
		}
}

function newMarker(lat,lng,id,name){
	var mLatLng = new google.maps.LatLng(lat,lng);
		var marker = new google.maps.Marker({
			position: mLatLng,
			title: name
		});
		
		marker.setMap(map);
		markersArray.push(marker);
		
			google.maps.event.addListener(marker, 'click', function() {
  window.location.href = '/trucking/php/view/viewSupplier.php?i='+id;  
});
}

function addStartingPoints(){
	if(jIsSet){
		var i=0;
		for(i=0;i<latitudes.length;i++){
			newMarker(latitudes[i],longitudes[i]);
		}
	}
}


/*
ONLY FOR GOOGLE MAPS API VERSION 2
function initializeMap(lat,lng,deep){
	if(GBrowserIsCompatible()){
		map = new GMap2(document.getElementById("mapCanvas"));
		map.setCenter(new GLatLng(lat, lng), deep);
		map.setUIToDefault();
	}
}
*/
function initializeMap(lat,lng,deep){
		var latlng = new google.maps.LatLng(lat,lng);
		var myOptions ={
			zoom: deep,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("mapCanvas"),myOptions);
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
<body onLoad='initializeMap(centerLat,centerLong,centerDeep);addStartingPoints();'>
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
				
				<a href="#" class="vendor"></a>
				<?
				if(isset($_GET['i']))
				{
				?>
				<a href="deleteVendor.php?i=<?echo$_GET['i'];?>" class="delete"></a>
				<a href="/trucking/php/edit/editVendor.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
				
				<?	
				}
				?>
			</div><br />
			
			<?
		if(isset($_GET['i']))
		{
		?>
			<div class="table" id='viewVendor'>
			<form id="formValidate" name="formValidate" method="POST" action="submitEditCustomer.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Vendor Information</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong></td>
						<td class="last"> <?if(isset($lastVal['vendorName']))echo$lastVal['vendorName'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Additional Information:</strong></td>
						<td class="last"><?if(isset($lastVal['vendorInfo']))echo $lastVal['vendorInfo'];?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Comments:</strong></td>
						<td class="last"> <?if(isset($lastVal['vendorComment']))echo$lastVal['vendorComment'];?></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Phone:</strong></td>
						<td class="last"> <?if(isset($lastVal['vendorTel']))echo showPhoneNumber($lastVal['vendorTel']);?> </td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Fax:</strong></td>
						<td class="last"> <?if(isset($lastVal['vendorFax']))echo showPhoneNumber($lastVal['vendorFax']);?></td>
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
					
				</table>
				<table>
				<tr>
					<td>
						<a href="/trucking/php/new/newSupplier.php?i=<?echo$lastVal['vendorId'];?>" >
							<strong>Add a supplier</strong>
							<img src='/trucking/img/95.png' width='20' height='20' />
						</a>
					</td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
$centerLat=0;
$centerLong=0;
$centerDeep=11;
$average=0;
			$queryVendors = "select * from supplier JOIN address using (addressId) where vendorId=".$lastVal['vendorId'];
			$terms = mysql_query($queryVendors,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table' id='suppliers'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Suppliers</th>
						<th>Address</th>
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
						<td class='first style2'>".$term['supplierName']."</td>
						<td>".$term['addressLine1']."</td>
						<td><a href='/trucking/php/view/viewSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a class='deleteAnchor' href='deleteSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
					if($term['addressLat']!=0 && $term['addressLong']!=0)
					{
						$arrayLat[]=$term['addressLat'];
						$arrayLng[]=$term['addressLong'];
						$centerLat+=$term['addressLat'];
						$centerLong+=$term['addressLong'];
						$average++;
					}
				}
				
				if($average>0){
				$centerLat=$centerLat/$average;	
				$centerLong=$centerLong/$average;	
				}
				
				$jArrayLat = "['".implode("','",$arrayLat)."']";
				$jArrayLng = "['".implode("','",$arrayLng)."']";
				echo "</table>";
				echo "</div>";
				echo "<script type='text/javascript'>latitudes=$jArrayLat;longitudes=$jArrayLng;jIsSet=true;</script>";
				echo "<script type='text/javascript'>centerLat=$centerLat;centerLong=$centerLong;centerDeep=$centerDeep;</Script>";
			}
			?>
		<?
		}
		?>	
		
		<div id='mapCanvas' class='mapCanvas'>
		</div>
			<div class='table'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='vendors'>
					<tr>
						<th class="full"  colspan='2'>Vendors</th>
					</tr>
					<tr>
						<th>Name</th>
						<th>Name</th>
					</tr>
					<tbody>
					<?
					$actual=0;
					$colorFlag=true;
					$tdClass="";
					if($numVendors>0)
					{
							while($vendor=mysql_fetch_assoc($vendors))
							{
								
									switch($actual){
										case 0:
										echo "<tr>";
										echo "<td ".$tdClass." id='vendor".$vendor['vendorId']."' >".$vendor['vendorName']."</td>";
										$actual++;
										break;
										case 1:
										echo "<td ".$tdClass." id='vendor".$vendor['vendorId']."' >".$vendor['vendorName']."</td>";
										echo "</tr>";
										$actual=0;
										break;
									}
									$colorFlag=!$colorFlag;
									if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}
							switch($actual){
								case 0:break;
								case 1:echo"<td ".$tdClass."></td></tr>";break;
							}
					}
					?>
					</tbody>
				</table>
			
			</div>
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
