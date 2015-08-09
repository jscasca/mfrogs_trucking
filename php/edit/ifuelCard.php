<?php
include("../commons.php");

session_start();

//p_array($_REQUEST);

//p_array($_SESSION);

?>


<html>
	<head>
	<title>Homepage</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<style media="all" type="text/css">@import "../../css/uploadFrames.css";</style>
	</head>
	<script type="text/javascript" src="/trucking/js/jquery.js" ></script>
<script type="text/javascript" src="/trucking/js/json2.js" ></script>
<script type="text/javascript">	
$(document).ready(function(){
	
	$('#fuelCardNumber').addClass("watermarkOn")
	.val("Fuel card Number")
	.focus(function(){
		if($(this).val() == "Fuel card Number"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("Fuel card Number").addClass("watermarkOn");
		}
	});
});

function validateForm(){
	
	if($('#fuelCardNumber').hasClass("watermarkOn")){
		alert("Please type the fuel card number");
		$('#fuelCardNumber').focus();
		return false;
	}
	var fileName = document.getElementById('fileUpload').value;
	var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
	if(ext != "jpg" && ext != "pdf"){
		alert("Please select a file with JPG or PDF extension");
		return false;
	}
	return true;
}
</script>
<body>
	<form enctype="multipart/form-data" action="uploader.php?truckId=<?echo$_GET['truckId'];?>&queryType=fuelCard" method="POST" onsubmit="return validateForm();">
		<table align='center'>
			<tr>
				<td><div id='tpn'></div><input name="fuelCardNumber" id="fuelCardNumber" type="text" size="16px" /></td>
				<td><div id='insU'></div><input name="uploadedfile" type="file" id="fileUpload" size="10px" /></td>
				<td><input type="submit" value="Upload & Submit" /></td>
			</tr>
		</table>
	</form>
</body>
</html>
