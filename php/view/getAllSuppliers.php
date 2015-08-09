<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

$querySuppliers =
"
SELECT
	*
FROM
	supplier
JOIN address using (addressId)";

$suppliers = mysql_query($querySuppliers,$conexion);
$numSuppliers = mysql_num_rows($suppliers);


$centerLat=0;
$centerLong=0;
$centerDeep=12;
$average=0;

if($numSuppliers>0)
			{
				$colorFlag=true;
				while($term = mysql_fetch_assoc($suppliers))
				{
					if($term['addressLat']!=0 && $term['addressLong']!=0)
					{
						$jsondata['point']['id'][]=$term['supplierId'];
						$jsondata['point']['addressLat'][]=$term['addressLat'];
						$jsondata['point']['addressLng'][]=$term['addressLong'];
						$jsondata['point']['supplierName'][]=$term['supplierName'];
						$centerLat+=$term['addressLat'];
						$centerLong+=$term['addressLong'];
						$average++;
					}
				}
				
			}
			
if($average>0)
{
$centerLat=$centerLat/$average;	
$centerLong=$centerLong/$average;	
}
$jsondata['cLat']=$centerLat;
$jsondata['cLong']=$centerLong;
$jsondata['cDeep']=$centerDeep;


	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

