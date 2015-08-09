<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Management";
#################
$subtitle = "Local Information";
$descriptionTitle = "Local tax and deduction Information.";
$description = "Edit local information. This information is used to calculates taxes and deductions.";

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

###############To Edit###############
$queryLast =
	"SELECT 
		*
	FROM
		stateinfo";
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);
##########################################

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?echo$title." -".$subtitle;?></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<link rel="shortcut icon" href="/trucking/img/favicon.ico" type="image/x-icon" />
	<style media="all" type="text/css">@import "../../css/all.css";</style>
<script type="text/javascript">	
function validateForm(){
	if(document.getElementById('mfiPass').value!=document.getElementById('mfiPassConfirm').value ){
		alert('Both Passwords must be the same');
		document.formValidate.mfiPass.focus;
		return false;
	}
	return true;
}
</script>
</head>
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
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2"><?echo $descriptionTitle ;?></th>
					</tr>
					<tr>
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitInformation.php?i=<?echo$lastVal['addressId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Local Information</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>SS Security:</strong></td>
						<td class="last"><input <?if(isset($lastVal['sssec']))echo"value='".decimalPad($lastVal['sssec'])."'";?> type="text" class="text" id='sssec' name='sssec'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Medicare:</strong></td>
						<td class="last"><input <?if(isset($lastVal['medicare']))echo"value='".decimalPad($lastVal['medicare'])."'";?> type="text" class="text" id='medicare' name='medicare' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>State w/h tax:</strong></td>
						<td class="last"><input <?if(isset($lastVal['withHoldingTax']))echo"value='".decimalPad($lastVal['withHoldingTax'])."'";?> type="text" class="text" id='withHoldingTax' name='withHoldingTax'/></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Federal w/h tax:</strong></td>
						<td class="last"><input <?if(isset($lastVal['fed']))echo"value='".decimalPad($lastVal['fed'])."'";?> type="text" class="text" id='fed' name='fed'/></td>
					</tr>
					<tr>
						<td class="first"><strong>Other:</strong></td>
						<td class="last"><input <?if(isset($lastVal['other']))echo"value='".decimalPad($lastVal['other'])."'";?> type="text" class="text" id='other' name='other' /></td>
					</tr>
					
					<tr class="bg">
						<td class="first" width="172"><strong>Per W/H Allowance Weekly:</strong></td>
						<td class="last"><input <?if(isset($lastVal['allowanceWeekly']))echo"value='".decimalPad($lastVal['allowanceWeekly'])."'";?> type="text" class="text" id='allowanceWeekly' name='allowanceWeekly'/></td>
					</tr>
					
						<tr class="bg">
						<td class="first" width="172"><strong>Hourly rate:</strong>(only for estimates)</td>
						<td class="last"><input <?if(isset($lastVal['hourlyRate']))echo"value='".decimalPad($lastVal['hourlyRate'])."'";?> type="text" class="text" id='hourlyRate' name='hourlyRate'/></td>
					</tr>
					
				</table>
				<table>
				<tr>
				<td><input type='reset' ></td>
				<td><input type='submit' ></td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
			
		  <!--<div class="table">
				<img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Header Here</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr>
						<td class="first""><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Lorem Ipsum</strong></td>
						<td class="last"><input type="text" class="text" /></td>
					</tr>
				</table>
	        <p>&nbsp;</p>
		  </div>-->
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
