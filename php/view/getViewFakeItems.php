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
						fakeitem
						JOIN material using (materialId)
						JOIN (SELECT addressLine1 as fromAddress, addressId as fromAddressId from address) as F using (fromAddressId)
						JOIN (SELECT addressLine1 as toAddress, addressId as toAddressId from address) as T using (toAddressId)

";

if($_GET['smaterialId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="materialId='".$_GET['smaterialId']."%' ";
	$multipleQuery=true;
}

if($_GET['sitemNumber']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="itemNumber like '".$_GET['sitemNumber']."%' ";
	$multipleQuery=true;
}

if($_GET['sfakeprojectId']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="fakeprojectId ='".$_GET['sfakeprojectId']."%' ";
	$multipleQuery=true;
}

if($_GET['sfrom']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="fromAddress like '".$_GET['sfrom']."%' ";
	$multipleQuery=true;
}

if($_GET['sto']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="toAddress like '".$_GET['sto']."%' ";
	$multipleQuery=true;
}
$queryTickets.="
ORDER BY 
	itemId desc
";
//echo $queryTickets;
$tbody="";
$tbody.= "
							<tbody>
						";
								$items = mysql_query($queryTickets,$conexion);
				$numitems=mysql_num_rows($items);
				$actual=true;
				$colorFlag=true;
				$tdClass="";
				if($numitems>0)
				{
						while($item=mysql_fetch_assoc($items))
						{
								$tbody.= "<tr id='item".$item['itemId']."'>";
								$tbody.= "<td ".$tdClass.">".$item['fakeprojectId']."</td>";
								$tbody.= "<td ".$tdClass.">".$item['itemNumber']."</td>";
								$tbody.= "<td ".$tdClass.">".$item['materialName']."</td>";
								$tbody.= "<td ".$tdClass.">".$item['fromAddress']."</td>";
								$tbody.= "<td ".$tdClass.">".$item['toAddress']."</td>";
								$tbody.= "</tr>";
							$colorFlag=!$colorFlag;
								if($colorFlag)$tdClass="";
								else $tdClass="class='bg'";
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

