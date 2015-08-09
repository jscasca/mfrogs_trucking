<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

//p_array($_GET);

//p_array($_SESSION);

$getTerm="
select 
	*
from
	driver
where
	driverId=".$_GET['i'].";";
	
$getTerm2="
select 
	*
from 
	ticket 
where 
	driverId=".$_GET['i'].";";

$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);

$terms2 = mysql_query($getTerm2,$conexion);
$numTerms2 = mysql_num_rows($terms2);

if($numTerms!=0)
{

if($numTerms2==0)
{
	$term = mysql_fetch_assoc($terms);

	$queryAddress="
	delete
		from
			address
		where
			addressId=".$term['addressId'].";";
	
	$queryContact="
	delete
		from
			driver
		where
			driverId=".$_GET['i'].";";

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",3,'{$term['driverLastName']} from driver');";

	mysql_query($queryAddress,$conexion);
	mysql_query($queryContact,$conexion);
	mysql_query($queryLog,$conexion);
	}
}


/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/
//echo $queryTerm;
//echo $queryLog;
//mysql_query($queryTerm,$conexion);
//mysql_query($queryLog,$conexion);
mysql_close();
header ("Location: editDriver.php");


?>

