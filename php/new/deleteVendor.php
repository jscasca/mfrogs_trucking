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
	vendor
where
	vendorId=".$_GET['i'].";";

$terms = mysql_query($getTerm,$conexion);
$numTerms = mysql_num_rows($terms);
if($numTerms!=0)
{
	$term = mysql_fetch_assoc($terms);
	
	$qgetSuppliers = "
	SELECT
		*
	FROM 
		supplier
	WHERE
		supplier.vendor=".$term['vendorId']."
	";
	
	$suppliers = mysql_query($getSuppliers,$conexion);
	mysql_query("BEGIN");
	while($supplier = mysql_fetch_assoc($terms))
	{
		$deleteSupplier="delete from supplier where supplierId=".$supplier['supplierId'];
		$deleteAddress="delete from address where addressId=".$supplier['addressId'];
		$deleteSupplierMaterial="delete from supplierMAterial where supplierId=".$supplier['addressId'];
		mysql_query($deleteSupplier,$conexion);
		mysql_query($deleteAddress,$conexion);
		mysql_query($deleteSupplierMaterial,$conexion);
	}
	mysql_query("COMMIT");
	
	$queryTerm="
	delete 
		from 
			vendor 
		where
			vendorId=".$_GET['i'].";";
			

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",3,'{$term['vendorName']} from vendor');";
	
	mysql_query("BEGIN");	
	mysql_query($queryTerm,$conexion);
	mysql_query($queryLog,$conexion);
	mysql_query("COMMIT");
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
header ("Location: newVendor.php");


?>

