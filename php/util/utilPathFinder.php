<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Util";
#################
$subtitle = "PathFinder";
$description = "The PathFinder utility helps you compare distances and prices from different suppliers to a job site. First select a customer. Choose between the customer projects or project sketches. Type the job dumptime to use in time calculations. Finally select the material. The pathfinder will show the route from each supplier providing the selected material to the job site";

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

//get the hourlyRate over 60 minutes

$results = mysql_query("select hourlyRate from stateinfo", $conexion);
$result = mysql_fetch_assoc($results);

$hourly = $result['hourlyRate'];
$perminute = $hourly / 60;

//$perminute = 10;

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
<?echo $gMapsV3;?>
<script type="text/javascript">	
var patternDigits = new RegExp(/[0-9]+/);
patternDigits.compile(patternDigits);

var patternLetters = new RegExp(/supplier/);
patternLetters.compile(patternLetters);
<?echo $jsColorArray;?>
var centerLat = 41.911098233333;
var centerLong = -87.640749033333;
var centerDeep = 13;

var perminute = <?echo $perminute;?>;

var markersArray = [];
var routesArray = [];

var directionsService = new google.maps.DirectionsService();
var directionsDisplay;
var map;

var lastSorted = 0;
var tableToSort = [];
var sortingUp = true;

var jobDumptime = 0;

var jobSite= new google.maps.Marker({
		position: new google.maps.LatLng(0,0),
		title: "Job"
	});
	jobSite.setMap(null);

$(document).ready(function()
{
	
	$('.sortable').live('click',function(){
		var index = $(this).attr('attributeToSort');
		console.log(index);
		sortTableByAttributeUp(index);
		/*var index = $(this).parent().children().index($(this));
		if(index == lastSorted){
			sortingUp = !sortingUp;
		}else{
			lastSorted = index;
			sortingUp = true;
		}
		sortTableBy(index);*/
	});
	
	$('#jobDumptime').change(function(){
		if(this.value != "")jobDumptime = this.value;
		else jobDumptime = 0;
	});
	
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	
	$('#projectId').change(function() {
		var project=this.value;
		getJobPosition(project);
	});
	
	$('#materialId').change(function() {
		var material=this.value;
		getSupplierAndPrice(material);
	});
	
	$('#pathType').change(function(){
		var type=this.value;
		var customer = $("#customerId option:selected").val();
		console.log("path type: "+type);
		getOptions(type,customer);
	});
	
	$(".toNew").live('click',function(){
		var type = $("#pathType option:selected").val();
		var project = $("#projectId option:selected").val();
		var material = $("#materialId option:selected").val();
		console.log(this);
		var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				console.log(id);
				window.location.replace("./prepareNewItem.php?type="+type+"&project="+project+"&supplier="+id+"&material="+material);
				//window.location.replace("./editDriver.php?i="+id);
				//alert(id);
				//getTicket(ticketId);
			}
	});
});

function sortTableByAttributeUp(attr) {
	console.log("sorting");
	var rows = $('#toSort tr:gt(0)');
	
	var size = rows.length;
	var aux;
	var sortedRows = new Array();
	
	for(i = 0; i < size - 1; i++) {
		for(j = i +1; j < size; j++){
			firstAtt = parseFloat($(rows[i]).attr(attr));
			secondAtt = parseFloat($(rows[j]).attr(attr));
			if(firstAtt > secondAtt) {
				aux = rows[i];
				rows[i] = rows[j];
				rows[j] = aux;
			}
		}
	}
	console.log(rows);
	$("#toSort tr:gt(0)").remove();
	for(i = 0; i< size; i++) {
		console.log(rows[i]);
		$('#toSort').append(rows[i]);
	}
	
}

