<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_REQUEST);

//p_array($_SESSION);

if(isset($_GET['materialId']))
{
	$queryMaterials =
	"SELECT
		*
	FROM
		supplier
	JOIN address using (addressId)
	JOIN suppliermaterial using (supplierId)
	JOIN material using (materialId)
	WHERE
		materialId = '".$_GET['materialId']."'";
		//echo $queryMaterials;
	$result = mysql_query($queryMaterials,$conexion);
	$num = mysql_num_rows($result);
	
	if($num>0){
		$tbody="
		<div class='table' id='priceList' >
			<img src='/trucking/img/bg-th-left.gif' width='8' height='7' alt='' class='left' />
				<img src='/trucking/img/bg-th-right.gif' width='7' height='7' alt='' class='right' />
				<table class='listing form' cellpadding='0' cellspacing='0' id='toSort' >
				<tr>
					<th class='first' >Supplier Name</th>
					<th>Material Name</th>
					<th class='sortable' attributeToSort='atprice' >Material Price</th>
					<th>Last Modified</th>
					<th class='sortable' attributeToSort='atdistance' >Distance</th>
					<th>DumpTime</th>
					<th class='sortable' attributeToSort='ateta' >ETA</th>
					<th class='sortable' attributeToSort='atx2' title='[ material price ] + [ hourly rate * ( ( ETA * 2 ) + Job Dumptime + Supplier Dumptime ) ]'>x2</th>
					<th title='[ material price ] + [ hourly rate * ( ( ETA * 2.5 ) + Job Dumptime + Supplier Dumptime ) ]'>x2.5</th>
					<th title='[ material price ] + [ hourly rate * ( ( ETA * 3.0 ) + Job Dumptime + Supplier Dumptime ) ]'>x3</th>
					<th class='last'></th>
				</tr>
		 ";
		 $colorFlag=true;
		while($row = mysql_fetch_assoc($result)){
			if($colorFlag){$class = "";}
			else{$class = "class='bg'";}
			$tbody.="
			<tr $class id='row".$row['supplierId']."' atprice='".$row['supplierMaterialPrice']."' atdistance='100000' ateta='100000' atx2='100000' >
			<td class='first style2' >".$row['supplierName']."</td>
			<td>".$row['materialName']."</td>
			<td id='matprice".$row['supplierId']."'>$ ".decimalPad($row['supplierMaterialPrice'])."</td>
			<td>".to_MDY($row['supplierMaterialLastModified'])."</td>
			<td id='dist".$row['supplierId']."' ></td>
			<td id='dptm".$row['supplierId']."' >".$row['supplierDumptime']."</td>
			<td id='eta".$row['supplierId']."' ></td>
			<td id='eta".$row['supplierId']."x2p' ></td>
			<td id='eta".$row['supplierId']."x25p' ></td>
			<td id='eta".$row['supplierId']."x3p' ></td>
			<td id='supplier".$row['supplierId']."' class='toNew' ><img src='/trucking/img/23.png' width='22px' /></td>
			</tr>
			";
			
			$jsondata['lat'][] = $row['addressLat'];
			$jsondata['lng'][] = $row['addressLong'];
			$price[] = $row['supplierMaterialPrice'];
			$lastMod[] = $row['supplierMaterialLastModified'];
			$mName[] = $row['materialName'];
			$sName[] = $row['supplierName'];
			$jsondata['supplierId'][] = $row['supplierId'];
			$jsondata['supplierName'][] = $row['supplierName'];
			$mId[] = $row['materialId'];
			$colorFlag=!$colorFlag;
		}
		$tbody.="</div>";
		
			
	}
	
	$row = mysql_fetch_assoc($result);
	
	$jsondata['table'] = $tbody;
	echo json_encode($jsondata);
	/*while($row = mysql_fetch_assoc($result)){
		//$jsondata[$row['materialId']]=$row['materialName']."~".$row['supplierMaterialPrice']."~".$row['supplierMaterialLastModified'];
		$jsondata['itemNumber']=$row['materialName'];
	}*/
	

}

/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

