<?php

include("../conexion.php");
include("../commons.php");
include("../password.php");

#################
$title = "View";
#################
$subtitle = "Shell Project";

$description = "
	View the information of Project Estimates. <br/>
	Click (<img src='/trucking/img/95.png' width='14px' height='14px' />) to add an item proposal. <br/>
	Click (<img src='/trucking/img/2.png' width='14px' height='14px' />) to view the estimate in a new page to print. <br/>
	Click (<img src='/trucking/img/74.png' width='14px' height='14px' />) to email the estimate. <br/>
	Click (<img src='/trucking/img/34.png' width='14px' height='14px' />) to import the estimate into a new project. <br/>
";

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
###############Show Customer###############
$queryLast =
	"SELECT 
		* 
	FROM
 		fakeproject
	JOIN address USING (addressId)
	LEFT JOIN customer USING (customerId)
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
var patternLetters = new RegExp(/project/);
patternLetters.compile(patternLetters);

$(document).ready(function()
{
	$('.clickable').click(function() {
		$(this).parents('table').find('.displayable').toggle();
	});
	
	$('#customer').change(function() {
		var customer=this.selectedIndex;
		getContacts(customer);
	});
	
		$('#projects tr td').live('dblclick',function(){
		var id=$(this).attr('id');
			if(id!=undefined){
				id=id.replace(patternLetters,'');
				window.location.replace("./viewEstimate.php?i="+id);
				
			}
	});
	
	$('#searchId').keyup(function(){getProjects();});
	$('#searchName').keyup(function(){getProjects();});
	$('#searchAddress').keyup(function(){getProjects();});
	$('#searchCity').keyup(function(){getProjects();});
});
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

function getProjects(){
	var fakeprojectId=$('#searchId').val();
	var fakeprojectName=$('#searchName').val();
	var addressLine=$('#searchAddress').val();
	var addressCity=$('#searchCity').val();
	//alert(brokerStatus);
	$.ajax({
		type: "GET",
		url: "getViewShellProjects.php",
		data: "fakeprojectId="+fakeprojectId+"&fakeprojectName="+fakeprojectName+"&addressLine="+addressLine+"&addressCity="+addressCity,
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

function validateForm(){

	if(document.getElementById('fakeprojectName').value.length==0){
		alert("Please type a name for this project");
		document.formValidate.fakeprojectName.focus;
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
				<a href="/trucking/php/new/newProposal.php?p=<?echo$lastVal['fakeprojectId'];?>" >
					<img src="/trucking/img/95.png" title='Add Proposal'>
				</a>
				<a href="javascript:void(0)" onclick="window.open('/trucking/php/view/showFakeProject.php?i=<?echo$_GET['i'];?>','Purchase Order')">
					<img src="/trucking/img/2.png" title='View Proposal'>
				</a>
				<a href="#">
					<img src="/trucking/img/74.png" title='Email Proposal'>
				</a>
				<a href="/trucking/php/view/ShellProjectToJob.php?i=<?echo$_GET['i'];?>" >
					<img src="/trucking/img/34.png" title='Import Estimate'>
				</a>
			
				<a onclick="return confirm('Are you sure you want to delete <?echo$lastVal['fakeprojectName'];?> and all its items (proposals)?');"  href="deleteEstimate.php?i=<?echo$_GET['i'];?>" class="delete" ></a>
				<a href="/trucking/php/edit/editEstimate.php?i=<?echo$_GET['i'];?>" class="edit" ></a>
			</div><br />
			<div class="table">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr class='clickable'>
						<th class="full" colspan="2">Description</th>
					</tr>
					<tr class='displayable' style="display:none;" >
						<td class="first" width="172"><strong><?echo $description ;?></strong></td>
					</tr>
				</table>
			</div>	
			
			<div class="table">
			<form id="formValidate" name="formValidate" method="POST" action="submitShellProject.php" onsubmit="return validateForm();" >
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0">
					<tr>
						<th class="full" colspan="2">Estimate</th>
					</tr>
					<tr>
						<td class="first" width="172">Estimate Id:<span style="color:red;">*</span></td>
						<td class="last"><strong><?if(isset($lastVal['fakeprojectId']))echo$lastVal['fakeprojectId'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Project Name:<span style="color:red;">*</span></td>
						<td class="last"><strong><?if(isset($lastVal['fakeprojectName']))echo$lastVal['fakeprojectName'];?></strong></td>
					</tr>
					<tr>
						<td class="first">Address Line:</td>
						<td class="last"><strong><?if(isset($lastVal['addressLine1']))echo$lastVal['addressLine1'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">Line 2:</td>
						<td class="last"><strong><?if(isset($lastVal['addressLine2']))echo$lastVal['addressLine2'];?></strong></td>
					</tr>
					<tr>
						<td class="first">City:</td>
						<td class="last"><strong><?if(isset($lastVal['addressCity']))echo$lastVal['addressCity'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">State:</td>
						<td class="last">
						<strong>
						<?
						if(isset($lastVal['addressState']))echo$lastVal['addressState'];
						?>
						</strong>
						<!--<input type="text" class="text" id='termName' name='termName'/>-->
						</td>
					</tr>
					<tr>
						<td class="first">Zip:</td>
						<td class="last"><strong><?if(isset($lastVal['addressZip'])&&$lastVal['addressZip']!=0)echo$lastVal['addressZip'];?></strong></td>
					</tr>
					<tr class="bg">
						<td class="first" width="172">P.O.Box:</td>
						<td class="last"><strong><?if(isset($lastVal['addressPOBox']))echo$lastVal['addressPOBox'];?></strong></td>
					</tr>

					<tr class='bg'>
						<td class="first" width="172">Customer:</td>
						<td class="last">
						<strong>
						<?if(isset($lastVal['customerName'])&&$lastVal['customerName']!=null)echo$lastVal['customerName'];?>
						</strong>
						</td>
					</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>
			
		<?
		}else{
		?>	
			<div class="table" id="search-bar">
				<img src="/trucking/img/bg-th-left.gif" width="8" height="7" alt="" class="left" />
				<img src="/trucking/img/bg-th-right.gif" width="7" height="7" alt="" class="right" />
				<table class="listing form" cellpadding="0" cellspacing="0" >
					<tr>
						<th class="full" colspan="5">Search Shell Project</th>
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
							echo "<td id='project".$project['fakeprojectId']."' $class>".$project['fakeprojectName']."</td>";
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
