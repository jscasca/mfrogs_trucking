<?php
require_once("conexion.php");
include_once 'commonData.php';

$path="";

$LTH=array("Loads","Tons","Hours");
$jsColorArray = "var colorArray = [
'#FF0000',
'#000099',
'#00CC00',
'#000000',
'#660066',
'#FF6600',
'#FF0066'
];";

$key="ABQIAAAAnfs7bKE82qgb3Zc2YyS-oBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSySz_REpPq-4WZA27OwgbtyR3VcA";
$alk="ABQIAAAAnfs7bKE82qgb3Zc2YyS-oBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSySz_REpPq-4WZA27OwgbtyR3VcA";
$googleMapsScript = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$key."&sensor=false' type='text/javascript'></script>";
$gMapsV3 = "<script type='text/javascript' src='http://maps.googleapis.com/maps/api/js?sensor=false'></script>";

function updateCredit($customerSuperCheckId, $conexionHandler){
	$queryCredit = "
		SELECT
			customerSuperCheckAmount - SUM(receiptchequesAmount) as difference,
			customerCreditAmount as lastCredit
		FROM
			customer_super_check
			JOIN receiptcheques using (customerSuperCheckId)
			LEFT JOIN customer_credit using (customerSuperCheckId)
		WHERE
			customerSuperCheckId = $customerSuperCheckId
	";
	$creditInfo = mysql_fetch_assoc(mysql_query($queryCredit, $conexionHandler));
	if($creditInfo['difference'] == 0){
		if($creditInfo['lastCredit'] != null){
			//delete
			mysql_query("delete from customer_credit where customerSuperCheckId = $customerSuperCheckId", $conexionHandler);
		}else{
			//wont happend nothing to do
			mysql_query("delete from customer_credit where customerSuperCheckId = $customerSuperCheckId", $conexionHandler);
		}
	}else{
		if($creditInfo['lastCredit'] != null){
			//update
			mysql_query("update customer_credit set customerCreditAmount = ".$creditInfo['difference']." where customerSuperCheckId = $customerSuperCheckId", $conexionHandler);
		}else{
			//insert
			mysql_query("insert into customer_credit (customerSuperCheckId, customerCreditAmount) values ($customerSuperCheckId, ".$creditInfo['difference'].")", $conexionHandler);
		}
	}
	return $creditInfo['difference'];
}

function getReportBalance($reportId, $conexionHandler){
	$totalReported = getReportTotal($reportId, $conexionHandler);
	$totalPaid = getReportPaid($reportId, $conexionHandler);
	return decimalPad($totalReported - $totalPaid);
	
}

function getInvoiceTotal($invoiceId, $conexionHandler){
	$queryTotal = "
		SELECT
			SUM( itemCustomerCost * ticketAmount ) as invoiceTotal
		FROM
			invoiceticket
			JOIN ticket using (ticketId)
			JOIN item using (itemId)
		WHERE
			invoiceId = $invoiceId
	";
	$invoiceInfo = mysql_fetch_assoc(mysql_query($queryTotal, $conexionHandler));
	return decimalPad($invoiceInfo['invoiceTotal']);
}

function getInvoicePaid($invoiceId, $conexionHandler){
	$queryPaid = "
		SELECT
			SUM( receiptchequesAmount ) as paidTotal
		FROM
			receiptcheques
		WHERE
			invoiceId = $invoiceId
	";
	$paidInfo = mysql_fetch_assoc(mysql_query($queryPaid, $conexionHandler));
	return decimalPad($paidInfo['paidTotal']);
}

function getReportPaid($reportId, $conexionHandler){
	$queryPaid = "
		SELECT
			SUM(paidchequesAmount) as paidTotal
		FROM
			report
			JOIN paidcheques using (reportId)
		WHERE reportId = $reportId
	";
	$paidInfo = mysql_fetch_assoc(mysql_query($queryPaid, $conexionHandler));
	return decimalPad($paidInfo['paidTotal']);
}

