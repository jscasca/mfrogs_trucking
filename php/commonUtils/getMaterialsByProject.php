<?

if(isset($_GET['projectId'])) {
	
	include("../conexion.php");
	include("../commons.php");
	include("../password.php");
	
	$queryMaterials ="
		SELECT
			distinct(materialId) as id,
			material.*
		FROM
			material
			JOIN item USING (materialId)
		WHERE
			projectId = ".$_GET['projectId']."
	";
	$result = mysql_query($queryMaterials,$conexion);
	$jsondata = array();
	while($row = mysql_fetch_assoc($result)){
		//$jsondata[$row['materialId']]=$row['materialName']."~".$row['supplierMaterialPrice']."~".$row['supplierMaterialLastModified'];
		$jsondata[] = $row;
	}
	
	echo json_encode($jsondata);
}

mysql_close();


?>
