<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_GET);

//p_array($_SESSION);

$receiptId = $_GET['receiptId'];
$receiptInfo = mysql_fetch_assoc(mysql_query("select * from receiptcheques where receiptchequesId = $receiptId", $conexion));
echo "delete from receiptcheques where receiptchequesId = $receiptId";
mysql_query("delete from receiptcheques where receiptchequesId = $receiptId", $conexion);
updateCredit($receiptInfo['customerSuperCheckId'], $conexion);

mysql_close($conexion);


?>