function getReportTotal($reportId, $conexionHandler){
	$queryTotal = "
		SELECT
			SUM( (ticketBrokerAmount * itemBrokerCost) * (if(item.itemDescription like 'toll%', 100, if(driver.driverId is null, broker.brokerPercentage, driver.driverPercentage ) ) )/100 ) as totalReported
		FROM
			reportticket
			JOIN report using (reportId)
			JOIN ticket using (ticketId)
			JOIN item using (itemId)
			JOIN broker using (brokerId)
			LEFT JOIN driver on (driver.driverId = report.reportType)
		WHERE
			reportId = ".$reportId."
	";
	$reportInfo = mysql_fetch_assoc(mysql_query($queryTotal, $conexionHandler));
	return decimalPad($reportInfo['totalReported']);
}

function getDateWeek($ymdWeek){
	return date('W', strtotime($ymdWeek));
}

function week_start_date($wk_num, $yr, $first = 1, $format = 'Y-m-d')
{
    $wk_ts  = strtotime('+' . $wk_num . ' weeks', strtotime($yr . '0101'));
    $mon_ts = strtotime('-' . date('w', $wk_ts) + $first . ' days', $wk_ts);
    return date($format, $mon_ts);
}
/*
//Get starting date of week 40 2011
$sStartDate = week_start_date(40, 2011);
//Get the ending date of the same week
$sEndDate   = date('Y-m-d', strtotime('+6 days', strtotime($sStartDate)));

//Get current week and year
$week = date('w');
$year = date('Y');
* */
function uniqueFilename() {
	// explode the IP of the remote client into four parts
	$arrIp = explode('.', $_SERVER['REMOTE_ADDR']);
	// get both seconds and microseconds parts of the time
	list($usec, $sec) = explode(' ', microtime());
	// fudge the time we just got to create two 16 bit words
	$usec = (integer) ($usec * 65536);
	$sec = ((integer) $sec) & 0xFFFF;
	// fun bit--convert the remote client's IP into a 32 bit
	// hex number then tag on the time.
	// Result of this operation looks like this xxxxxxxx-xxxx-xxxx
	$strUid = sprintf("%08x-%04x-%04x", ($arrIp[0] << 24) | ($arrIp[1] << 16) | ($arrIp[2] << 8) | $arrIp[3], $sec, $usec);
	// tack on the extension and return the filename
	return $strUid ;
}

function file_extension($filename)
{
    $path_info = pathinfo($filename);
    return $path_info['extension'];
}

function decimalPad($value, $positions=3){
	$value = round($value,$positions);
	$pos = strpos($value,".");
	if($pos===false){
		$value.= ".".str_repeat("0",$positions);
	}else{
		if(strlen($value)==$pos+$positions+1) $value = $value;
		if(strlen($value)<$pos+$positions+1) $value .= str_repeat("0",($positions - (strlen($value) - ($pos+1))));
		if(strlen($value)>$pos+$positions+1) $value = substr($value,0,($pos+$positions+1));
	}
	return $value;
}

function decimalFill($value, $positions=2){
	$pos = strpos($value,".");
	if($pos===false){
		$value.= ".".str_repeat("0",$positions);
	}else{
		if(strlen($value)==$pos+$positions+1) $value = $value;
		if(strlen($value)<$pos+$positions+1) $value .= str_repeat("0",($positions - (strlen($value) - ($pos+1))));
		if(strlen($value)>$pos+$positions+1) $value = substr($value,0,($pos+$positions+1));
	}
	return $value;
}
/*
function decimalPad($value)
{
	$pos = strpos($value,".");
	if($pos===false)
	{
		$value.=".00";
	}
	else
	{
		if( $pos == (strlen($value)-1) ) $value .= "00";
		if( $pos == (strlen($value)-2) ) $value .= "0";
		if( $pos < (strlen($value) -2) ) $value = substr($value,0,($pos + 3));
	}
	return $value;
}*/

