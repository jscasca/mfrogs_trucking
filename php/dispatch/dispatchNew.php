<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Dispatch";
#################
$subtitle = "New Dispatch";
$description = "Create a new dispatch. A dispatch is used to identify how many trucks are already working on a job and which. Values marked with <span style='color:red;'>*</span> are mandatory.";

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
<script type="text/javascript">	
function validateForm(){

	if(document.getElementById('projectId').selectedIndex==0){
		alert("Select a project to continue");
		document.formValidate.projectId.focus
		return false;
	}
	
	if(document.getElementById('dispatchCount').value.length==0){
		alert("Please select the number of trucks");
		document.formValidate.dispatchCount.focus
		return false;
	}
	return true;
}

$(document).ready(function()
{
	
	$('#customerId').change(function() {
		var customer=this.value;
		getProjects(customer);
	});
	
});
function getProjects(customer){
	$.ajax({
		type: "GET",
		url: "getProjects.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var material=$('#projectId');
			material.children().remove();
			material.append("<option value='0' >--Select Project--</option>");
			jQuery.each(obj, function(i,val){
				material.append("<option value='"+i+"' >"+val+"</option>");
			});
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
				<a href="#" class="vendor"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitNew.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">New Dispatch</th>
					</tr>
<tr>
						<td class="first" width="172"><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryTerm0 = "select * from customer order by customerName asc";
						$terms0 = mysql_query($queryTerm0,$conexion);
						$countTerms0= mysql_num_rows($terms0);
						echo "<select name='customerId' id='customerId' style='font-family:verdana;font-size:8pt'>";
						if($countTerms0 > 0)
						{
							
							if(!isset($_GET['i']))
							{
								echo "<option selected='selected'>--Select Customer--</option>";
								while($term0=mysql_fetch_assoc($terms0))
								{
									echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
							else
							{	
								while($term0=mysql_fetch_assoc($terms0))
								{
									if($_GET['i']==$term0['customerId'])
										echo "<option selected='selected' value='{$term0['customerId']}'>{$term0['customerName']}</option>";
									else
										echo "<option value='{$term0['customerId']}'>{$term0['customerName']}</option>";
								}
							}
						}
								else
						{
							echo "<option selected='selected'>There are no customers in the DataBase</option>";
							
						}
						echo "</select>";
						?>
						</td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project:</strong><span style="color:red;">*</span></td>
						<td class="last">
						
						<?
						echo "<select name='projectId' id='projectId' style='font-family:verdana;font-size:8pt'>";
						echo "<option selected='selected' value=''>--Select project--</option>";
						echo "</select>";
						?>
						
						</td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Number of trucks:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='dispatchCount' name='dispatchCount'/></td>
					</tr>
					<tr>
						<td class="first"><strong>Date:</strong></td>
						<td class="last"><input type="text" class="text" id='dispatchDate' name='dispatchDate' /></td>
					</tr>
					<tr class="bg">
						<td class="first"><strong>Comment:</strong></td>
						<td class="last"><textarea rows="2" cols="43" class="text" id='dispatchComment' name='dispatchComment' /></textarea></td>
					</tr>
				</table>
				<table>
				<tr>
				<td><input type='reset'  value='Reset' ></td>
				<td><input type='submit' value='Submit' ></td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			<?
			
			$queryContacts = "select * from dispatch JOIN project using (projectId) LEFT JOIN (select count(*) as truckCount, dispatchId from truckdispatch group by dispatchId) as td using (dispatchId) order by dispatchId desc";
			$terms = mysql_query($queryContacts,$conexion);
			$numTerms = mysql_num_rows($terms);
			if($numTerms>0)
			{
				echo "<div class='table'>";
				echo "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				echo "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				echo "<table class='listing' cellpadding='0' cellspacing='0'>";
				echo "<tr>
						<th class='first' width='177'>Project</th>
						<th>Date</th>
						<th>Trucks Needed</th>
						<th>Trucks in</th>
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
						<td class='first style2'>".$term['projectName']."</td>
						<td class='first style2'>".to_MDY($term['dispatchDate'])."</td>
						<td class='first style2'>".$term['dispatchCount']."</td>
						<td class='first style2'>".($term['truckCount']==null?"0":$term['truckCount'])."</td>
						<td class='last'><a onclick=\"return confirm('Are you sure you want to delete this dispatch?');\" href='deleteDispatch.php?i=".$term['dispatchId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
				}
				
				
				echo "</table>";
				echo "</div>";
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
