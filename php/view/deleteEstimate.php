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
	fakeproject
where
	fakeprojectId=".$_GET['i'].";";
	
$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);

if($numTerms!=0)
{
	$term = mysql_fetch_assoc($terms);
	
	$queryTerm="
	delete 
		from 
			fakeproject 
		where
			fakeprojectId=".$_GET['i'].";";
			
	$queryItem="
	delete
		from
			fakeitem
		where
			fakeprojectId=".$term['fakeprojectId'].";";
			
				$queryOwner="
	delete
		from
			owner
		where
			fakeprojectId=".$term['fakeprojectId'].";";

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",3,'{$term['fakeprojectName']} from fakeproject');";
			
	mysql_query($queryTerm,$conexion);
	mysql_query($queryItem,$conexion);
	mysql_query($queryOwner,$conexion);
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
header ("Location: viewEstimate.php");


?>

