<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$queryLast =
	"SELECT
	*
FROM
	vendor
WHERE
	vendorId=".$_GET['vendorId'];
		//echo $queryLast;
$querySuppliers =
"
SELECT
	*
FROM
	supplier
JOIN address using (addressId)
WHERE
	vendorId=".$_GET['vendorId'];
$Last = mysql_query($queryLast,$conexion);
$lastVal = mysql_fetch_assoc($Last);

$suppliers = mysql_query($querySuppliers,$conexion);
$numSuppliers = mysql_num_rows($suppliers);

$edit="<a href='deleteVendor.php?i={$lastVal['vendorId']}' class='delete' ></a>
				<a href='/trucking/php/edit/editVendor.php?i={$lastVal['vendorId']}' class='edit' ></a>";

$tbody="";

$tbody.="<div class='table' id='viewVendor'>
			<form id='formValidate' name='formValidate' method='POST' action='submitEditItem.php' onsubmit='return validateForm();' >
				<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' >
				<tr>
						<th class='full' colspan='2'>Vendor Information</th>
					</tr>
					<tr class='bg'>
						<td class='first' width='172'><strong>Name:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
							 {$lastVal['vendorName']}
						</td>
					</tr>
					<tr>
						<td class='first' width='172'><strong>Additional Information:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
							 {$lastVal['vendorInfo']}
						</td>
					</tr>
					<tr class='bg'>
						<td class='first' width='172'><strong>Comments:</strong><span style='color:red;'>*</span></td>
						<td class='last'>
							 {$lastVal['vendorComment']}
						</td>
					</tr>
					

				</table>
				<table>
				<tr>
					<td>
						<a href='/trucking/php/new/newSupplier.php?i={$lastVal['vendorId']}' >
							<strong>Add a supplier</strong>
							<img src='/trucking/img/95.png' width='20' height='20' />
						</a>
					</td>
				</tr>
				</table>
				</form>
	        <!--<p>&nbsp;</p>-->
			</div>";
$tbodySuppliers="";
$tbodySuppliers.= "<div class='table' id='suppliers'>";

$centerLat=0;
$centerLong=0;
$centerDeep=12;
$average=0;

if($numSuppliers>0)
			{
				$tbodySuppliers.= "<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />";
				$tbodySuppliers.= "<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />";
				$tbodySuppliers.= "<table class='listing' cellpadding='0' cellspacing='0'>";
				$tbodySuppliers.= "<tr>
						<th class='first' width='177'>Suppliers</th>
						<th>Address</th>
						<th>View</th>
						<th>Edit</th>
						<th class='last'>Delete</th>
					</tr>";
				$colorFlag=true;
				while($term = mysql_fetch_assoc($suppliers))
				{
					if($colorFlag)
					{
						$tbodySuppliers.= "<tr>";
						!$colorFlag;
					}
					else
					{
						$tbodySuppliers.= "<tr class='bg'>";
						!$colorFlag;
					}
					$tbodySuppliers.= "
						<td class='first style2'>".$term['supplierName']."</td>
						<td>".$term['addressLine1']."</td>
						<td><a href='/trucking/php/view/viewSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>
						<td><a href='/trucking/php/edit/editSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>
						<td class='last'><a class='deleteAnchor'  href='deleteSupplier.php?i=".$term['supplierId']."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>
					</tr>";
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
				
				
				$tbodySuppliers.= "</table>";
				
			}
			
if($average>0)
{
$centerLat=$centerLat/$average;	
$centerLong=$centerLong/$average;	
}
$tbodySuppliers.= "</div>";
$jsondata['table']=$tbody;
$jsondata['tableSuppliers']=$tbodySuppliers;
$jsondata['edit']=$edit;
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

