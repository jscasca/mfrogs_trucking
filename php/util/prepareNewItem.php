<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//echo "hola";

//p_array($_REQUEST);

$urlBuilder = "";

if($_REQUEST['type']==0){
	//Project
	$projectQuery = "SELECT projectId as pId, customerId, addressId FROM project where projectId=".$_REQUEST['project'];
	$projectResult = mysql_query($projectQuery,$conexion);
	$project = mysql_fetch_assoc($projectResult);
	$urlBuilder.= "c=".$project['customerId'];
	$urlBuilder.= "&p=".$project['pId'];
	
	$supplierQuery = "SELECT supplierId, addressId FROM supplier WHERE supplierId=".$_REQUEST['supplier'];
	$supplierResult = mysql_query($supplierQuery,$conexion);
	$supplier = mysql_fetch_assoc($supplierResult);
	$urlBuilder.= "&s=".$supplier['supplierId'];
	
	$urlBuilder.= "&m=".$_REQUEST['material'];
	
	$priceQuery = "SELECT supplierMaterialPrice from suppliermaterial where supplierId=".$_REQUEST['supplier']." and materialId=".$_REQUEST['material'];
	$priceResult = mysql_query($priceQuery,$conexion);
	$priceInfo = mysql_fetch_assoc($priceResult);
	$urlBuilder.= "&mP=".urlencode($priceInfo['supplierMaterialPrice']);
	
	$pAddressQuery = "SELECT addressId, addressLine1 from address WHERE addressId=".$project['addressId'];
	$pAddressResult = mysql_query($pAddressQuery,$conexion);
	$pAddress = mysql_fetch_assoc($pAddressResult);
	$urlBuilder.="&pA=".urlencode($pAddress['addressLine1']);
	$urlBuilder.="&pAI=".urlencode($pAddress['addressId']);
	
	$sAddressQuery = "SELECT addressId, addressLine1 from address WHERE addressId=".$supplier['addressId'];
	$sAddressResult = mysql_query($sAddressQuery,$conexion);
	$sAddress = mysql_fetch_assoc($sAddressResult);
	$urlBuilder.="&sA=".urlencode($sAddress['addressLine1']);
	$urlBuilder.="&sAI=".urlencode($sAddress['addressId']);
	
	//echo $urlBuilder;
	header ("Location:../new/newItem.php?".$urlBuilder);
	
}else{
	$sketchQuery = "SELECT fakeprojectId, customerId, addressId FROM fakeproject where fakeprojectId=".$_REQUEST['project'];
	$result = mysql_query($sketchQuery,$conexion);
	$project = mysql_fetch_assoc($result);
	$urlBuilder.= "c=".$project['pId'];
	$urlBuilder.= "&p=".$project['pId'];
	
	$supplierQuery = "SELECT supplierId, addressId FROM supplier WHERE supplierId=".$_REQUEST['supplier'];
	$supplierResult = mysql_query($supplierQuery,$conexion);
	$supplier = mysql_fetch_assoc($supplierResult);
	$urlBuilder.= "&s=".$supplier['supplierId'];
	
	$urlBuilder.= "&m=".$_REQUEST['material'];
	
	$priceQuery = "SELECT supplierMaterialPrice from suppliermaterial where supplierId=".$_REQUEST['supplier']." and materialId=".$_REQUEST['material'];
	$priceResult = mysql_query($priceQuery,$conexion);
	$priceInfo = mysql_fetch_assoc($priceResult);
	$urlBuilder.= "&mP=".urlencode($priceInfo['supplierMaterialPrice']);
	
	$pAddressQuery = "SELECT addressLine1 from address WHERE addressId=".$project['addressId'];
	$pAddressResult = mysql_query($pAddressQuery,$conexion);
	$pAddress = mysql_fetch_assoc($pAddressResult);
	$urlBuilder.="&pA=".urlencode($pAddress['addressLine1']);
	$urlBuilder.="&pAI=".urlencode($pAddress['addressId']);
	
	$sAddressQuery = "SELECT addressLine1 from address WHERE addressId=".$supplier['addressId'];
	$sAddressResult = mysql_query($sAddressQuery,$conexion);
	$sAddress = mysql_fetch_assoc($sAddressResult);
	$urlBuilder.="&sA=".urlencode($sAddress['addressLine1']);
	$urlBuilder.="&sAI=".urlencode($sAddress['addressId']);
	
	//echo $urlBuilder;
	header ("Location:../new/newProposal.php?".$urlBuilder);
}

mysql_close();

?>
