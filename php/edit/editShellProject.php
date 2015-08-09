<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "Edit";
#################
$subtitle = "Shell Project";
$description = "Edit  Shell job/project. Values marked with <span style='color:red;'>*</span> are mandatory.";

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

if(isset($_GET['i']))
{
###############To Edit###############
$queryLast =
	"SELECT 
		* 
	FROM
 		fakeproject
	JOIN address USING (addressId)
	LEFT JOIN (SELECT customerId, customerName FROM customer) as c USING (customerId)
	WHERE
		fakeprojectId=".$_GET['i'];
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);
##########################################
}

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
var patternLetters = new RegExp(/fakeproject/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('#customer').change(function() {
		var customer=this.value;
		getContacts(customer);
	});
	
	$('#projects tr td').live('dblclick',function(){
		var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./editShellProject.php?i="+id);
			}
	});
	
	$('#searchId').keyup(function(){getProjects();});
	$('#searchName').keyup(function(){getProjects();});
	$('#searchAddress').keyup(function(){getProjects();});
	$('#searchCity').keyup(function(){getProjects();});
});

function getProjects(){
	var projectId=$('#searchId').val();
	var projectName=$('#searchName').val();
	var addressLine=$('#searchAddress').val();
	var addressCity=$('#searchCity').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getEditShellProjects.php",
		data: "fakeprojectId="+projectId+"&fakeprojectName="+projectName+"&addressLine="+addressLine+"&addressCity="+addressCity,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			console.log(obj);
			if(obj.error==null){
				if(obj.table!=null){
					$('#projects > tbody:last').remove();
					$('#projects').append(obj.table);
					//$($('#projects tbody')[1]).remove();
					//$('#projects').append(obj.table);
				}
			}else{alert('Error: '+obj.error);}
		},
		async: false
	});
}
function getContacts(customer){
	$.ajax({
		type: "GET",
		url: "getContacts.php",
		data: "customerId="+customer,
		success:function(data){
			var obj=jQuery.parseJSON(data);
			var contacts=$('#contact');
			contacts.children().remove();
			jQuery.each(obj, function(i,val){
				contacts.append("<option value='"+i+"' >"+val+"</option>");
			});
		},
		async: false
	});
}
function validateForm(){

	if(document.getElementById('fakeprojectName').value.length==0){
		alert("Please type a name for this shellproject");
		document.formValidate.projectName.focus;
		return false;
	}
	if(document.getElementById('customer').selectedIndex==0 ){
		alert("Please select a customer for this contact");
			document.formValidate.customer.focus
			return false;
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
		<?
		if(isset($_GET['i']))
		{
		?>
		
			<div class="top-bar">
				<a href="#" class="truck"></a>
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
			<form id="formValidate" name="formValidate" method="POST" action="submitEditShellProject.php?a=<?echo$lastVal['addressId'];?>&i=<?echo$lastVal['fakeprojectId'];?>" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Edit ShellProject</th>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Project Number:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" disabled <?if(isset($lastVal['fakeprojectId']))echo"value='".$lastVal['fakeprojectId']."'";?> /></td>
					</tr>
					<tr>
						<td class="first" width="172"><strong>Project Name:</strong><span style="color:red;">*</span></td>
						<td class="last"><input type="text" class="text" id='fakeprojectName' name='fakeprojectName' <?if(isset($lastVal['fakeprojectName']))echo"value='".$lastVal['fakeprojectName']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>Address Line:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine1' name='addressLine1'  <?if(isset($lastVal['addressLine1']))echo"value='".$lastVal['addressLine1']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>Line 2:</strong></td>
						<td class="last"><input type="text" class="text" id='addressLine2' name='addressLine2'  <?if(isset($lastVal['addressLine2']))echo"value='".$lastVal['addressLine2']."'";?> /></td>
					</tr>
					<tr>
						<td class="first"><strong>City:</strong></td>
						<td class="last"><input type="text" class="text" id='addressCity' name='addressCity'  <?if(isset($lastVal['addressCity']))echo"value='".$lastVal['addressCity']."'";?> /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>State:</strong></td>
						<td class="last">
						<?
						$queryState = "select * from state";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='addressState' id='addressState' style='font-family:verdana;font-size:8pt'>";
						echo "<option >--Select State--</option>";
						while($state=mysql_fetch_assoc($states))
						{
							if(isset($lastVal['addressState'])&& $lastVal['addressState'] ==$state['stateId'])
								echo "<option selected='selected' value='{$state['stateId']}'>{$state['stateName']}</option>";
							else
								echo "<option value='{$state['stateId']}'>{$state['stateName']}</option>";
						}
						echo "</select>";
						?>
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr>
						<td class="first"><strong>Zip:</strong></td>
						<td class="last"><input type="text" class="text" id='addressZip' name='addressZip'  <?if(isset($lastVal['addressZip']))echo"value='".$lastVal['addressZip']."'";?>  /></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172"><strong>P.O.Box:</strong></td>
						<td class="last"><input type="text" class="text" id='addressPOBox' name='addressPOBox'  <?if(isset($lastVal['addressPOBox']))echo"value='".$lastVal['addressPOBox']."'";?>/></td>
					</tr>
					<tr class='bg'>
						<td class="first" width="172"><strong>Customer:</strong><span style="color:red;">*</span></td>
						<td class="last">
						<?
						$queryState = "select * from customer";
						$states = mysql_query($queryState,$conexion);
						echo "<select name='customer' id='customer' style='font-family:verdana;font-size:8pt'>";
						if(isset($lastVal['customerId'])&&$lastVal['customerName']!=null)
						{
							echo "<option value='0'>--Select customer--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								if($state['customerId']==$lastVal['customerId'])
									echo "<option selected='selected'  value='{$state['customerId']}'>{$state['customerName']}</option>";
								else
									echo "<option value='{$state['customerId']}'>{$state['customerName']}</option>";
							}
						}
						else
						{
							echo "<option selected='selected' value='0'>--Select customer--</option>";
							while($state=mysql_fetch_assoc($states))
							{
								echo "<option value='{$state['customerId']}'>{$state['customerName']}</option>";
							}
						}
						echo "</select>";
						?>
						</td>
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
		}
		else
		{
		?>
			<div class="table" id="search-bar">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
					<tr>
						<th class="full" colspan="5">Search ShellProject</th>
					</tr>
					<tr class="bg">
						<td>Id</td>
						<td>Name</td>
						<td>Address</td>
						<td>City</td>
					</tr>
					<tr>
						<td><input type="text"  size='8px' name="searchId" id="searchId" /></td>
						<td><input type="text"  size='8px' name="searchName" id="searchName" /></td>
						<td><input type="text"  size='8px' name="searchAddress" id="searchAddress" /></td>
						<td><input type="text"  size='8px' name="searchCity" id="searchCity" /></td>
					</tr>
				</table>
			</div>
			
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" id="projects">
					<tr><th class="full" colspan="4" >ShellProjects</th></tr>
					<tr>
						<th>Id</th><th>Name</th>
						<th>Id</th><th>Name</th>
					</tr>
					<tbody>
					<?
					$queryProjects = "select * from fakeproject order by fakeprojectName";
					$projects = mysql_query($queryProjects,$conexion);
					$numProjects = mysql_num_rows($projects);
					$first =true;
					$class = " class='bg' ";
					while($project = mysql_fetch_assoc($projects)){
						if($first){
							echo "<tr>";
							echo "<td $class>".$project['fakeprojectId']."</td>";
							echo "<td id='fakeproject".$project['fakeprojectId']."' $class>".$project['fakeprojectName']."</td>";
							if($class=="")$class=" class='bg' ";
							else $class="";
						}else{
							echo "<td $class>".$project['fakeprojectId']."</td>";
							echo "<td id='project".$project['fakeprojectId']."' $class>".$project['fakeprojectName']."</td>";
							echo "</tr>";
						}
						$first = !$first;
					}
					if(!$first){echo "<td colspan='2' $class></td></tr>";}
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
