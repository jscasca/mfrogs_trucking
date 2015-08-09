<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Dispatch";
#################
$subtitle = "Search for Dispatch";
$description = "";

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
<script type="text/javascript" src="/trucking/js/jquery.mobile-1.0b1.min.js" ></script>
<script type="text/javascript">	
var patternTrucks = new RegExp(/truck/);
patternTrucks.compile(patternTrucks);
var patternDispatch = new RegExp(/dispatch/);
patternDispatch.compile(patternDispatch);
var colorSelected = "#FFF";
var colorless = "#D8D8D8";
var img = "/trucking/img/112.png";
var imgRemove = "/trucking/img/118.png";
var dispatchSelected = 0;

var last = null;

$(document).ready(function()
{
	$('a').live('touchend', function(e) {
		var el = $(this);
		var link = el.attr('href');
		window.location = link;
	});
	
	$('a').live('vclick', function(e) {
		var el = $(this);
		var link = el.attr('href');
		window.location = link;
	});
	
	$(".sendText").click(function(){
		sendTexts();
	});
	$("#checkAllTrucks").change(function(){
		var booleanVal = $(this).is(':checked');
		markAllTrucks(booleanVal);
	});
	
	$("#featuresList input[name='features[]']").change(function(){
		getTrucks();
	});
	
	$('#trucksList tr td img').live('click',function(){
		var id=$(this).closest('td').attr('id');
		if(id!=undefined){
			id=id.replace(patternTrucks,'');
			if(dispatchSelected != 0){
				makeDispatch(id);
			}
		}
	});
	
	$('#dispatchList tr td').live('dblclick',function(){
		var id=$(this).attr('id');
		if(id!=undefined){
			id=id.replace(patternDispatch,'');
			
			if(id == dispatchSelected){
				dispatchSelected = 0;
				$(this).css('backgroundColor',colorless);
			}else{
				$('#dispatch'+dispatchSelected).css('backgroundColor',colorless);
				dispatchSelected = id;
				$(this).css('backgroundColor',colorSelected);
			}
			getTrucks();
		}
	});
	
	$('#dispatchList tr td').live('vclick',function(){
		var id=$(this).attr('id');
		if(id!=undefined){
			id=id.replace(patternDispatch,'');
			
			if(id == dispatchSelected){
				dispatchSelected = 0;
				$(this).css('backgroundColor',colorless);
			}else{
				$('#dispatch'+dispatchSelected).css('backgroundColor',colorless);
				dispatchSelected = id;
				$(this).css('backgroundColor',colorSelected);
			}
			getTrucks();
		}
	});

	$('#brokerId').change(function(){getTrucks();});
	$('#truckNumber').keyup(function(){getTrucks();});
	
	$('.dateTime').click(function(){
		var value = $(this).attr('value');
		cleanDispatch();
		getDispatch(value);
	});

});

function sendTexts(){
	$('#loading').append("<img id='loadingImg' src='/trucking/img/007.gif' width='500px' height='16px' />");
	var trucks = getMarkedTrucks();
	console.log(trucks);
	var text = $('#textingArea').val();
	console.log(text);
	if(text == ''){
		alert("Please write a message to be send");
		$('#loadingImg').remove();
		return false;
	}
	$.ajax({
		type: "GET",
		url: "sendTexts.php",
		data: "trucks="+trucks+"&text="+text,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			if(obj.error==null){
				if(obj.response!=null){
					alert("response");
				}
			}
			$('#loadingImg').remove();
		},
		async: false
	});
	
}

function markAllTrucks(booleanVal){
	$.each($("input[name='truckscheck[]']"),function(){
		$(this).attr('checked',booleanVal);
	});
}

function getMarkedTrucks(){
	var ids = "";
	$.each($("input[name='truckscheck[]']:checked"),function(){
		ids +=(ids?',':'')+$(this).attr('value');
	});
	return ids;
}

function getMarkedFeatures(){
	var ids = "";
	$.each($("input[name='features[]']:checked"),function(){
		ids +=(ids?',':'')+$(this).attr('value');
	});
	return ids;
}

