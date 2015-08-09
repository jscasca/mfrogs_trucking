<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['fakeprojectId']))
{
	$queryMaterials =
	"SELECT
		*
	FROM
		fakeproject
	JOIN address using (addressId)
	LEFT JOIN fakeitem ON fakeitem.projectId=fakeproject.fakeprojectId
	WHERE
		fakeprojectId = '".$_GET['fakeprojectId']."'
	ORDER BY
		itemNumber DESC
	LIMIT 1";
	$result = mysql_query($queryMaterials,$conexion);
	
	$row = mysql_fetch_assoc($result);
	if($row['itemNumber']!=null){$jsondata['itemNumber']=$row['itemNumber']+1;}
	else{$jsondata['itemNumber']=1;}
	
	$jsondata['addressLine1'] = $row['addressLine1'];
	$jsondata['addressId'] = $row['addressId'];
	
	/*while($row = mysql_fetch_assoc($result)){
		//$jsondata[$row['materialId']]=$row['materialName']."~".$row['supplierMaterialPrice']."~".$row['supplierMaterialLastModified'];
		$jsondata['itemNumber']=$row['materialName'];
	}*/
	
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

