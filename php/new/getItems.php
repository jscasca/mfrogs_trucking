<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['projectId']))
{
	$queryItems =
	"SELECT
		*
	FROM
		item
	JOIN material using (materialId) 
	JOIN supplier using (supplierId)
	WHERE
		projectId = '".$_GET['projectId']."'";
	$result = mysql_query($queryItems,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		//$jsondata[$row['itemId']]=$row['itemNumber'];
		$jsondata[$row['itemId']]=$row['materialName']." to ".$row['supplierName']." @@  MC (".decimalPad($row['itemMaterialPrice']).") CC (".decimalPad($row['itemCustomerCost']).") BC (".decimalPad($row['itemBrokerCost']).")";
	}
	/**//*
		$queryItems =
	"SELECT
		*
	FROM
		item
	JOIN material using (materialId) 
	JOIN (SELECT 	supplierId,
								supplierName,
								addressId as sai,
								addressLine1 as toAddress
							FROM
								supplier
							JOIN address using (addressId) ) as SA using (supplierId)
	WHERE
		SA.sai = item.fromAddressId and
		projectId = '".$_GET['projectId']."'";
	$result = mysql_query($queryItems,$conexion);
	
	while($row = mysql_fetch_assoc($result)){
		//$jsondata[$row['itemId']]=$row['itemNumber'];
		$jsondata[$row['itemId']]=$row['materialName']." from ".$row['supplierName']." ##  MC (".decimalPad($row['itemMaterialPrice']).") CC (".decimalPad($row['itemCustomerCost']).") BC (".decimalPad($row['itemBrokerCost']).")";
	}
	*/
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

