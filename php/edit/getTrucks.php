<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['brokerId']))
{
	$queryTrucks =
	"SELECT
		*
	FROM
		truck
	WHERE
		brokerId = '".$_GET['brokerId']."'";
	$result = mysql_query($queryTrucks,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata[$row['truckId']]=$row['truckNumber'];
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

