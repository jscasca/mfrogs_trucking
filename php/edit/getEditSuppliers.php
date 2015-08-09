<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$multipleQuery =false;
$queryTickets ="
SELECT
	*
FROM
	supplier
JOIN
	address
using (addressId) ";

if($_GET['vendorId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="vendorId='".$_GET['vendorId']."%' ";
	$multipleQuery=true;
}

if($_GET['supplierName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="supplierName like '".$_GET['supplierName']."%' ";
	$multipleQuery=true;
}
if($_GET['supplierCity']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="addressCity like '".$_GET['supplierCity']."%' ";
}
$queryTickets.="
ORDER BY 
	supplierName
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
				$suppliers = mysql_query($queryTickets,$conexion);
				$numBrokers=mysql_num_rows($suppliers);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numBrokers>0)
				{
						while($supplier=mysql_fetch_assoc($suppliers))
						{
							if($actual){
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass." id='supplier".$supplier['supplierId']."'>".$supplier['supplierName']."</td>";
								$tbody.= "<td ".$tdClass.">".$supplier['addressLine1']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								$tbody.= "<td ".$tdClass." id='supplier".$supplier['supplierId']."'>".$supplier['supplierName']."</td>";
								$tbody.= "<td ".$tdClass.">".$supplier['addressLine1']."</td>";
								$tbody.= "</tr>";
							}
							$actual=!$actual;
						}
						if(!$actual){
							$tbody.="<td ".$tdClass." colspan='2'></td></tr>";
						}
				}
			$tbody.= "</tbody>";
$jsondata['table']=$tbody;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

