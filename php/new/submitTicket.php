<?php
include("../commons.php");
include("../conexion.php");

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(0);

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);
$testExisting="select count(*) as existing from ticket where ticketMfi='".$_GET['ticket']. "' and ticketNumber='".$_GET['ticketNumber']."'";
$tests=mysql_query($testExisting,$conexion);
$test=mysql_fetch_assoc($tests);

if($test['existing']==0)
{
	$queryTicket="
	insert into
		ticket
		(
			itemId,
			truckId,
			driverId,
			ticketDate,
			ticketAmount,
			ticketBrokerAmount,
			ticketNumber,
			ticketMfi,
			ticketPercentage
		)
		values
		(
			'".mysql_real_escape_string($_GET['itemId'])."',
			'".mysql_real_escape_string($_GET['truckId'])."',
			'".mysql_real_escape_string($_GET['driverId'])."',
			'".mysql_real_escape_string(to_YMD($_GET['ticketDate']))."',
			'".mysql_real_escape_string($_GET['ticketAmount'])."',
			'".mysql_real_escape_string($_GET['ticketBrokerAmount'])."',
			'".mysql_real_escape_string($_GET['ticketNumber'])."',
			'".mysql_real_escape_string($_GET['ticket'])."',
			'".mysql_real_escape_string($_GET['ticketPercentage'])."'
		)";

	//echo $queryTicket;
	mysql_query($queryTicket,$conexion);
	
	$lastTicket = mysql_insert_id();
	
	if($_GET['driverId']!=0){
		
		$queryRelation = "select count(*) as existing from lastdrivingrelation where truckId =".$_GET['truckId'];
		$relations = mysql_query($queryRelation,$conexion);
		$relation = mysql_fetch_assoc($relations);
		if($relation['existing']==0){
			$querySaveRelation = "insert into lastdrivingrelation (truckId,driverId) values ('".$_GET['truckId']."','".$_GET['driverId']."')";
		}else{
			$querySaveRelation = "update lastdrivingrelation set driverId = '".$_GET['driverId']."' where truckId = '".$_GET['truckId']."'";
		}
		mysql_query($querySaveRelation,$conexion);
	}

	$queryLog="
	insert into 
		log
			(logDate, userId, logAction, logDescription)
		values
			(NOW(),".$_SESSION['user']->id.",1,' ".mysql_real_escape_string($_REQUEST['itemId'])." into items');";
	
	mysql_query($queryLog,$conexion);
	
	$item = mysql_fetch_assoc(mysql_query("select * from item where itemId=".$_GET['itemId'],$conexion));
	$truck = mysql_fetch_assoc(mysql_query("select * from truck where truckId=".$_GET['truckId'],$conexion));
	$material = mysql_fetch_assoc(mysql_query("select * from material where materialId=".$item['materialId'],$conexion));
	$broker = mysql_fetch_assoc(mysql_query("select * from broker where brokerId=".$truck['brokerId'],$conexion));
	
	$newLine = "";
	$newLine.="<tr>";
	$newLine.="<td class=fisrt'>".$item['projectId']."</td>";
	$newLine.="<td class=fisrt'>".$item['itemNumber']."</td>";
	$newLine.="<td class=fisrt'>".$_GET['ticketDate']."</td>";
	$newLine.="<td class=fisrt'>".$broker['brokerPid']."-".$truck['truckNumber']."</td>";
	$newLine.="<td class=fisrt'>".$material['materialName']."</td>";
	$newLine.="<td class=fisrt'>".$item['itemDisplayFrom']."</td>";
	$newLine.="<td class=fisrt'>".$item['itemDisplayTo']."</td>";
	$newLine.="<td class=fisrt'>".$_GET['ticket'].($_GET['ticketNumber']==""?"":"/".$_GET['ticketNumber'])."</td>";
	$newLine.="<td><a href='/trucking/php/view/viewTicket.php?i=".$lastTicket."'><img src='/trucking/img/16.png' width='20' height='20' /></a></td>";
	$newLine.="<td><a href='/trucking/php/edit/editTicket.php?i=".$lastTicket."'><img src='/trucking/img/13.png' width='20' height='20' /></a></td>";
	$newLine.="<td class='last'><a onclick=\"return confirm('Are you sure you want to delete ticket #".$_GET['ticket']."?');\" href='deleteTicket.php?i=".$lastTicket."'><img src='/trucking/img/118.png' width='20' height='20' /></a></td>";
	$newLine.="</tr>";

	//$jsondata['table']="";
	$jsondata['line']=$newLine;
}else{$jsondata['error']="Ticket number and dump already exist";}
echo json_encode($jsondata);

mysql_close($conexion);

//header ("Location:newItem.php");

?>