function getPercentage($number)
{
	return $number;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function cleanPhoneNumber($str)
{
	$num = preg_replace('%[^0-9]%',"",$str);
	return $num;
}

function showPhoneNumber($num)
{
	$len=strlen($num);
	if($len==0)
	{
		return "N/A";
	}
	if($len<10)
	{
		$str="";
		$count=0;
		for($i=$len-1;$i>0;$i--)
		{
			$count++;
			$str=$num[$i].$str;
			if($count==3)
			{
				$str=" ".$str;
				$count=0;
			}
		}
		$str=$num[0].$str;
	}
	if($len==10)
	{
		$str="(".substr($num,0,3).") ".substr($num,3,3)." ".substr($num,6);
	}
	if($len>10)
	{
		$str=substr($num,0,$len-10)." (".substr($num,$len-10,3).") ".substr($num,$len-7,3)." ".substr($num,$len-4);
	}
	return $str;
}

//Takes a date a return the next saturday
function getNextSaturday($date){
	
	$now = strtotime($date);
	$nextSaturday = strtotime('next Saturday', $now);
	return date('Y-m-d',$nextSaturday);
}

//Takes a date in 'Y-m-d' and returns the last sunday before that day. If that day is a sunday it will return the one before.
function lastSunday($date){
	return (date('Y-m-d', strtotime('last Sunday', strtotime($date)) ));
}

//Takes a function in 'Y-m-d' notation
function isSunday($date){
	return (date('N', strtotime($date)) == 7);
}


//get closest Saturday to close week
function getNextSaturdayDate($date)
{
$ret = to_YMD($date);
//$ret = strtotime($ret);
//$ret = date('Y-m-d',$ret);
	for($i=1;$i<=7;$i++)
	{
		if(date('w',strtotime())==6)
		//if(date('w',strtotime($ret.'+'.$i.' day'))==6)
		{
			$ret = date('Y-m-d',strtotime('+'.$i.' day'));
		}
	}
	$ret=to_MDY($ret);

	return $ret;
}

function to_number($string)
{
	$num = filter_var($string, FILTER_SANITIZE_NUMBER_INT);
	return str_replace('-','',$num);
}

//converts from YYYY-MM-DD to mm/dd/yyyy
function to_MDY($date2conv, $short = false)
{
	//$m=substr($date2conv,5,2);
	$m=substr($date2conv,strpos($date2conv,"-")+1,strpos($date2conv,"-",strpos($date2conv,"-")+1)-strpos($date2conv,"-")-1);
	//$d=substr($date2conv,8,2);
	$d=substr($date2conv,strrpos($date2conv,"-")+1);
	//$y=substr($date2conv,0,4);
	$y=substr($date2conv,0,strpos($date2conv,"-"));
	if($short) $y = substr($y,2);
	return($m.'/'.$d.'/'.$y);
}

//converts from mm/dd/yyyy to YYYY-MM-DD
function to_YMD($date2conv)
{
	$m=substr($date2conv,0,strpos($date2conv,"/"));
	$d=substr($date2conv,strpos($date2conv,"/")+1,strpos($date2conv,"/",strpos($date2conv,"/")+1)-strpos($date2conv,"/")-1);
	$y=substr($date2conv,strrpos($date2conv,"/")+1);
	return($y.'-'.$m.'-'.$d);
}

function b2t($b)
{
	if($b)
		return 1;
	else
		return 0;
}

function p_array($array)
{
	echo '<PRE>';
	print_r($array);
	echo '</PRE>';
}
/*
//Function to get coordinates
function getCoordinates($address) 
{
  $url = "http://maps.google.com/maps/geo?q=" . urlencode($address)
         . "&amp;output=json";
  $result = file_get_contents($url);
  $result = json_decode($result, 1);
  if (isset($result['Placemark'])) {
    list($lat, $long) = $result['Placemark'][0]['Point']['coordinates'];
  } else {
    $lat = $long = false;
  }
  //return array($lat, $long);
  return array($long, $lat);
}/**/

function getCoordinates($address) {
	$_url = sprintf('http://maps.google.com/maps?output=js&q=%s',rawurlencode($address));
	$_result = false;
	if($_result = file_get_contents($_url)) {
		if(strpos($_result,'errortips')>1 || strpos($_result,'Did you mean:') !== false) return false;
		preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $_result, $_match);
		$_coords['lat'] = $_match[1];
		$_coords['long'] = $_match[2];
		$_coords[0] = $_coords['lat'];
		$_coords[1] = $_coords['long'];
	}
	return $_coords;
}

function contains($haystack,$needle){
	$pos = strpos($haystack,$needle);
	if($pos === false)
		return false;
	else
		return true;
}

