<?
include("../commons.php");

$query = $_REQUEST['query'];

$queryNews = "select * from broker where brokerName like '%$query%' order by brokerName";
$resultSet = mysql_query($queryNews,$conexion);
if(mysql_num_rows($resultSet) == 0){
	$jsonData['suggestions'][] = "";
	$jsonData['data'][] = 0;
}
while($result = mysql_fetch_assoc($resultSet)){
	$jsonData['suggestions'][] = $result['brokerName'];
	$jsonData['data'][] = $result['brokerId'];
}

$jsonData['query'] = $query;
mysql_close($conexion);
echo json_encode($jsonData);

?>
