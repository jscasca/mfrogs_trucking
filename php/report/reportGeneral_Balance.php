<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Broker";
$description = "
This reports offers a general view of each broker balance in a period of time. If dates are not selected the report will cover all the tickets created up to the current date.
<ul>
	<li>Broker: The broker name and payment percentage.</li>
	<li>Income: The earnings of the broker in the specified period of time.</li>
	<li>Total tickets: The total number of tickets submitted in that period of time.</li>
	<li>In report: The amount earned in the reports created in that period of time.</li>
	<li>Not reported: The number of tickets from that period of time that are not contained in any report.</li>
	<li>Paid: The amount paid to that broker for those reports.</li>
	<li>Cheques: The number of cheques released to the broker as payment.</li>
</ul>
";

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
	$('#descriptionDisplay').click(function(){
		$('#hiddenDescription').toggle();
	});
	$('#preview').click(function(){
		var fromDate = $('#fromDate').val();
		var toDate = $('#toDate').val();
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'showGeneralBalance.php?fromDate='+fromDate+'&toDate='+toDate
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#popup').click(function(){
		getBalanceByDates();
	});
	
});


function validateDate(dateString){
	
	if(dateString == ""){
		alert("Please select a valid date.");
		return false;
	}
	
	return true;
}

function getBalanceByDates(){
	var startDate = $('#fromDate').val();
	if(!validateDate(startDate)){return;}
	var endDate = $('#toDate').val();
	if(!validateDate(endDate)){return;}
	var url = 'showGeneralBalance.php?startDate='+startDate+'&endDate='+endDate;
	var windowName = 'popUp';
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(url,windowName,windowSize);
	event.preventDefault();
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
						<th class="full" id='descriptionDisplay' >Description</th>
					</tr>
					<tr style="display:none;" id='hiddenDescription'>
						<td class="last" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">Brokers Weekly</th>
					</tr>
					<tr class='bg'>
						<td>From Date</td>
						<td>To Date</td>
						<td></td>
					</tr>
					<tr>
						<td><input type='text' size='10px' id='fromDate' name='fromDate' /></td>
						<td><input type='text' size='10px' id='toDate' name='toDate' /></td>
						<td>
							<input type='button' size='10px' id='preview' name='prevBtn' value='Preview' />
							<input type='button' size='10px' id='popup' name='popBtn' value='Balance' />
						</td>
					</tr>
				</table>
				</form>
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
