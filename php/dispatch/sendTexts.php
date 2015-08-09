<?
include("../conexion.php");
include("../commons.php");
include("../password.php");

//p_array($_GET);

//p_array($_SESSION);

if(isset($_GET['trucks'])){

	//get brokers and mails
	$queryBrokers = "
		SELECT
			* 
		FROM
			broker 
			JOIN truck using (brokerId)
			LEFT JOIN carrier using (carrierId)
		WHERE
			truckId in (".$_GET['trucks'].") ";
	//echo $queryBrokers;
	$trucks = mysql_query($queryBrokers,$conexion);
	$truckString="";
	while($truck = mysql_fetch_assoc($trucks)){
		if($truck['brokerEmail'] != null && $truck['brokerEmail'] != ""){
			$truckString=$truck['brokerEmail'].",";
		}
		if($truck['carrierMail']!=null && $truck['brokerMobile']!=null && $truck['brokerMobile'] != ""){
			$truckString=$truck['brokerMobile']."@".$truck['carrierMail'].",";
		}
		//$truckString[]=$truck['truckId'];
	}
	$dst = substr($truckString,0,-1);
	
	$queryInfo = "select * from mfiinfo";
	$mfiInfo = mysql_query($queryInfo,$conexion);
	$info = mysql_fetch_assoc($mfiInfo);
	$text = $_GET['text'];
	$usr = $info['mfiMail'];
	$pss = $info['mfiPass'];
	//echo $dst . "<br/>";
	//echo $text . "<br/>";
	//echo $usr . "<br/>";
	//echo $pss . "<br/>";
	$response = send_mail($dst,$text,$usr,$pss);
}
$jsondata['trucks']=$truckString;
$jsondata['response']=$response;

	echo json_encode($jsondata);
/*
For Log Actions:
1 -> New (insert into)
2 -> Edit (update from)
3 -> Delete (delete from)
*/

mysql_close();


?>