function sortTable(index){
	var rows = $("#toSort tr:gt(0)");
	var tbody = document.getElementById('toSort').tBodies[0];
	var newBody = tbody;
	//console.log(tbody);
	//$("#toSort").find("tr:gt(0)").remove();
	var size = rows.length;
	var aux;
	var i = 0;
	for( i=1;i<size;i++){
		
		//console.log(newBody.rows[i]);
		var td = newBody.rows[i].cells[index];
		//console.log(td.textContent);
		var getCompare = td.textContent.split(' ');
		var toCompare = 0;
		//console.log(getCompare);
		if(index == 2 || index == 7){
			toCompare = getCompare[1];
		}else{
			toCompare = getCompare[0];
		}
		//console.log(toCompare);
		//var toSet = rows[i].find("td:eq("+index+")").text();
		var j=0;
		for(j=i+1;j<=size;j++){
			var td2 = newBody.rows[j].cells[index];
			var getMove = td2.textContent.split(' ');
			var toMove = 0;
			if(index == 2 || index == 7){
				toMove = getMove[1];
			}else{
				toMove = getMove[0];
			}
			
			//console.log(toCompare+" > "+toMove);
			if(sortingUp){
				if(parseFloat(toCompare) > parseFloat(toMove)){
					var tmpNode = tbody.replaceChild(tbody.rows[i],tbody.rows[j]);
					tbody.insertBefore(tmpNode,tbody.rows[i]);
					//switch
					//aux = tbody.rows[i];
					//newBody.rows[i] = newBody.rows[j];
					//newBody.rows[j] = newBody.rows[i];
				}
			}else{
				if(parseFloat(toCompare) < parseFloat(toMove)){
					var tmpNode = tbody.replaceChild(tbody.rows[i],tbody.rows[j]);
					tbody.insertBefore(tmpNode,tbody.rows[i]);
				}
			}
		}
		console.log(newBody);
	}
	
	/*
	rows.each(function(){
		console.log($(this).find("td:eq("+index+")").text());
		toSetRow = $(this);
		var togetVal = $(this).find("td:eq("+index+")").text();
		var splitted = togetVal.split(' ');
		var toGet = 0;
		if(index == 2){
			toGet = splitted[1];
		}else{ 
			toGet = splitted[0];
		}
		console.log(toGet);
		
	});*/
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

function getJobPosition(projectId){
	$.ajax({
		type: "GET",
		url: "getJobPosition.php",
		data: "projectId="+projectId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			if(obj.error==null){
				if(obj.lat!=null && obj.lng!=null){
					setJob(obj.lat,obj.lng);
				}else{jobSite.setMap(null);}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function getOptions(type,customer){
	$.ajax({
		type: "GET",
		url: "getProjectOptions.php",
		data: "type="+type+"&customerId="+customer,
		success: function(data){
			var obj = jQuery.parseJSON(data);
			console.log(obj);
			var projects = $("#projectId");
			projects.children().remove();
			projects.append("<option value='0' >--Select--</option>");
			jQuery.each(obj, function(i, val){
				projects.append("<option value='"+i+"' >"+val+"</option>");
			});
			/*
			material.append("<option value='0' >--Select Item--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
			*/
		},
		async: false
	});
}

function getSupplierAndPrice(materialId){
	$.ajax({
		type: "GET",
		url: "getSupplierAndPrice.php",
		data: "materialId="+materialId,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			//console.log(obj);
			if(obj.error==null){
				if(obj.lat!=null && obj.lng!=null){
					var i=0;
					deleteOverLays()
					deleteRoutes();
					for(i=0;i<obj.lat.length;i++){
						//console.log("getting supplier route");
						setSupplierPoint(obj.lat[i],obj.lng[i],obj.supplierName[i],obj.supplierId[i]);
						setRoute(obj.lat[i],obj.lng[i],obj.supplierId[i],colorArray[i]);
					}
				}
				if(obj.table!=null){
					if($('#priceList').length==0){
						$('#mapCanvas').after(obj.table);
					}else{
						$('#priceList').replaceWith(obj.table);
					}
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}

function setRoute(lat,lng,sId,color){
		//console.log("setting route");
		if(jobSite.getMap()!=null){
			var orig = new google.maps.LatLng(lat,lng);
			var dest = jobSite.getPosition();
			var request = {
				origin: orig,
				destination: dest,
				travelMode: google.maps.TravelMode.DRIVING
			};
			directionsService.route(request, function(response,status){
				//console.log(status);
				if(status == google.maps.DirectionsStatus.OK){
					//directionsDisplay.setDirections(response);
					//console.log(response.routes[0].legs[0]);
					var title = "";
					var route = response.routes[0].legs[0];
					console.log(route);
					var materialPriceText = $('#matprice'+sId).text().split(' ');
					var dumptime = $('#dptm'+sId).text();
					var tons = 1;
					var tonsTitle = "";
					
					materialPrice = materialPriceText[1];
					title = title + " [ "+materialPrice+" ]";
					//title = title + " * 1 ] ";
					
					if($('#byLoad').attr('checked')){
						
					}else{
						tons = 20;
						tonsTitle = "/20";
					}
					
					var etaVal = route.duration.value;
					var eta = Math.round(etaVal/60);
					
					$('#dist'+sId).text(route.distance.text);
					$('#row' + sId).attr('atdistance', route.distance.value);
					$('#row' + sId).attr('ateta', etaVal);
					//$('#eta'+sId).text(route.duration.text);
					$('#eta'+sId).text(eta + " mins");
					//console.log(perminute+" * [("+eta+" * 2 ) + "+dumptime+" + "+jobDumptime+" ]");
					var roundPricex2 = perminute * ((eta * 2) + parseFloat(dumptime) + parseFloat(jobDumptime));
					//console.log(parseFloat(materialPrice)+" + "+parseFloat(roundPricex2/tons));
					var etax2 = new Number(parseFloat(materialPrice) + parseFloat(roundPricex2/tons));
					$('#eta'+sId+'x2p').text("$ "+etax2.toFixed(2));
					$('#row' + sId).attr('atx2', etax2.toFixed(2));
					$('#eta'+sId+'x2p').attr("title",title + " + [ "+perminute+" * [( "+eta+" * 2 ) + "+jobDumptime+" + "+dumptime+" ] ]"+tonsTitle);
					var roundPricex25 = perminute * ((eta*(2.5)) + parseFloat(dumptime) + parseFloat(jobDumptime));
					var etax25 = new Number(parseFloat(materialPrice) + parseFloat(roundPricex25/tons));
					$('#eta'+sId+'x25p').text("$ "+etax25.toFixed(2));
					$('#eta'+sId+'x25p').attr("title",title + " + [ "+perminute+" * [( "+eta+" * 2.5 ) + "+jobDumptime+" + "+dumptime+" ] ]"+tonsTitle);
					var roundPricex3 = perminute * ((eta*3) + parseFloat(dumptime) + parseFloat(jobDumptime));
					var etax3 = new Number(parseFloat(materialPrice) + parseFloat(roundPricex3/tons));
					$('#eta'+sId+'x3p').text("$ "+etax3.toFixed(2));
					$('#eta'+sId+'x3p').attr("title",title + " + [ "+perminute+" * [( "+eta+" * 3 ) + "+jobDumptime+" + "+dumptime+" ] ]"+tonsTitle);
					//console.log(route);
					var i=0;
					for(i=0;i<route.steps.length;i++){
						//route.steps[i].polyline.setMap(map);
						var truckPath = new google.maps.Polyline({
							path: route.steps[i].lat_lngs,
							strokeColor: color,
							strokeOpacity: 1.0,
							strokeWeight: 2
						});
						truckPath.setMap(map);
						routesArray.push(truckPath);
					}
				}else if(status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT){
					setTimeout(function(){
						setRoute(lat,lng,sId,color);
					},200);
				}
			});
		}else{console.log('map null');}
}

function setSupplierPoint(lat,lng,name,id){
	//alert('algo');
	//console.log("setting point");
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

function deleteOverLays(){
		if(markersArray){
			for(i in markersArray){
				markersArray[i].setMap(null);
			}
			markersArray.length = 0;
		}
}

function deleteRoutes(){
		if(routesArray){
			for(i in routesArray){
				routesArray[i].setMap(null);
			}
			routesArray.length = 0;
		}
}

function deleteOverLays(){
		if(markersArray){
			for(i in markersArray){
				markersArray[i].setMap(null);
			}
			markersArray.length = 0;
		}
}

function newMarker(lat,lng){
	var mLatLng = new google.maps.LatLng(lat,lng);
		var marker = new google.maps.Marker({
			position: mLatLng,
			title: "Supplier"
		});
		
		marker.setMap(map);
		markersArray.push(marker);
}

function setJob(lat,lng){
	var mLatLng = new google.maps.LatLng(lat,lng);
	jobSite.setMap(null);
	jobSite = new google.maps.Marker({
		position: mLatLng,
		title: "Job"
	});
	jobSite.setMap(map);
}

function initializeMap(lat,lng,deep){
	directionsDisplay = new google.maps.DirectionsRenderer();
		var latlng = new google.maps.LatLng(lat,lng);
		var myOptions ={
			zoom: deep,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("mapCanvas"),myOptions);
		directionsDisplay.setMap(map);
}

</script>
<body onLoad='initializeMap(centerLat,centerLong,centerDeep);'>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitTicket.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="8">Search Options</th>
					</tr>
					<tr>
						<td>Customer</td>
						<td>Sketch/Project</td>
						<td>Job Dumptime</td>
						<td>Load</td>
						<td>Material</td>
					</tr>
					<tr class='bg' >
						<td class='first'>
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
							<select name='pathType' id='pathType' style='font-family:verdana;font-size:8pt'>
								<option selected='selected' value='0' >Projects</option>
								<option value='1' >Sketches</option>
							</select>
						</td>
						<td>
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?><span style="color:red;">*</span>
						</td>
						
						<td>
							<input type="text" id="jobDumptime" name="jobDumptime" />
						</td>
						
						<td>
							<input type="checkbox" id="byLoad" name="byLoad" /><label for="byLoad" >By Load</label>
						</td>
						
						<td>
						<?
						echo "<select name='materialId' id='materialId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Material--</option>";
						$queryMaterial = "SELECT * FROM material order by materialName";
						$materials = mysql_query($queryMaterial,$conexion);
						while($material=mysql_fetch_assoc($materials)){
								if(isset($_GET['b'])&&$_GET['b']==$material['materialId'])
									echo "<option selected='selected' value='".$material['materialId']."' >".$material['materialName']."</option>";
								else
									echo "<option value='".$material['materialId']."' >".$material['materialName']."</option>";
						}
						echo "</select>";
						?><span style="color:red;">*</span>
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<div id='mapCanvas' class='mapCanvas'>
			</div>
			<div id='priceList'>
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
