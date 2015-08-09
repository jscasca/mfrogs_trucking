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
	dispatch
where
	dispatchId=".$_GET['i'].";";

$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);
if($numTerms!=0)
{
	$term = mysql_fetch_assoc($terms);
	
	$queryTerm="
	delete 
		from 
			dispatch
		where
			dispatchId=".$_GET['i'].";";
			
	$queryTruckDispatch="
	delete
		from
			truckdispatch
		where
			dispatchId=".$_GET['i'].";";
	
	
	mysql_query($queryTerm,$conexion);
	mysql_query($queryTruckDispatch,$conexion);
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
header ("Location: dispatchNew.php");


?>

