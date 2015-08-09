<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['materialName']))
{
	$queryMaterial =
	"SELECT
		*
	FROM
		material
	WHERE
		materialName = '".$_GET['materialName']."'";
	$result = mysql_query($queryMaterial,$conexion);
	
	$row = mysql_fetch_assoc($result);
	
	$jsondata['materialName']=$row['materialName'];
	
	echo json_encode($jsondata);
}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();
//header ("Location: managementMaterials.php");


?>

