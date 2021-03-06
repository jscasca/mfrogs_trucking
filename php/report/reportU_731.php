<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Broker";
$description = ". Values marked with <span style='color:red;'>*</span> are mandatory.";

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
var patternDate = new RegExp(/(\d+)\/(\d+)\/(\d+)/);
patternDate.compile(patternDate);

$(document).ready(function()
{
	$('#preview').click(function(){
		var fromDate = $('#fromDate').val();
		var toDate = $('#toDate').val();
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'getBrokerTickets.php?fromDate='+fromDate+'&toDate='+toDate
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#unionReport731').click(function(){
		showUnionReport731();
	});
	
	$('#brokersPayroll').click(function(){
		getBrokersPayroll();
	});
	
	$('#driversPayroll').click(function(){
		getDriversPayroll();
	});
	
});

function showUnionReport731(){
	var startDate = evalDate($('#fromDate').val());
	var endDate = evalDate($('#toDate').val());
	if(startDate == 0 || endDate== 0){alert("Please select a valid date");return;}
	var unionId=$('#unionId').val();
	
	var url ="showUnionReport731.php?startDate="+startDate+"&endDate="+endDate+"&unionId="+unionId;
	var windowName = "Broker Payroll";
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(url,windowName,windowSize);
}


function validateDate(dateString){
	
	if(dateString == ""){
		alert("Please select a valid date.");
		return false;
	}
	
	return true;
}

function evalDate(date){
		if(date.match(patternDate)){
			date=date.replace(patternDate,'$3-$1-$2');
			return date;
		}else{return '0';}
}

function getBalanceByDates(){
	var startDate = $('#fromDate').val();
	if(!validateDate(startDate)){return;}
	var endDate = $('#toDate').val();
	if(!validateDate(endDate)){return;}
	var projectId=$('#projectId').val();
	var url = 'showPayroll.php?startDate='+startDate+'&endDate='+endDate+'&projectId='+projectId;
	var windowName = 'popUp';
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(url,windowName,windowSize);
	//event.preventDefault();
}


function validateForm(){
	
	if(!preview){
		if(!confirm("Are you sure you don't want to see the preview?")){
			return false;
		}
	}
	
	if(document.getElementById('fromDate').value.length==0){
		alert("Please type a starting date");
			document.formValidate.fromDate.focus;
			return false;
	}
	
	if(document.getElementById('toDate').value.length==0){
		alert("Please select and end date");
			document.formValidate.toDate.focus;
			return false;
	}
	
	return true;
}
</script>
<style type="text/css">
.options{
	float:left;
}
</style>
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
						<th class="full" colspan="6">Certified Payroll</th>
					</tr>
					<tr class='bg'>
						<td>From Date</td>
						<td>To Date</td>
						<td>Project</td>
						<td>Options</td>
						<td>Generate:</td>
					</tr>
					<tr>
						<td><input type='text' size='10px' id='fromDate' name='fromDate' /></td>
						<td><input type='text' size='10px' id='toDate' name='toDate' /></td>
						<td> <?
						$queryTerm = "select * from worker_union";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='unionId' id='unionId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Union--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
										echo "<option value='{$term['unionId']}'>{$term['unionName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no worker unions in the DataBase</option>";
							
						}
						echo "</select>";
						?></td>
						<td></td>
						<td>
							<input type='button' size='10px' id='unionReport731' name='popBtn' value='Report' />
						</td>
					</tr>
				</table>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='iframes' id='previewFrame' >
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
