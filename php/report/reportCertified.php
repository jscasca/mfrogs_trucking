<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Report";
#################
$subtitle = "Invoice";
$description = "Add a new invoice. Invoices contain all the tickets already created in a range of time. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
	$('#invoices tr').live('dblclick',function(){
			var invoiceId = this.id;
			invoiceId = invoiceId.replace(/invoice/,'');
			
			var url = 'showInvoice.php?i='+invoiceId;
			var windowName = 'popUp';
			var windowSize = 'width=814,heigh=514,scrollbars=yes';
			window.open(url,windowName,windowSize);
			event.preventDefault();
	});
	
	$('#projectId').change(function(){
			getProjectInfo();
	});
	
	$('#generateCertified').click(function(){
		
		if(!validateElements())
			return false;
		generatePreview();
	});
	
	$('#showCertified').click(function(){
		if(!validateElements())
			return false;
		showPreview();
	});
	
});

function showPreview(){
	var projectId = $('#projectId').val();
	var brokerId = $('#brokerId').val();
	var weekNumber = $('#weekNumber').val();
	
	var url = 'previewCertified.php?projectId='+projectId+"&brokerId="+brokerId+"&week="+weekNumber+"&hideDeductions=false";
	var windowName = 'popUp';
	var windowSize = 'width=814,heigh=514,scrollbars=yes';
	window.open(url,windowName,windowSize);
	event.preventDefault();
}

function generatePreview(){
	var projectId = $('#projectId').val();
	var brokerId = $('#brokerId').val();
	var weekNumber = $('#weekNumber').val();
	
		$('#framePreview').remove();
		
		$('<iframe />',{
			name: 'framePreview',
			id: 'framePreview',
			src: 'previewCertified.php?projectId='+projectId+"&brokerId="+brokerId+"&week="+weekNumber+"&hideDeductions=true"
		}).width('100%').height('2048px').appendTo('#previewFrame');
}

function validateElements(){
	
	
	if($('#projectId').val() == 0){
		alert("Please select a project");
		return false;
	}
	
	if($('#brokerId').val() == 0){
		alert("Please select a broker");
		return false;
	}
	
	if($('#projectFirstTicket').text()=="N/A"){
		alert("There are no tickets for that project");
		return false;
	}
	return true;
}


function getProjectInfo(){
	var projectId=$('#projectId').val();
	$.ajax({
		type: "GET",
		url: "getProjectInfo.php",
		data: "projectId="+projectId,
		success: function(data){
			//console.log(data);
			var obj=jQuery.parseJSON(data);
			$('#projectClass1PW').text(obj.class1pw);
			$('#projectClass2PW').text(obj.class2pw);
			$('#projectClass3PW').text(obj.class3pw);
			$('#projectClass4PW').text(obj.class4pw);
			$('#projectBrokerPW').text(obj.brokerpw);
			$('#projectFirstTicket').text(obj.lastTicketDate);
		},
		async:false
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
		
		
			<div class="top-bar">
				<a href="#" class="project"></a>
			</div><br />
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInvoice.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="8">Invoices Report</th>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td>First Ticket</td>
						<td colspan="4">Class Prevailing Wage</td>
						<td>Broker Wage</td>
					</tr>
					<tr class='bg'>
						<td>Project</td>
						<td>
						<?
						$queryTerm = "select * from project where projectInactive=0 order by projectName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Project--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
										echo "<option selected='selected' value='{$term['projectId']}'>{$term['projectName']}</option>";
									else
										echo "<option value='{$term['projectId']}'>{$term['projectName']}</option>";
								}
						}
						else
						{
							echo "<option selected='selected'>There are no projects in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
						<td id='projectFirstTicket'></td>
						<td id='projectClass1PW'></td>
						<td id='projectClass2PW'></td>
						<td id='projectClass3PW'></td>
						<td id='projectClass4PW'></td>
						<td id='projectBrokerPW'></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						
						<?
						$queryUnionInfo = "select * from union731 order by unionId desc limit 1";
						$unionsInfo = mysql_query($queryUnionInfo,$conexion);
						$countUnions = mysql_num_rows($unionsInfo);
						if($countUnions > 0){
							$union = mysql_fetch_assoc($unionsInfo);
							echo "<td id='unionClass1HR'>".decimalPad($union['unionClass1HourlyRate'])."</td>";
							echo "<td id='unionClass2HR'>".decimalPad($union['unionClass2HourlyRate'])."</td>";
							echo "<td id='unionClass3HR'>".decimalPad($union['unionClass3HourlyRate'])."</td>";
							echo "<td id='unionClass4HR'>".decimalPad($union['unionClass4HourlyRate'])."</td>";
							echo "<td></td></td>";
						}else{
							echo "<td colspan='5'>There is no union information</td>";
						}
						?>
					</tr>
					<tr class="bg">
						<td>Broker</td>
						<td>
							<?
						$queryTerm = "select * from broker where brokerStatus=1 order by brokerName";
						$terms = mysql_query($queryTerm,$conexion);
						$countTerms = mysql_num_rows($terms);
						echo "<select name='brokerId' id='brokerId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value='0' >--Select Broker--</option>";
						if($countTerms > 0)
						{
								while($term=mysql_fetch_assoc($terms))
								{
									//if(isset($_GET['p'])&&$_GET['p']==$term['projectId'])
										//echo "<option selected='selected' value='{$term['projectId']}'>{$term['projectName']}</option>";
									//else
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
						<td><input type="text" class="text" id="weekNumber" name="weekNumber" size="5px" style="width:35px" /></td>
						<td colspan="5"></td>
					</tr>
					
					<tr>
						<td colspan="5"></td>
						<td colspan="2"><input type="button" id="showCertified" name="showCertified" value="Show"/></td>
						<td><input type="button" id="generateCertified" name="generateCertified" value="Generate"/></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='table' id='reports' >
			
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
