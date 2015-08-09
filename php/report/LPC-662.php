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
	$('#preview').click(function(){
		var startDate = $('#invoiceStartDate').val();
		var endDate = $('#invoiceEndDate').val();
		var projectId = $('#projectId').val();
		var comment = $('#invoiceComment').text();
		$('#framePreview').remove();
	});
		
	$('#invoiceStartDate').blur(function(){
		var startDate = this.value;
		startDate = startDate.replace(/(\d+)\/(\d+)\/(\d+)/,'$3/$1/$2');
		d =new Date(startDate);
		d.setDate((d.getDate() - d.getDay())+6);
		year = d.getFullYear()+'';
		month=d.getMonth()+1+'';
		day = d.getDate()+'';
		if(month.length==1)month='0'+month;
		if(day.length==1)day='0'+day;
		$('#invoiceEndDate').val(month+'/'+day+'/'+year);
	});
	
});

function validateForm(){
	
	if(!preview){
		if(!confirm("Are you sure you don't want to see the preview?")){
			return false;
		}
	}
	
	if(document.getElementById('projectId').selectedIndex==0 ){
		alert("Please select a project for this invoice");
			document.formValidate.projectId.focus;
			return false;
	}
	
	if(document.getElementById('invoiceStartDate').value.length==0){
		alert("Please type a starting date");
			document.formValidate.invoiceStartDate.focus;
			return false;
	}
	
	if(document.getElementById('invoiceEndDate').value.length==0){
		alert("Please select and end date");
			document.formValidate.invoiceEndDate.focus;
			return false;
	}
	
	return true;
}
</script>
<body>
<div id="main">
	<div id="header">
		<a href="index.html" class="logo"><img src="/trucking/img/logo.gif" width="118" height="62" alt="" /></a>
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
						<th class="full" colspan="5">Invoices Report</th>
					</tr>
					<tr>
						<td>
						<?
						$queryTerm = "select * from project";
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
						<td><input type='text' size='10px' id='invoiceStartDate' name='invoiceStartDate' /></td>
						<td><input type='text' size='10px' id='invoiceEndDate' name='invoiceEndDate' /></td>
						<td><input type='button' size='10px' id='preview' name='prevBtn' value='Preview' /></td>
						<td><input type='submit' size='10px' id='submit' name='subBtn' value='Submit' /></td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			<div class='table' id='reports' >
			<?
				$queryInvoices = "
				SELECT 
					invoiceId,
					projectId,
					projectName,
					invoiceDate,
					invoiceStartDate,
					invoiceEndDate,
					sum(itemCustomerCost*ticketAmount) as invoiceTotal
				FROM 
					invoice
				JOIN invoiceTicket using (invoiceId)
				JOIN ticket using (ticketId)
				JOIN project using (projectId)
				JOIN (select itemId, itemCustomerCost from item) as I using (itemId)
				";
				$invoices = mysql_query($queryInvoices,$conexion);
				$numInvoices = mysql_num_rows($invoices);
				if($numInvoices>0){
					echo "
					<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
					<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
						<table id='invoices' class='listing form'  cellpadding='0' cellspacing='0'>
							<tr>
								<th class='first' width='7%'>Job Id</th>
								<th width='29%'>Job Name</th>
								<th width='15%'>Invoice Date</th>
								<th width='15%'>Start </th>
								<th width='15%'>End</th>
								<th class='last' width='19%'>Total</th>
							</tr>
							<tbody>
						";
					$colorFlag=true;
						while($invoice = mysql_fetch_assoc($invoices)){
							if($colorFlag){echo "<tr id='invoice".$invoice['invoiceId']."'>";}
							else{echo "<tr class='bg' id='invoice".$invoice['invoiceId']."'>";}
							!$colorFlag;
							echo "<td class='first style2' width='7%'>".$invoice['projectId']."</td>";
							echo "<td width='29%'>".$invoice['projectName']."</td>";
							echo "<td width='15%'>".to_MDY($invoice['invoiceDate'])."</td>";
							echo "<td width='15%'>".to_MDY($invoice['invoiceStartDate'])."</td>";
							echo "<td width='15%'>".to_MDY($invoice['invoiceEndDate'])."</td>";
							echo "<td width='19%'>$".decimalPad($invoice['invoiceTotal'])."</td>";
							echo "</tr>";
							
						}echo "</tbody></table>";
				}
			?>
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
