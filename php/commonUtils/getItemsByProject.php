<?

if(isset($_GET['projectId'])) {
	
	include("../conexion.php");
	include("../commons.php");
	include("../password.php");
	
	$queryItems ="
		SELECT
			*
		FROM
			item
			LEFT JOIN material USING (materialId)
		WHERE
			projectId = ".$_GET['projectId']."
	";
	$result = mysql_query($queryItems,$conexion);
	$jsondata = array();
	while($row = mysql_fetch_assoc($result)){
		$jsondata[] = $row;
	}
	
	echo json_encode($jsondata);
}

mysql_close();


?>
