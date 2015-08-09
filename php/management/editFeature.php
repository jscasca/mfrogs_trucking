<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Management";
#################
$subtitle = "Edit Truck Feature";
$description = "Add features to group truck search engine";

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
		feature
	WHERE
		featureId=".$_GET['i'];
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

	if(document.getElementById('truckFeatureName').value.length==0){
		alert("Please type a name for the feature");
		document.formValidate.truckFeatureName.focus
		return false;
	}
	if(document.getElementById('truckFeatureDescription').value.length==0 ){
		
		if(confirm("Are you sure you don't want to type a description?"))
			return true;
		else{
			document.formValidate.truckFeatureDescription.focus
			return false;
		}
	}
	return true;
}
</script>
</head>
<body>
<div id="main">
	<div id="header">
		<a href="index.html" class="logo"><img src="../../img/logo.gif" width="118" height="62" alt="" /></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditFeature.php?i=<?echo$_GET['i'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Edit Truck Feature</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong></td>
						<td class="last"><input <?if(isset($lastVal['featureName']))echo"value='".$lastVal['featureName']."'";?> type="text" class="text" id='truckFeatureName' name='truckFeatureName'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Short Description:</strong></td>
						<td class="last"><input <?if(isset($lastVal['featureDescription']))echo"value='".$lastVal['featureDescription']."'";?> type="text" class="text" id='truckFeatureDescription' name='truckFeatureDescription' /></td>
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
			
			<?
			/*
			$queryTruckFeatures = "select * from truckFeature";
			$terms = mysql_query($queryTruckFeatures,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Name</th>
						<th>Description</th>
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
						<td class='first style2'>".$term['truckFeatureName']."</td>
						<td class='first style2'>".$term['truckFeatureDescription']."</td>
						<td class='last'><a href='editTruckFeature.php?i=".$term['truckFeatureId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ".$term['truckFeatureName']."?');\" href='deleteTruckFeature.php?i=".$term['truckFeatureId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
			}
			*/
			?>
			
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
