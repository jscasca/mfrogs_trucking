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
	vendor
 ";

if($_GET['vendorName']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="vendorName like '".$_GET['vendorName']."%' ";
	$multipleQuery=true;
}
if($_GET['vendorPid']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="vendorId like '".$_GET['vendorId']."%' ";
}
$queryTickets.="
ORDER BY 
	vendorName
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
				$vendors = mysql_query($queryTickets,$conexion);
				$numvendors=mysql_num_rows($vendors);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numvendors>0)
				{
						while($vendor=mysql_fetch_assoc($vendors))
						{
							if($actual){
								$tbody.= "<tr>";
								$tbody.= "<td ".$tdClass." id='vendor".$vendor['vendorId']."'>".$vendor['vendorName']."</td>";
								$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
							}else{
								$tbody.= "<td ".$tdClass." id='vendor".$vendor['vendorId']."'>".$vendor['vendorName']."</td>";
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

