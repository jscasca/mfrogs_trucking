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
	
	$('#swpNumber').addClass("watermarkOn")
	.val("SWP Number")
	.focus(function(){
		if($(this).val() == "SWP Number"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("SWP Number").addClass("watermarkOn");
		}
	});
	
	$('#swpExp').addClass("watermarkOn")
	.val("Expiration")
	.focus(function(){
		if($(this).val() == "Expiration"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("Expiration").addClass("watermarkOn");
		}
	});
});

function validateForm(){
	
	if($('#swpNumber').hasClass("watermarkOn")){
		alert("Please type the special waste permit number");
		$('#swpNumber').focus();
		return false;
	}
	if($('#swpExp').hasClass("watermarkOn")){
		alert("Please type the special waste permit expiration date");
		$('#swpExp').focus();
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
	<form enctype="multipart/form-data" action="uploader.php?truckId=<?echo$_GET['truckId'];?>&queryType=truckSwp" method="POST" onsubmit="return validateForm();">
		<table align='center'>
			<tr>
				<td><div id='tpn'></div><input name="swpNumber" id="swpNumber" type="text" size="12px" /></td>
				<td><div id='tpn'></div><input name="swpExp" id="swpExp" type="text" size="8px" /></td>
				<td><div id='insU'></div><input name="uploadedfile" type="file" id="fileUpload" size="10px" /></td>
				<td><input type="submit" value="Upload & Submit" /></td>
			</tr>
		</table>
	</form>
</body>
</html>
