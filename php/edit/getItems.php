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
	JOIN (SELECT 	supplierId,
								supplierName,
								addressId as sai,
								addressLine1 as toAddress
							FROM
								supplier
							JOIN address using (addressId) ) as SA using (supplierId)
	WHERE
		SA.sai = item.toAddressId and
		projectId = '".$_GET['projectId']."'";
	$result = mysql_query($queryItems,$conexion);
	
	while($item = mysql_fetch_assoc($result)){
		//$jsondata[$row['itemId']]=$row['itemNumber'];
		$jsondata[$item['itemId']]= "(".$item['itemNumber'].") ".$item['materialName']." to ".$item['supplierName'];
	}
	
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
	
	while($item = mysql_fetch_assoc($result)){
		//$jsondata[$row['itemId']]=$row['itemNumber'];$option = "(".$item['itemNumber'].") ".$item['materialName']." from/to ".$item['supplierName'];
		$jsondata[$item['itemId']]= "(".$item['itemNumber'].") ".$item['materialName']." from ".$item['supplierName'];
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