function getTrucks(){
	var features = getMarkedFeatures();
	var broker = $("#brokerId").val();
	var optionsIndicator = $('#optionsIndicator').val();
	console.log(optionsIndicator);
	$.ajax({
		type: "GET",
		url: "getTrucks.php",
		data: "features="+features+"&dispatch="+dispatchSelected+"&optionsIndicator="+optionsIndicator+"&broker="+broker,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			if(obj.error==null){
				if(obj.tbody!=null){
					$('#trucksList').replaceWith(obj.tbody);
				}
			}
		},
		async: false
	});
}

function cleanDispatch(){
	dispatchSelected = 0;
	$('#dispatchList').find('tr:gt(1)').remove();
	getTrucks();
}

function reassignDispatched(obj){
	$('#trucksList td').each(function(){
		var id = $(this).attr('id');
		if(id != undefined )
		deselectTruck(id.replace(patternTrucks,''));
		//deselectTruck($(this).attr('id').replace(patternTrucks,''));
	});
	for( var id in obj){
		selectTruck(id);
	}
}

function getDispatched(){
	
	$.ajax({
		type: "GET",
		url: "getDispatched.php",
		data: "dispatchId="+dispatchSelected,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			//reassignDispatched(obj.trucks);
		},
		async: false
	});
}

function makeDispatch(dispatched){
	
	$.ajax({
		type: "GET",
		url: "makeDispatch.php",
		data: "dispatchId="+dispatchSelected+"&dispatchedId="+dispatched,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			if(obj.error==null){
				if(obj.action == "insert"){
					selectTruck(obj.id);
					add();
				}else{
					deselectTruck(obj.id);
					substract();
				}
			}
		},
		async: false
	});
}

function selectTruck(id){
	$('#truck'+id).css('backgroundColor',colorSelected);
	$('#truck'+id+' img').attr('src',imgRemove);
}

function deselectTruck(id){
	$('#truck'+id).css('backgroundColor',colorless);
	$('#truck'+id+' img').attr('src',img);
}

function add(){
	var cant = parseInt($('#count'+dispatchSelected).text());
	$('#count'+dispatchSelected).text(cant+1);
}

function substract(){
	var cant = parseInt($('#count'+dispatchSelected).text());
	$('#count'+dispatchSelected).text(cant-1);
}

