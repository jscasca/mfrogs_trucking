<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$paidchequesId = $_REQUEST['i'];
echo $paidchequesId;
$creditInfo = mysql_fetch_assoc(mysql_query("select * from receiptcheques where receiptchequesId = $paidchequesId",$conexion));

mysql_query("delete from receiptcheques where receiptchequesId = $paidchequesId", $conexion);

if($creditInfo['customerSuperCheckId']!=null){
	//super check
	updateCredit($creditInfo['customerSuperCheckId'], $conexion);
}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);
?>
