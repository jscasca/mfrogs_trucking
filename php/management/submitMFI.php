<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$coordinates=getCoordinates("{$_REQUEST['addressLine1']} {$_REQUEST['addressZip']} {$_REQUEST['addressCity']} {$_REQUEST['addressState']}");

$queryAddress="
UPDATE 
	address 
SET
	addressLine1='".mysql_real_escape_string($_REQUEST['addressLine1'])."',
	addressLine2='".mysql_real_escape_string($_REQUEST['addressLine2'])."',
	addressCity='".mysql_real_escape_string($_REQUEST['addressCity'])."',
	addressState='".mysql_real_escape_string($_REQUEST['addressState'])."',
	addressZip='".mysql_real_escape_string($_REQUEST['addressZip'])."',
	addressPOBox='".mysql_real_escape_string($_REQUEST['addressPOBox'])."',
	addressLat='".$coordenadas[0]."',
	addressLong='".$coordenadas[1]."'
WHERE
	addressId=".$_GET['i'];
	
$queryMfi="
UPDATE 
	mfiinfo
SET
	mfiTel='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['mfiTel']))."',
	mfiFax='".cleanPhoneNumber(mysql_real_escape_string($_REQUEST['mfiFax']))."',
	mfiMail='".mysql_real_escape_string($_REQUEST['mfiMail'])."',
	mfiPass='".mysql_real_escape_string($_REQUEST['mfiPass'])."'";

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",2,' mfiInfo Table');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryAddress;
//echo $queryMfi;
//echo $queryLog;
mysql_query($queryAddress,$conexion);
mysql_query($queryMfi,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementMFI.php");


?>

