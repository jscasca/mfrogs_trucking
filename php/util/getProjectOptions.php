<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['type']) && isset($_GET['customerId']))
{
$customer=$_GET['customerId'];
	
	if($_GET['type']=='0'){//Projects
		$queryProjects = "SELECT projectId as id, projectName as name FROM project WHERE customerId = ".$customer." and projectInactive=0 order by name asc";
	}else{
		$queryProjects = "SELECT fakeprojectId as id, fakeprojectName as name FROM fakeproject  WHERE customerId = ".$customer." order by name asc";
	}
	
	$result = mysql_query($queryProjects,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata[$row['id']]=$row['name'];
	}
	
	echo json_encode($jsondata);
}

mysql_close();


?>

