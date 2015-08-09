<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

	$queryTicket="
	insert into
		fuel_load
		(
			brokerId,
			truckId,
			fuelLoadDate,
			fuelLoadCommet,
			fuelLoadStart,
			fuelLoadFinish,
			fuelLoadRegistered,
			fuelLoadMileage
		)
		values
		(
			'".$_GET['brokerId']."',
			'".$_GET['truckId']."',
			'".mysql_real_escape_string(to_YMD($_GET['fdate']))."',
			'".mysql_real_escape_string($_GET['comment'])."',
			'".mysql_real_escape_string($_GET['start'])."',
			'".mysql_real_escape_string($_GET['finish'])."',
			'".mysql_real_escape_string($_GET['registered'])."',
			'".mysql_real_escape_string($_GET['mileage'])."'
		)";

	$jsondata['query'] = $queryTicket;
	mysql_query($queryTicket,$conexion);
	
	$lastFuel = mysql_insert_id();
	
	$broker = mysql_fetch_assoc(mysql_query("select brokerPid from broker where brokerId=".$_GET['brokerId'],$conexion));
	$truck = mysql_fetch_assoc(mysql_query("select truckNumber from truck where truckId=".$_GET['truckId'],$conexion));
	
	$newLine = "";
	$newLine.="<tr id='removableFuel$lastFuel'>";
	$newLine.="<td class=fisrt'>".$_GET['fdate']."<input type='hidden' value='".$lastFuel."' class='hiddenId' /></td>";
	$newLine.="<td >".$broker['brokerPid']."-".$truck['truckNumber']."</td>";
	$newLine.="<td >".$_GET['comment']."</td>";
	$newLine.="<td >".$_GET['start']."</td>";
	$newLine.="<td >".$_GET['finish']."</td>";
	$newLine.="<td >".($_GET['finish']-$_GET['start'])."</td>";
	$newLine.="<td >".$_GET['registered']."</td>";
	$newLine.="<td >".$_GET['mileage']."</td>";
	$newLine.="<td><img src='/trucking/img/13.png' width='20' height='20' class='editable' /></td>";
	$newLine.="<td class='last'><img src='/trucking/img/118.png' width='20' height='20' class='deletable' /></td>";
	$newLine.="</tr>";

	//$jsondata['table']="";
	$jsondata['line']=$newLine;
	
echo json_encode($jsondata);

mysql_close($conexion);

//header ("Location:newItem.php");

?>
