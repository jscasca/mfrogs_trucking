<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Dispatch";
#################
$subtitle = "Advanced View";
$description = "View information related to dispatch.";

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


$currentDate = date("Y-m-d",strtotime("now"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?echo$title." -".$subtitle;?></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" href="/trucking/img/favicon.ico" type="image/x-icon" />
	<style media="all" type="text/css">@import "../../css/longView.css";</style>
	<style media="all" type="text/css">@import "../../css/autocomplete.css";</style>
</head>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript" src="/trucking/js/jquery.autocomplete.js" ></script>
<script type="text/javascript" src="/trucking/js/jquery.mobile-1.0b1.min.js" ></script>
<script type="text/javascript">	
var patternTrucks = new RegExp(/truck/);
patternTrucks.compile(patternTrucks);
var patternDispatch = new RegExp(/dispatch/);
patternDispatch.compile(patternDispatch);
var patternLetters = new RegExp(/th/);
patternLetters.compile(patternLetters);
var colorSelected = "#FFF";
var colorless = "#D8D8D8";
var img = "/trucking/img/112.png";
var imgRemove = "/trucking/img/118.png";
var dispatchSelected = 0;

var last = null;

var actCd = 0;
var actCt = 0;

var autoProject;
var projectId = 0;

var autoBroker;
var brokerId = 0;


$(document).ready(function()
{
	
	$('#searchButton').click(function(){
		//getTable(project,broker,start,end,papers,grouping)
		var start = $('#startDate').val();
		var end = $('#endDate').val();
		var papers = $('#paperwork').val();
		var grouping = $('#groupBy').val();
		getTable(projectId,brokerId,start,end,papers,grouping);
		
	});
	
	var projectOptions = {
		serviceUrl: 'auto_getProjects',
		onSelect: function(value,data){projectId = data;console.log(projectId);}
	};
	
	autoProject = $('#projectName').autocomplete(projectOptions);
		
	var brokerOptions = {
		serviceUrl: 'auto_getBrokers',
		onSelect: function(value,data){brokerId = data;console.log(brokerId);}
	};
	
	autoBroker = $('#brokerName').autocomplete(brokerOptions);
	
	$("input[type='checkbox']").live('change',function(){
		var dispatchId = $(this).closest("table").attr('id').replace(patternDispatch,'');
		var truckId = $(this).closest("tr").attr('id').replace(patternTrucks,'');
		if($(this).is(":checked")){ var set=true;}else{var set=false;}
		submitPapers(dispatchId,truckId,set);
	});
	$("#featuresList input[name='features[]']").change(function(){
		getTrucks();
	});
	
	$('.table table td p').live('dblclick',function(){
		$(this).focus();
		var element = $(this);
		var content = element.text();
		if(content == "-no comment-")content="";
		element.replaceWith("<input type='text' size='50px' id='activeText' value='"+content+"' />");
		$('#activeText').focus(
			function(){
				$(this).select();
			}
		);
		
		$('#activeText').focus();
		$('#activeText').mouseup(
			function(e){
				e.preventDefault();
			}
		);
	});
	
	$('#activeText').live('blur',function(){
		var content = $(this).val();
		console.log(content);
		if(content != ""){
			var dispatchId = $(this).closest("table").attr('id').replace(patternDispatch,'');
			var truckId = $(this).closest("tr").attr('id').replace(patternTrucks,'');
			submitComment(dispatchId,truckId,content);
			$(this).replaceWith("<p>"+content+"</p>");
		}else{
			$(this).replaceWith("<p>-no comment-</p>");
		}
	});
	
	$('.table table th').live('dblclick',function(){
		var id=$(this).attr('id');
		console.log(id);
		if(id!=undefined){
				id=id.replace(patternLetters,'');
				//$("tbody[id^=tb"+id+"]").hide();
				//$("tbody[id^=tb"+id+"]").toggle();
				$("[class^='tb"+id+"']").toggle();
				//$("#tb"+id).toggle();
				//var tbody = $("#tb"+id);
				//tbody.toggle();
				//console.log(tbody);
				//window.location.replace("./editDriver.php?i="+id);
			}
		//console.log($(this).closest("table"));
		//$(this).closest("table").find("tbody:last").toggle();
	});
	
	$('.dateTime').click(function(){
		var value = $(this).attr('value');
		cleanDispatch();
		getDispatch(value);
	});
	
	/*
	$('.table table th').live('vclick',function(){
		$(this).closest("table").find("tbody:last").toggle();
	});*/
	
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
	
	$("input[type='checkbox']").live('change',function(){
		var dispatchId = $(this).attr('id').replace(patternDispatch,'');
		var truckId = $(this).closest("tr").attr('id').replace(patternTrucks,'');
		if($(this).is(":checked")){ var set=true;}else{var set=false;}
		submitPapers(dispatchId,truckId,set);
	});

});

function submitPapers(dispatchId,truckId,set){
	$.ajax({
		type: "GET",
		url: "submitPapers.php",
		data: "dispatchId="+dispatchId+"&truckId="+truckId+"&set="+set,
		success: function(){},
		async: true
	});
}

function getTable(project,broker,start,end,papers,grouping){
	console.log("get table");
	$.ajax({
		type: "GET",
		url: "getView.php",
		data: "project="+project+"&broker="+broker+"&start="+start+"&end="+end+"&papers="+papers+"&grouping="+grouping,
		success: function(data){
			var obj = jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.query!=null){
					//$('#dispatchDate').val(obj.newDay);
					$("#resultTable").remove();
					if(obj.table!=null){
						//$('.table').append(obj.tbody);
						console.log("appending");
						$('#advancedViewResult').append(obj.table);
					}
				}
			}
		},
		async: true
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
		
			<div class="table" id="selectDiv">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="7">Dispatch for</th>
					</tr>
					<!--<tr>
						<td class="first" width="172" align="center" class="put-in-center">
							<style type="text/css">
								table.mfinfo td{
								font-size: 16px;
								font-style: italic;
								}
							</style>
							<img src="/trucking/img/99.png" width="18px" value="-1" class="dateTime">
							<input type="text" id="dispatchDate" size="10" value="<?echo to_MDY($currentDate);?>" />
							<img src="/trucking/img/98.png" width="18px" value="+1" class="dateTime">
						</td>
					</tr>-->
					<tr class="bg">
						<td class="first">Paperwork</td>
						<td>Project</td>
						<td>Broker</td>
						<td colspan="2">Date Range</td>
						<td>Group By</td>
						<td class="last"></td>
					</tr>
					<tr>
						<td>
							<select name='paperwork' id='paperwork' style='font-family:verdana;font-size:8pt'>
								<option value="0" selected='selected'>All</option>
								<option value="1" >Complete</option>
								<option value="2" >Missing</option>
							</select>
						</td>
						<td>
							<input type="text" id="projectName" name="projectName"/>
						</td>
						<td>
							<input type="text" id="brokerName" name="brokerName"/>
						</td>
						<td>
							<input type="text" id="startDate" name="startDate" size="6" />
						</td>
						<td>
							<input type="text" id="endDate" name="endDate" size="6"/>
						</td>
						<td>
							<select name='groupBy' id='groupBy' style='font-family:verdana;font-size:8pt'>
								<option value="0" selected='selected'>Date</option>
								<option value="1" >Job</option>
								<option value="2" >Broker</option>
							</select>
						</td>
						<td><input type="button" value="Search" id="searchButton" /></td>
					</tr>
				</table>
				
				
			
			
			
			<?
			/*
			$dateInformation = "select * from dispatch join project using (projectId) LEFT JOIN (select count(*) as truckCount, dispatchId from truckdispatch group by dispatchId) as td using (dispatchId) where dispatchDate = '$currentDate'";
			$dispatchs = mysql_query($dateInformation, $conexion);
			while($dispatch = mysql_fetch_assoc($dispatchs)){
				echo "
				<table class='listing form' cellpadding='0' cellspacing='0' id='dispatch".$dispatch['dispatchId']."'> 
					<tr>
						<th class='first' colspan='2'>".$dispatch['projectName']."</th>
						<th class='last' width='10%' >".($dispatch['truckCount']==null?"0":$dispatch['truckCount'])."/".$dispatch['dispatchCount']."</th>
					</tr>";
				echo "<tbody>";
				$getTrucksPerDispatch = "select * from truckdispatch join truck using (truckId) join broker using (brokerId) where dispatchId = ".$dispatch['dispatchId'];
				$trucks = mysql_query($getTrucksPerDispatch,$conexion);
				$flag = true;
				while($truck = mysql_fetch_assoc($trucks)){
					echo "<tr ".($flag?"class='bg'":"")." id='truck".$truck['truckId']."' ><td width='20%'><input type='checkbox' ".($truck['truckDispatchPapers']?"checked disabled ":"")." />".$truck['brokerPid']."-".$truck['truckNumber']."</td><td colspan='2'><p>".($truck['truckDispatchComment']==""?"-no comment-":$truck['truckDispatchComment'])."</p></td></tr>";
					$flag = !$flag;
				}
				echo "</tbody>";
				echo "</table>";
			}*/
			?>
			</div>	
			
			
			<div id="advancedViewResult" class="table">
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
