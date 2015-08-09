<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);
$fakeprojectId= $_GET['i'];

$date=getdate();

$queryMfi="
SELECT
	*,
	CURDATE()
FROM
	mfiinfo
JOIN address using (addressId)
";
$frogsInfo=mysql_query($queryMfi,$conexion);
$mfiInfo = mysql_fetch_assoc($frogsInfo);

$queryInfo="
SELECT
	*
FROM
	fakeproject
WHERE
	fakeprojectId=".$fakeprojectId;

$reg=mysql_query($queryInfo,$conexion);
$projectInfo = mysql_fetch_assoc($reg);


$queryInfo2="
SELECT
*
from address
WHERE
	addressId=".$projectInfo['addressId'];
	
$reg2=mysql_query($queryInfo2,$conexion);
$projectInfo2 = mysql_fetch_assoc($reg2);

$queryInfo3="
SELECT
*
from customer
WHERE
	customerId=".$projectInfo['customerId'];
	
$reg3=mysql_query($queryInfo3,$conexion);
$projectInfo3 = mysql_fetch_assoc($reg3);

$queryInfo4="
SELECT
*
from address
WHERE
	addressId=".$projectInfo3['addressId'];
	
$reg4=mysql_query($queryInfo4,$conexion);
$projectInfo4 = mysql_fetch_assoc($reg4);


$queryInvoice="
SELECT 
	*
FROM
	fakeitem
WHERE 
fakeprojectId=".$fakeprojectId;
$invoices = mysql_query($queryInvoice,$conexion);

mysql_close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Purchase Order</title>
<script language="javascript" type="text/javascript">

function imprimir()
{
  var Obj = document.getElementById("desaparece");
  Obj.style.visibility = 'hidden';
  window.print();
}

</script>

<style type="text/css">
body {
	font-size:12px;
	font-family:"Courier New", Courier, monospace;
}
</style>

</head>

<body>
<div align="center">
  <h2>Martinez Frog's Inc.
  </h2>
  <p>650 Andy Drive   ·   Melrose Park, IL 60160   ·   Phone. (708) 259 9955   ·   Fax (312) 277 1976</p>
  <HR color="#000000">
   <h4>PROPOSAL
  </h4>
  <p align="left">Date <?echo $date[mon]?>/<?echo $date[mday]?>/<?echo $date[year]?></p>
</div>
<table width="813">
  <tr>
  
    <td align="justify" width="391">
  <h4>PROPOSAL SUBMITTED TO:</h4>
  <p>Attn: <?echo $projectInfo3['customerName']?></p>
  <p>Address: <?echo$projectInfo4['addressLine1'];?></p>
  <p><?echo$projectInfo4['addressCity'].", ".$projectInfo4['addressState'].". ".$projectInfo4['addressZip'];?></p>
  <p>Phone: <?echo $projectInfo3['customerTel']?></p>
  <p>Fax: <?echo $projectInfo3['customerFax']?></p>
</td>

<td align="justify" width="391">
  <h4>JOB  TO BE PERFORMED AT:</h4>
  <p>Project Name: <?echo $projectInfo['fakeprojectName']?></p>
  <p>Address: <?echo$projectInfo2['addressLine1'];?></p>
  <p><?echo$projectInfo2['addressCity'].", ".$projectInfo2['addressState'].". ".$projectInfo2['addressZip'];?></p>
	</br></br></br></br>
</td>

  </tr>
</table>


<table align="left" class="report" width="100%" cellspacing="0" border="1">
<tr>
	<th width="15%" >Description</th>
	<th width="10%" >Quantity</th>
	<th width="9%" >Cost</th>
    
      </tr>
      
      <?php
$total=0;
$count=0;
$subtotal = 0;
while($ticket=mysql_fetch_assoc($invoices))
{

$num=1;
$invoices4 = mysql_query($queryInvoice4,$conexion);
$invoicesInfo4=mysql_fetch_assoc($invoices4);
	echo "<tr>";
		echo "<td align=center>".$ticket['itemDescription']."</td>";
		echo "<td align=center >".$num;
		if($ticket['itemType']==0) echo " Load";
		if($ticket['itemType']==1) echo " Ton";
		if($ticket['itemType']==2) echo " Hour";
		"</td>";
		echo "<td align=center >".decimalPad($ticket['itemBrokerCost']);echo"</td>";
	echo "</tr>";
	
}