function getDispatch(move){
	var date = $('#dispatchDate').attr('value');
	$.ajax({
		type: "GET",
		url: "getDispatch.php",
		data: "dispatchDate="+date+"&move="+move,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			if(obj.error==null){
				if(obj.currentDate!=null){
					$('#dispatchDate').val(obj.currentDate);
					if(obj.tbody!=null){
						$('#dispatchList').append(obj.tbody);
					}
				}
			}
		},
		async: false
	});
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
		</div>
		<div id="center-column">
		
			<div class="left-inside">
				<div class="inside-side">
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='8' height='7' alt='' class='right' />
					<table class='side' cellpadding='0' cellspacing='0' id='featuresList'>
						<tr>
							<th class="full">Features</th>
						</tr>
						<tr>
							<td>
								<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt;width:120px'>
									<option selected='selected' value='0' >--Select Broker--</option>
									<?
										$brokers = mysql_query("select * from broker order by brokerName", $conexion);
										while($broker = mysql_fetch_assoc($brokers)){
											echo "<option value='".$broker['brokerId']."'>".$broker['brokerName']." (".$broker['brokerPid'].")</option>";
										}
									?>
								</select>
							</td>
						</tr><tr>
							<td class='bg'>
								<select id='optionsIndicator' name='optionsIndicator'>
									<option value='2' >All</option>
									<option value='1' >Any</option>
									<option value='0' >None</option>
								</select>
							</td>
						</tr>
						<?
						$featuresQuery = "select * from feature";
						$features = mysql_query($featuresQuery,$conexion);
						$num_features = mysql_num_rows($features);
						if($num_features>0){
							while($feature = mysql_fetch_assoc($features)){
								echo"<tr><td class='first' align='right'>";
								echo "<input type='checkbox' name='features[]' id='feature-".$feature['featureId']."' value='".$feature['featureId']."' /><label for='feature-".$feature['featureId']."'><strong>".$feature['featureName']."</strong> ".$feature['featureDescription']."</label> ";
								echo"</td></tr>";
							}
						}
						?>
					</table>
				</div>
			</div>
			
			<div id="center-inside">
				<div class="inside-table">
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='8' height='7' alt='' class='right' />
					<table class='long-center' cellpadding='0' cellspacing='0' id='texting'>
						<tr>
							<th class="full">Texting</th>
						</tr>
						<tr>
							<td>
								<textarea id="textingArea" rows="2" cols="40"></textarea>
								<input type="button" class="sendText" value="Send Text"/>
							</td>
						</tr>
					</table>
					<div id="loading">
					</div>
				</div>
				
				<div class="inside-table">
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='8' height='7' alt='' class='right' />
					<table class='long-center' cellpadding='0' cellspacing='0' id='trucksList'>
						<tr>
							<th class="full" colspan="3"><input type='checkbox' name='truckHeader' id='checkAllTrucks' /><label for='checkAllTrucks'>Trucks</label></th>
						</tr>
						<?
						$tdClass="";
						$trucksQuery = "select * from truck JOIN broker using (brokerId) order by brokerName";
						$trucks = mysql_query($trucksQuery,$conexion);
						$num_trucks = mysql_num_rows($trucks);
						$colorFlag = true;
						$actual = 0;
						echo "<tbody>";
						if($num_trucks>0){
							while($truck = mysql_fetch_assoc($trucks)){
								
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
								switch($actual){
									case 0:
									echo "<tr>";
									echo "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/112.png' width='14px' /></td>";
									$actual++;
									break;
									case 1:
									echo "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/112.png' width='14px' /></td>";
									$actual++;
									break;
									case 2:
									echo "<td ".$tdClass." id='truck".$truck['truckId']."' ><input type='checkbox' name='truckscheck[]' value='".$truck['truckId']."' id='check".$truck['truckId']."' /><label for='check".$truck['truckId']."' >".$truck['brokerPid']."-".$truck['truckNumber']." </label><img src='/trucking/img/112.png' width='14px' /></td>";
									echo "</tr>";
									$actual=0;
									break;
								}
							
						}
						switch($actual){
								case 0:break;
								case 1:echo"<td ".$tdClass." colspan='2'></td></tr>";break;
								case 2:echo"<td ".$tdClass." colspan='1'></td></tr>";break;
							}
						}
						echo "</tbody>";
						?>
					</table>
				</div>
			
			</div>
			
			<div id="right-inside">
				<div class="inside-side">
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='8' height='7' alt='' class='right' />
					<table class='side' cellpadding='0' cellspacing='0' id='dispatchList'>
						<tr>
							<th class="full">Dispatchs for:</th>
						</tr>
						<?
						$today = date("Y-m-d",strtotime("now"));
						?>
						<tr>
							<td>
								<img src="/trucking/img/99.png" width="18px" value="-1" class="dateTime">
								<input type="text" id="dispatchDate" size="10" value="<?echo to_MDY($today);?>" />
								<img src="/trucking/img/98.png" width="18px" value="+1" class="dateTime">
							</td>
						</tr>
						<?
						$dispatchQuery = "select * from dispatch JOIN project using (projectId) LEFT JOIN (select count(*) as truckCount, dispatchId from truckdispatch group by dispatchId) as td using (dispatchId) where dispatchDate = '$today' order by projectName asc";
						$dispatches = mysql_query($dispatchQuery,$conexion);
						$num_dispatch = mysql_num_rows($dispatches);
						if($num_dispatch>0){
							while($dispatch = mysql_fetch_assoc($dispatches)){
								if($dispatch['truckCount']==$dispatch['dispatchCount']){
									echo "<tr>";
								}else
									echo "<tr>";
									
								echo"<td id='dispatch".$dispatch['dispatchId']."' >";
								echo $dispatch['projectName']." <Strong><label id='count".$dispatch['dispatchId']."'>".($dispatch['truckCount']==null?"0":$dispatch['truckCount'])."</label>/<label id='max".$dispatch['dispatchCount']."' >".$dispatch['dispatchCount']."</label></Strong>";
								echo"</td>";
								echo "</tr>";
							}
						}
						?>
					</table>
				</div>
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