function send_mail($dst,$text,$usr,$pss){
	require_once("class.phpmailer.php");
	//require_once("smtp.inc.php");
	if($dst=="")return 0;
	$mail = new PHPMailer(true);
	$mail->Mailer = "smtp";
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 465;
	$mail->Username = $usr;
	$mail->Password = $pss;
	$mail->From = $usr;
	$mail->FromName = "Martinez Frogs Inc.";
	$mail->Subject = "";
	$mail->AltBody = $text;
	$mail->Body = $text;
	//$mail->MsgHTML("some html <b>here</b>");
	//$mail->AddAttachment("path/file.zip");
		$destinataries = explode(",",$dst);
		foreach($destinataries as $eachDest){
			//echo "adding --> $eachDest <br/ >";
			$mail->AddAddress(trim($eachDest),'');
		}
	//$mail->AddAddress($dst,"tin");
	$mail->IsHTML(false); 
	$mail->SMTPDebug = 1;
	
	if(!$mail->send()){
		return "Error " . $mail->ErrorInfo;
	}else{
		return "Message succesfully sent!";
	}
}

function get_driving_information($start, $finish)
{
    $start  = urlencode($start);
    $finish = urlencode($finish);

    $url = 'http://maps.google.co.uk/maps?f=d&hl=en&saddr='.$start.'&daddr='.$finish.'&output=html';
    //echo $url;
	if($data = file_get_contents($url))
    {
		//echo $data;
        if(preg_match_all('!<div class="altroute-rcol altroute-info">!', $data, $matches,PREG_OFFSET_CAPTURE))
        {
			p_array($matches);
			$time = substr($data,$matches[0][0][1],60);
			//$time = substr($time,strpos($time,'>')+1);
			$time = substr($time,strpos($time,'>')+1,(strpos($time,'<',strpos($time,'>')))-6);
			return "<div>".$time;
			//p_array($matches);
            //$distance   = $matches[1];
            //$time       = $matches[2];

				/*
            if(isset($matches[3]) AND $matches[3] > 0)
            {
                $time = $matches[2] * 60 + $matches[3];
            }
			*/

            //return array('distance' => $distance, 'time' => $time);
        }
        else
        {
            throw new Exception('Could not find that route');
        }
    }
    else
    {
        throw new Exception('Could not resolve URL');
    }
}

/*

function get_driving_information($start, $finish)
{
    $start  = urlencode($start);
    $finish = urlencode($finish);

    $url = 'http://maps.google.co.uk/maps?f=d&hl=en&saddr='.$start.'&daddr='.$finish.'&output=html';
    echo $url;
	if($data = file_get_contents($url))
    {
        if(preg_match('!<div class=dditd id=dditd><div><b>(\d+).*?</b>.*?<b>(\d+)[^\d]*(\d*)!si', $data, $matches))
        {
            $distance   = $matches[1];
            $time       = $matches[2];

            if(isset($matches[3]) AND $matches[3] > 0)
            {
                $time = $matches[2] * 60 + $matches[3];
            }

            return array('distance' => $distance, 'time' => $time);
        }
        else
        {
            throw new Exception('Could not find that route');
        }
    }
    else
    {
        throw new Exception('Could not resolve URL');
    }
}

*/

/////////USAGE////////
/*
And the usage (with postcodes):

try
{
    $info = get_driving_information('ec1m 4df', 'pr4 0up');
    echo $info['distance'].' miles '.$info['time'].' minutes';
}
catch(Exception $e)
{
    echo 'Caught exception: '.$e->getMessage()."\n";
}

# Outputs 229 miles 243 minutes
Or with addresses:

try
{
    $info = get_driving_information('44-46 St. John Street London', '11 Manor Road, Inskip, Preston');
    echo $info['distance'].' miles '.$info['time'].' minutes';
}
catch(Exception $e)
{
    echo 'Caught exception: '.$e->getMessage()."\n";
}

# Outputs 229 miles 243 minutes

Filter ID Filter Name 
int 			257 
boolean 		258 
float 			259 
validate_regexp 272 
validate_url 	273 
validate_email 	274 
validate_ip 	275 
string 			513 
stripped 		513 
encoded 		514 
special_chars 	515 
full_special_chars 522 
unsafe_raw 		516 
email 			517 
url 			518 
number_int 		519 
number_float 	520 
magic_quotes 	521 
callback 		1024 

*/
?>
