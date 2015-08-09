<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
if ($_GET['projectId'] == 0){
	$jsondata['class1pw'] = "";
	$jsondata['class2pw'] = "";
	$jsondata['class3pw'] = "";
	$jsondata['class4pw'] = "";
	$jsondata['brokerpw'] = "";
	$jsondata['lastTicketDate'] = "";
}else{
$queryProjectInfo = "select * from project where projectId =".$_GET['projectId'];
$projectsInfo = mysql_query($queryProjectInfo,$conexion);
$projectInfo = mysql_fetch_assoc($projectsInfo);
$jsondata['class1pw'] = decimalPad($projectInfo['projectClass1PW']);
$jsondata['class2pw'] = decimalPad($projectInfo['projectClass2PW']);
$jsondata['class3pw'] = decimalPad($projectInfo['projectClass3PW']);
$jsondata['class4pw'] = decimalPad($projectInfo['projectClass4PW']);
$jsondata['brokerpw'] = decimalPad($projectInfo['projectBrokerPW']);

$queryFirstTicket = "select * from project join item using (projectId) join ticket using (itemId) where projectId=".$_GET['projectId']." order by ticketDate asc limit 1";
$lastTicketReg = mysql_query($queryFirstTicket,$conexion);
$numTickets = mysql_num_rows($lastTicketReg);
if($numTickets > 0 ){
	$lastTicket = mysql_fetch_assoc($lastTicketReg);
	$jsondata['lastTicketDate']=to_MDY($lastTicket['ticketDate']);
}else{
	$jsondata['lastTicketDate']="N/A";
}
}
	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

