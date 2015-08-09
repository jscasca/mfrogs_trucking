<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Management";
#################
$subtitle = "Terms";
$description = "Add the terms that can be agreed with the customers or the brokers, the name of the term is the name that will appear when filling information forms; The value stands for the number of days in which to pay that the name represent";

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
<script type="text/javascript">	
function validateForm(){

	if(document.getElementById('termName').value.length==0){
		alert("Please type a name for the term");
		document.formValidate.termName.focus
		return false;
	}
	if(document.getElementById('termValue').value.length==0 || isNaN(document.getElementById('termValue').value)){
		alert("Please type a value in days for the term");
		document.formValidate.termValue.focus
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
			<!--<div class="top-bar">
				<a href="#" class="button">ADD NEW </a>
				<h1>Contents</h1>
				<div class="breadcrumbs"><a href="#">Homepage</a> / <a href="#">Contents</a></div>
			</div><br />-->
		  <!--<div class="select-bar">
		    <label>
		    <input type="text" name="textfield" />
		    </label>
		    <label>
			<input type="submit" name="Submit" value="Search" />
			</label>
		  </div>-->
			<!--<div class="table">
				<img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing" cellpadding="0" cellspacing="0">
					<tr>
						<th class="first" width="177">Header Here</th>
						<th>Header</th>
						<th>Head</th>
						<th>Header</th>
						<th>Header</th>
						<th>Head</th>
						<th>Header</th>
						<th class="last">Head</th>
					</tr>
					<tr>
						<td class="first style1">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style2">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style3">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style1">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style2">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr class="bg">
						<td class="first style3">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
					<tr>
						<td class="first style4">- Lorem Ipsum </td>
						<td><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
						<td><img src="img/hr.gif" width="16" height="16" alt="" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td><img src="img/edit-icon.gif" width="16" height="16" alt="edit" /></td>
						<td><img src="img/login-icon.gif" width="16" height="16" alt="login" /></td>
						<td><img src="img/save-icon.gif" width="16" height="16" alt="save" /></td>
						<td class="last"><img src="img/add-icon.gif" width="16" height="16" alt="add" /></td>
					</tr>
				</table>
				<div class="select">
					<strong>Other Pages: </strong>
					<select>
						<option>1</option>
					</select>
			  </div>
			</div>-->
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
			<form id="formValidate" name="formValidate" method="POST" action="submitTerm.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Term</th>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Name:</strong></td>
						<td class="last"><input type="text" class="text" id='termName' name='termName'/></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Value in days:</strong></td>
						<td class="last"><input type="text" class="text" id='termValue' name='termValue' /></td>
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
			
			$queryTerms = "select * from term";
			$terms = mysql_query($queryTerms,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Name</th>
						<th>Value in days</th>
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
						<td class='first style2'>".$term['termName']."</td>
						<td class='first style2'>".$term['termValue']."</td>
						<td class='last'><a href='editTerm.php?i=".$term['termId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ".$term['termName']."?');\" href='deleteTerm.php?i=".$term['termId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
			}
			
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
