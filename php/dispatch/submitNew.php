<?php
include("../commons.php");
include("../conexion.php");


ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

$queryDispatch="
insert into
	dispatch 
	(
		projectId,
		dispatchDate,
		dispatchComment,
		dispatchCount
	)
	values
	(
		'".mysql_real_escape_string($_REQUEST['projectId'])."',
		".($_REQUEST['dispatchDate']==""?"now()":"'".to_YMD($_REQUEST['dispatchDate'])."'").",
		'".mysql_real_escape_string($_REQUEST['dispatchComment'])."',
		'".mysql_real_escape_string($_REQUEST['dispatchCount'])."'
	)";
	
	//echo $queryDispatch;
mysql_query ($queryDispatch, $conexion);

mysql_query($queryVendor,$conexion);
$vendorId = mysql_insert_id();	
//echo $queryVendor;

$queryLog="
insert into 
	log
		(logDate, userId, logAction, logDescription)
	values
		(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['dispatchDate'])." into vendors');";
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
mysql_query($queryLog,$conexion);

mysql_close($conexion);

header ("Location:dispatchNew.php");

?>
