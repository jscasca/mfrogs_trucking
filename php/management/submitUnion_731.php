<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_REQUEST);

//p_array($_SESSION);

$queryUnion="
insert into 
	union731
		(
			unionClass1HourlyRate,
			unionClass2HourlyRate,
			unionClass3HourlyRate,
			unionClass4HourlyRate,
			unionWelfare,
			unionPension,
			unionCCSC,
			unionCISC,
			unionMIAF,
			unionITETF,
			unionLTF,
			unionSF,
			unionMonthlyDues,
			unionStartDate,
			unionEndDate
		) 
	values 
		(
			'".mysql_real_escape_string($_REQUEST['unionClass1HR'])."',
			'".mysql_real_escape_string($_REQUEST['unionClass2HR'])."',
			'".mysql_real_escape_string($_REQUEST['unionClass3HR'])."',
			'".mysql_real_escape_string($_REQUEST['unionClass4HR'])."',
			'".mysql_real_escape_string($_REQUEST['unionWelfare'])."',
			'".mysql_real_escape_string($_REQUEST['unionPension'])."',
			'".mysql_real_escape_string($_REQUEST['unionCCSC'])."',
			'".mysql_real_escape_string($_REQUEST['unionCISC'])."',
			'".mysql_real_escape_string($_REQUEST['unionMIAF'])."',
			'".mysql_real_escape_string($_REQUEST['unionITETF'])."',
			'".mysql_real_escape_string($_REQUEST['unionLTF'])."',
			'".mysql_real_escape_string($_REQUEST['unionSF'])."',
			'".mysql_real_escape_string($_REQUEST['unionMonthlyDues'])."',
			'".mysql_real_escape_string(to_YMD($_REQUEST['unionStart']))."',
			'".mysql_real_escape_string(to_YMD($_REQUEST['unionEnd']==""?$_REQUEST['unionStart']:$_REQUEST['unionEnd']))."'
		
		)";
		//echo $queryUnion;;

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,'".mysql_real_escape_string($_REQUEST['unionStart'])." into union');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryCarrier;
//echo $queryLog;
mysql_query($queryUnion,$conexion);
mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: managementUnion_731.php");


?>

