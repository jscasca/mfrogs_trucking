<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "New";
#################
$subtitle = "Report";
$description = "Add a new report. Reports contain all the tickets already created in a range of time. Reports can be for brokers or drivers, select a driver to make a driver report or leave empty to make a broker report. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
$(document).ready(function()
{
	$('#preview').click(function(){
		var startDate = $('#reportStartDate').val();
		var endDate = $('#reportEndDate').val();
		var brokerId = $('#brokerId').val();
		var driverId = $('#driverId').val();
		var preview = $('#reportType option:selected').text();
		if(driverId==0)preview = "Broker";
		else preview = "Driver";
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'preview'+preview+'Report.php?startDate='+startDate+'&endDate='+endDate+'&brokerId='+brokerId+'&driverId='+driverId
		}).width('100%').height('2048px').appendTo('#previewFrame');
		preview = true;
	});
	
	$('#reportStartDate').blur(function(){
		var startDate = this.value;
		if(startDate != ""){
			startDate = startDate.replace(/(\d+)\/(\d+)\/(\d+)/,'$3/$1/$2');
			d =new Date(startDate);
			d.setDate((d.getDate() - d.getDay())+6);
			
		}else{
			d = new Date();
		}
		year = d.getFullYear()+'';
		month=d.getMonth()+1+'';
		day = d.getDate()+'';
		if(month.length==1)month='0'+month;
		if(day.length==1)day='0'+day;
		$('#reportEndDate').val(month+'/'+day+'/'+year);
	});
	
	$('#brokerId').change(function() {
		var broker=this.value;
		getDrivers(broker);
	});
	
});

function getDrivers(broker){
	$.ajax({
		type: "GET",
		url: "getDrivers.php",
		data: "brokerId="+broker,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#driverId');
			material.children().remove();
			material.append("<option value='0' >--Select Driver--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}

function validateForm(){
	
	
	
	if(document.getElementById('brokerId').selectedIndex==0 ){
		alert("Please select a broker for this report");
			document.formValidate.brokerId.focus;
			return false;
	}
	
	if(document.getElementById('reportStartDate').value.length==0){
		alert("Please type a starting date");
			document.formValidate.reportStartDate.focus;
			return false;
	}
	
	if(document.getElementById('reportEndDate').value.length==0){
		alert("Please select and end date");
			document.formValidate.reportEndDate.focus;
			return false;
	}
	
	if(!preview){
		if(!confirm("Weekly report will be created for the ticket in this period of time. Are you sure you want to continue?")){
			return false;
		}
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
			<form id="formValidate" name="formValidate" method="POST" action="submitReport.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="6">New Report</th>
					</tr>
					<tr class='bg'>
						<td><strong>Broker:</strong><span style="color:red;">*</span></td>
						<td><strong>Driver:</strong></td>
						<td><strong>Starting Date:</strong><span style="color:red;">*</span></td>
						<td><strong>End Date:</strong><span style="color:red;">*</span></td>
						<!--<td><strong>Report Type:</strong><span style="color:red;">*</span></td>-->
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from broker where brokerStatus=1 order by brokerName asc";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Broker--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['p'])&&$_GET['p']==$term['brokerId'])
										echo "<option selected='selected' value='{$term['brokerId']}'>{$term['brokerName']}</option>";
									else
										echo "<option value='{$term['brokerId']}'>{$term['brokerName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no brokers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td>
						<?
						echo "<select name='driverId' id='driverId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Driver--</option>";
						echo "</select>";
						?>
						</td>
						<td><input type='text' size='10px' id='reportStartDate' name='reportStartDate' /></td>
						<td><input type='text' size='10px' id='reportEndDate' name='reportEndDate' /></td>
						<!--<td>
							<select id='reportType' name='reportType' style='font-family:verdana;font-size:8pt'/>
								<option value='1' name='Broker' >Broker</option>
								<option value='0' name='Driver' >Driver</option>
							</select>
						</td>-->
						<td><input type='button' size='10px' id='preview' name='prevBtn' value='Preview' /></td>
						<td><input type='submit' size='10px' id='submit' name='subBtn' value='Submit' /></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<div class='iframes' id='previewFrame' >
			</div>
			<?
			/*
			$queryContacts = "select * from item order by itemId desc limit 5";
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
						<td class='first style2'>JOB &nbsp;&nbsp;".$term['projectId']."&nbsp;&nbsp; ITEM &nbsp;&nbsp;".$term['itemNumber']."</td>
						<td><a href='/trucking/php/view/viewItem.php?i=".$term['itemId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editItem.php?i=".$term['itemId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete item #".$term['itemNumber']."?');\" href='deleteItem.php?i=".$term['itemId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
			}*/
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
