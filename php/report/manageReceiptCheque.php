<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

$invoiceId = $_GET['invoiceId'];

$customerId = $_GET['customerId'];
$projectId = $_GET['projectId'];
$afterDate = $_GET['afterDate'];
$beforeDate = $_GET['beforeDate'];
$week = $_GET['week'];
$paid = $_GET['paid'];
$invoiceNum = $_GET['invoiceNum'];

$additionalParams = "?customerId=$customerId&projectId=$projectId&afterDate=$afterDate&beforeDate=$beforeDate&beforeEndDate=$beforeEndDate&week$week=&paid=$paid&invoiceNum=$invoiceNum";

#################
$title = "Invoice Pay Cheque";
#################
$subtitle = "Pay Cheque";
$description = "Remove existing pay cheques from this invoice.";

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

###############Next Autoincrement#########
/*$next_inc=0;
$showstatus="show table status like 'paidcheques'";
$status=mysql_query($showstatus,$conexion);
$row=mysql_fetch_assoc($status);
$next_inc=$row['Auto_increment'];*/
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
$(document).ready(function(){
	$(".deletable").click(function(){
		console.log($(this));
		var id = $(this).attr("valor");
		removePayCheque(id);
	});
});

function removePayCheque(paidId){
	$.ajax({
		type: "GET",
		url: "removeReceiptCheque.php",
		data: "i="+paidId,
		success:function(data){
			$("#receiptcheque"+data).remove();
			//var obj=jQuery.parseJSON(data);
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
		
		
<div class="top-bar">
				<a href="reportInvoice.php<?echo $additionalParams;?>" class='returnLink'>Back<img src='/trucking/img/72.png' width='32px' height='32px' /></a>
				<a href="#" class="shellproject"></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Invoices Cheques</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitPayCheque.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="5" height="4" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="4" height="4" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="4">Pay Cheques available for Invoice <?echo $invoiceId;?></th>
					</tr>
					<tr>
						<th>Number</th>
						<th>Date</th>
						<th>Amount</th>
						<th>Remove</th>
					</tr>
					<?
					$cheques = mysql_query("select * from receiptcheques where invoiceId = $invoiceId", $conexion);
					$flag = true;
					while($cheque = mysql_fetch_assoc($cheques)){
						$flag = !$flag;
						echo "<tr ".($flag?"class='bg'":"")." id='receiptcheque".$cheque['receiptchequesId']."'>";
						echo "<td>".$cheque['receiptchequeNumber']."</td>";
						echo "<td>".to_MDY($cheque['receiptchequesDate'])."</td>";
						echo "<td>".decimalPad($cheque['receiptchequesAmount'])."</td>";
						echo "<td><img src='/trucking/img/118.png' width='20' height='20' class='deletable' valor='".$cheque['receiptchequesId']."' /></td>";
						echo "</tr>";
					}
					?>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
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
