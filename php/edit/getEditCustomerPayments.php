<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$multipleQuery =false;
$queryPayments ="
SELECT
	*
FROM
	customer_super_check
 ";

if($_GET['customerId']!=0){
	if($multipleQuery){$queryPayments.="AND ";}else{$queryPayments.="WHERE ";}
	$queryPayments.="customerId = ".$_GET['customerId']." ";
	$multipleQuery=true;
}
if($_GET['checkNumber']!=""){
	if($multipleQuery){$queryPayments.="AND ";}else{$queryPayments.="WHERE ";}
	$queryPayments.="customerSuperCheckNumber like '".$_GET['checkNumber']."%' ";
}
$queryPayments.="
ORDER BY 
	customerSuperCheckDate desc
";
//echo $queryTickets;
$tbody="";
$tbody.= "
			<tbody>
		";
$payments = mysql_query($queryPayments,$conexion);
$numpayments=mysql_num_rows($payments);
$colorFlag=true;
$tdClass="";
if($numpayments>0)
{
		while($payment=mysql_fetch_assoc($payments)){
			if($colorFlag) $class = " bg";
			else $class = "";
			$colorFlag = !$colorFlag;
			$tbody.= "<tr  class='editable$class' id='paymentRow".$payment['customerSuperCheckId']."' superCheckId='".$payment['customerSuperCheckId']."' >";
			$tbody.= "<td>".$payment['customerSuperCheckNumber']."</td>";
			$tbody.= "<td>".to_MDY($payment['customerSuperCheckDate'])."</td>";
			$tbody.= "<td>".decimalPad($payment['customerSuperCheckAmount'])."</td>";
			$tbody.= "<td><img src='/trucking/img/118.png' width='20px' height='20px' class='removablePayment' /></td>";
			$tbody.= "</tr>";
		}
}
$tbody.= "</tbody>";
$jsondata['table']=$tbody;
$jsondata['query']=$queryPayments;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

