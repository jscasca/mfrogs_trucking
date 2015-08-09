<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['vendorId']))
{
	$queryTruckFeature =
	"SELECT
		*
	FROM
		supplier
	WHERE
		vendorId = '".$_GET['vendorId']."'";
	$result = mysql_query($queryTruckFeature,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		$jsondata[$row['supplierId']]=$row['supplierName'];
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

