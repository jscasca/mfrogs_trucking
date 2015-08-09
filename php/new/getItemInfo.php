<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['itemId']))
{
	$queryTruckFeature =
	"SELECT
		*
	FROM
		item
	JOIN material using(materialId)
	JOIN (select addressId as fromAddressId, addressLine1 as fromAddress from address) as F using (fromAddressId)
	JOIN (select addressId as toAddressId, addressLine1 as toAddress from address) as T using (toAddressId)
	WHERE
		itemId = '".$_GET['itemId']."'";
	$result = mysql_query($queryTruckFeature,$conexion);
	
	$row = mysql_fetch_assoc($result);
		$jsondata['itemInfo'] = ($row['itemDescription']==""?"": $row['itemDescription']." -- ")."Moving ".$row['materialName']." from ".$row['fromAddress']." to ".$row['toAddress'].".";
		//$jsondata['itemInfo'] = "Moving  from  to ";
		$jsondata['amounts'] = $LTH[$row['itemType']];
		$jsondata['itemDescription'] = $row['itemDescription'];
	
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

