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
	broker
where
	brokerId=".$_GET['i'].";";

$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);
if($numTerms!=0)
{
	$term = mysql_fetch_assoc($terms);
	
	$queryTerm="
	delete 
		from 
			broker 
		where
			brokerId=".$_GET['i'].";";
			
	$queryAddress="
	delete
		from
			address
		where
			addressId=".$term['addressId'].";";
	
	$queryContact="
	delete
		from
			truck
		where
			brokerId=".$term['customerId'].";";

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",3,'{$term['brokerName']} from broker');";
			
	mysql_query($queryTerm,$conexion);
	mysql_query($queryAddress,$conexion);
	mysql_query($queryContact,$conexion);
	mysql_query($queryLog,$conexion);
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
header ("Location: newBroker.php");


?>

