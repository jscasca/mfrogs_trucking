<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Customer_Balance";
$description = "Customer Balacne overview.";

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
	<style media="all" type="text/css">@import "../../css/all.css";</style>
</head>
<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
var patternLetters = new RegExp(/driver/);
patternLetters.compile(patternLetters);

$(document).ready(function() {
	$('#customers tr td').dblclick(function() {
		var customerId = $(this).closest('tr').attr('customerId');
		showComplete(customerId);
	});
});

function showComplete(customerId) {
	window.location.href = '../new/newCustomer_Balance.php?c=' + customerId;
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
			
		<?
		}
		else
		{
		?>	
			<!--<div class='table' id='searchBar'>
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
				<tr>
					<th class="full"  colspan='5'>Search Drivers</th>
				</tr>
				<tr>
					<td>Broker</td>
					<td>Unique Identifier</td>
					<td>Name</td>
					<td>Status</td>
				</tr>
				<tr>
					<td class="last"></td>
					<td><input type='text' size='8px' id='brokerPid' name='brokerPid' /></td>
					<td><input type='text' size='8px' id='driverName' name='driverName' /></td>
					<td>
							<select name='driverStatus' id='driverStatus' style='font-family:verdana;font-size:8pt' >
								<option value='0' >All</option>
								<option value='1' >Active</option>
								<option value='2' >Inactive</option>
							</select>
						</td>
				</tr>
				</table>
			</div>-->
			
		<div class='table' >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id='customers'>
				<tr>
					<th class="full"  colspan='9'>Balance</th>
				</tr>
				<tr>
					<th>Customer</th>
					<th>Balance</th>
				</tr>
				<tbody>
				<?
				$customers = mysql_query("select * from customer JOIN term using (termId) order by customerName", $conexion);
				$customerBalance = array();
				while($customer = mysql_fetch_assoc($customers)) {
					$invoicesQuery = "
						SELECT
							invoiceId,
							invoiceDate,
							project.projectId,
							projectName,
							SUM(ticketAmount * itemCustomerCost) as totalAmount
						FROM
							project
							JOIN invoice using (projectId)
							JOIN invoiceticket using (invoiceId)
							JOIN ticket using (ticketId)
							JOIN item using (itemId)
						WHERE
							customerId = ".$customer['customerId']."
						GROUP BY
							invoiceId
						ORDER BY
							projectId,
							invoiceId
					";
					$invoices = mysql_query($invoicesQuery, $conexion);
					while($invoice = mysql_fetch_assoc($invoices)) {
						$paidInfo = mysql_fetch_assoc(mysql_query("SELECT COALESCE(SUM(receiptchequesAmount),0) as totalPaid, count(*) as totalCheques FROM receiptcheques WHERE invoiceId = ".$invoice['invoiceId'],$conexion));
						$invoiceAmount = decimalPad($invoice['totalAmount']);
						$invoicePaid = decimalPad($paidInfo['totalPaid']);
						$invoiceBalance = decimalFill(decimalPad($invoiceAmount - $invoicePaid));
						if($invoiceBalance == 0) continue;
						if(isset($customerBalance[$customer['customerId']])) {
							$customerBalance[$customer['customerId']]['balance'] = $invoiceBalance + $customerBalance[$customer['customerId']]['balance'];
						} else {
							$customerBalance[$customer['customerId']]['balance'] = $invoiceBalance;
							$customerBalance[$customer['customerId']]['name'] = $customer['customerName'];
						}
					}
				}
				$odd = true;
				foreach($customerBalance as $customerId=>$balanceInfo) {
					$class = "Warning";
					if($balanceInfo['balance'] > 0) { $class = 'Unpaid';}
					if($balanceInfo['balance'] < 0) { $class = 'Paid';}
					echo "<tr class='".($odd ? "odd" : "even")."$class' customerId='$customerId'>
						<td class='last'>".$balanceInfo['name']."</td>
						<td aling='right'>".decimalPad($balanceInfo['balance'],2)."</td>
					</tr>";
					$odd = !$odd;
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
