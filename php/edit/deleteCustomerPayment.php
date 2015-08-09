<?php
include("../commons.php");
include("../conexion.php");

session_start();

//p_array($_GET);

//p_array($_SESSION);

//$newPrice = decimalPad($_GET['price']);

mysql_query("DELETE FROM customer_credit WHERE customerSuperCheckId = ".$_GET['customerSuperCheckId'], $conexion);
mysql_query("DELETE FROM receiptcheques WHERE customerSuperCheckId = ".$_GET['customerSuperCheckId'], $conexion);
mysql_query("DELETE FROM customer_super_check WHERE customerSuperCheckId = ".$_GET['customerSuperCheckId'], $conexion);
echo $_GET['customerSuperCheckId'];

mysql_close($conexion);


?>
