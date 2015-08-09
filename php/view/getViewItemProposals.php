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
						item_proposal
						JOIN material using (materialId)

";

if($_GET['smaterialId']!=0){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="materialId='".$_GET['smaterialId']."%' ";
	$multipleQuery=true;
}

if($_GET['sprojectId']!=""){
	if($multipleQuery){$queryTickets.="AND ";}else{$queryTickets.="WHERE ";}
	$queryTickets.="projectId ='".$_GET['sprojectId']."%' ";
	$multipleQuery=true;
}
$queryTickets.="
ORDER BY 
	itemProposalId desc
";
//echo $queryTickets;
$tbody="";
$tbody.= "<tbody>";
$items = mysql_query($queryTickets,$conexion);
$numitems=mysql_num_rows($items);
$actual=true;
$colorFlag=true;
$tdClass="";
if($numitems>0)
{
		while($item=mysql_fetch_assoc($items))
		{
				$tbody.= "<tr id='item".$item['itemProposalId']."'>";
				$tbody.= "<td ".$tdClass.">".$item['projectId']."</td>";
				$tbody.= "<td ".$tdClass.">".$item['itemProposalId']."</td>";
				$tbody.= "<td ".$tdClass.">".$item['materialName']."</td>";
				$tbody.= "<td ".$tdClass.">".$item['itemProposalDisplayFrom']."</td>";
				$tbody.= "<td ".$tdClass.">".$item['itemProposalDisplayTo']."</td>";
				$tbody.= "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalCustomerCost'])."</td>";
				$tbody.= "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalBrokerCost'])."</td>";
				$tbody.= "<td ".$tdClass." align='right'>".decimalPad($item['itemProposalMaterialPrice'])."</td>";
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

