<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Management";
#################
$subtitle = "MFI Info";
$descriptionTitle = "Martinez Frogs Inc. Information.";
$description = "Edit the company's information. This information is displayed on invoices. The mail and password are used to send emails and texts.";

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
		mfiinfo
	JOIN
		address
	ON
		(address.addressId=mfiinfo.addressId)";
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
			<form id="formValidate" name="formValidate" method="POST" action="submitMFI.php?i=<?echo$lastVal['addressId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Martinez Frogs Inc. Info</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Phone Number:</strong></td>
						<td class="last"><input <?if(isset($lastVal['mfiTel']))echo"value='".showPhoneNumber($lastVal['mfiTel'])."'";?> type="text" class="text" id='mfiTel' name='mfiTel'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Fax Number</strong></td>
						<td class="last"><input <?if(isset($lastVal['mfiFax']))echo"value='".showPhoneNumber($lastVal['mfiFax'])."'";?> type="text" class="text" id='mfiFax' name='mfiFax' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>E-mail address</strong></td>
						<td class="last"><input <?if(isset($lastVal['mfiMail']))echo"value='".$lastVal['mfiMail']."'";?> type="text" class="text" id='mfiMail' name='mfiMail'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Password:</strong></td>
						<td class="last"><input <?if(isset($lastVal['mfiPass']))echo"value='".$lastVal['mfiPass']."'";?> type="password" class="text" id='mfiPass' name='mfiPass' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Password Confirm:</strong></td>
						<td class="last"><input <?if(isset($lastVal['mfiPass']))echo"value='".$lastVal['mfiPass']."'";?> type="password" class="text" id='mfiPassConfirm' name='mfiPassConfirm'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?> type="text" class="text" id='addressLine1' name='addressLine1' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?> type="text" class="text" id='addressLine2' name='addressLine2'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input <?if(isset($lastVal['addressCity']))echo"value='".$lastVal['addressCity']."'";?> type="text" class="text" id='addressCity' name='addressCity' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from state";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='addressState' id='addressState' style='font-family:verdana;font-size:8pt'>";
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['addressState'])&&($lastVal['addressState']==$state['stateId']))
								echo "<option selected='selected' value='{$state['stateId']}'>{$state['stateName']}</option>";
							echo "<option value='{$state['stateId']}'>{$state['stateName']}</option>";
						}
						echo "</select>";
						?>
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?> type="text" class="text" id='addressZip' name='addressZip' /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?> type="text" class="text" id='addressPOBox' name='addressPOBox'/></td>
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
