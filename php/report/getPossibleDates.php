<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

$queryFirstDate = 
"SELECT ticketDate FROM item join ticket using(itemId) where projectId = ".$_GET['projectId']." order by ticketDate asc limit 1";
$queryEndDate = 
"SELECT ticketDate FROM item join ticket using(itemId) where projectId = ".$_GET['projectId']." order by ticketDate desc limit 1";

$firstDates = mysql_query($queryFirstDate,$conexion);
if(mysql_num_rows($firstDates)>0) {$firstDate = mysql_fetch_assoc($firstDates); $startingDate = to_MDY($firstDate['ticketDate']); }
else $startingDate="";
$lastDates = mysql_query($queryEndDate,$conexion);
if(mysql_num_rows($lastDates)>0) {$lastDate = mysql_fetch_assoc($lastDates); $endingDate = to_MDY($lastDate['ticketDate']); }
else $endingDate="";

$jsondata['startingDate'] = $startingDate;
$jsondata['endingDate'] = $endingDate;

	echo json_encode($jsondata);

mysql_close();

?>