?>
</table>

<div align="center">
  <h6 align="justify">1.        The customer represents and warrants that any materials which MARTINEZ FROG'S INC.  is removing are clean materials void of any all contamination, including without limitation any petroleum products, including without limitation crude oil or any fraction thereof, and any "hazardous substance" or "pollutant contaminant" as defined in Sections 101 (14) and 101 (33) respectively of the Comprehensive Environmental Response, Compensation and Liability Act (42) U.S.C. Sec. 9601 (14)  and (33) or any other federal, state or local statute or ordinance and also void of asbestos, asphalt, glass, metals, trees, wood, landscape waste, plastics and other similar materials. The customer shall, immediately upon request, present MARTINEZ FROG'S INC. such evidence as MARTINEZ FROG'S INC. may require that all materials to be so removed are in fact clean materials. The customer shall indemnify and hold harmless MARTINEZ FROG'S INC. for any expenses, damage, costs, losses, fines, or penalties, including without limitation, attorney's fees and costs of suit, arising out of, related to, concerning or resulting from the breach of this representation and warranty. Breach of this said representation and warranty shall be a material breach of the parties' contract.</h6>
  <h6 align="justify">2.        The customer shall pay MARTINEZ FROG'S INC. all monies coming due hereunder within __30____ days; the failure of the customer to do so shall be a material breach of this agreement. The customer shall pay MARTINEZ FROG'S INC. costs of collection, including without limitation, attorneys' fees and costs of suit incurred as the result of the customer's failure to pay MARTINEZ FROG'S INC. in accordance with this agreement. Additionally, any sum remaining unpaid when due as hereinabove set forth shall bear interest at the rate of 18% per annum thereafter.</h6>
  <h6 align="justify">3.        If trucks are loaded by the customer or any other person entity except MARTINEZ FROG'S INC. the customer will be responsible for any excessive or overweight load(s) and the customer will pay and discharge any fines, costs or penalties incurred  arising  out of or resulting from any such excessive or overweight load(s); will post all bonds necessary for the release of such truck and its driver from custody; will indemnify and hold harmless MARTINEZ FROG'S INC. with regard to same, including without limitation paying all attorneys; fees and costs incurred by MARTINEZ FROG'S INC.; and will pay MARTINEZ FROG'S INC. based upon MARTINEZ FROG'S INC. then standard hourly rate, for all time such truck and or its driver are detained or otherwise out of service as result thereof.</h6>
</div>

<div align="left">
<h4>  <p align="left">If you agree with our estimate, please sign and fax back to our office before beginning the project specified above.
  If you have any question, feel free to give me a call at (708) 259 9955 .</p>
  <p align="left">Attentively,</p>
  <p align="left">Ricardo Martinez, President.</p>
</h4>

<table width="813">
  <tr>
  
    <td width="391">
  <h4>Signature __________________________________</h4>

</td>

   <td width="391">
  <h4>Date _________________________________________</h4>

</td>

  </tr>
</table>
</div>

<div align="left">
<h4 align="center">
  <p align="center">ACCEPTANCE OF THE PROPOSAL</p>
</h4>
<h4>
  <p align="left">The above prices, specifications and conditions are satisfactory and are hereby accepted. Martinez Frog's Inc. is authorized to do the work as specified. Payment will be made as outlined above.,</p>
</h4>

<table width="813">
  <tr>
  
    <td width="391">
  <h4>Print Name: <u><?echo $projectInfo3['customerName']?><u> </br>  <h6 align="center">Customer Authorized Officer</h6></h4>
 

    </td>

   <td width="391">
  <h4>Date of Acceptance______________________________</h4>
  <p>&nbsp;</p>

   </td>

  </tr>
</table>

<table width="813">
  <tr>
  
    <td width="391">
  <h4>Signature ____________________________________</h4>

  </tr>
</table>
</div>



</body>
</html>
