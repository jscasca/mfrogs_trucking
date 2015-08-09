<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$supplierchequeId = $_REQUEST['i'];
echo $supplierchequeId;
mysql_query("delete from suppliercheque where supplierchequeId = $supplierchequeId", $conexion);

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//mysql_query($queryLog,$conexion);

mysql_close($conexion);
?>
