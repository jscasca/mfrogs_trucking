<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['customerId']))
{
	$queryTrucks =
	"SELECT
		*
	FROM
		project
		JOIN address USING (addressId)
	WHERE
	 projectInactive=0 
	 AND
		customerId = '".$_GET['customerId']."'
	ORDER BY
		projectId desc";
	$result = mysql_query($queryTrucks,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata[$row['projectId']]=$row['projectId']." - ".$row['projectName']." / ".$row['addressLine1'];
	}
	
	echo json_encode($jsondata);
}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

