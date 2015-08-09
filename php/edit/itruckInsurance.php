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
	$('#insDate').addClass("watermarkOn")
	.val("Date")
	.focus(function(){
		if($(this).val() == "Date"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("Date").addClass("watermarkOn");
		}
	});
	$('#insExp').addClass("watermarkOn")
	.val("Expire date")
	.focus(function(){
		if($(this).val() == "Expire date"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("Expire date").addClass("watermarkOn");
		}
	});
});

function validateForm(){
	
	if($('#insDate').hasClass("watermarkOn")){
		alert("Please type a date");
		$('#insDate').focus();
		return false;
	}
	if($('#insExp').hasClass("watermarkOn")){
		alert("Please type an expiration date");
		$('#insExp').focus();
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
	<form enctype="multipart/form-data" action="uploader.php?truckId=<?echo$_GET['truckId'];?>&queryType=truckIns" method="POST" onsubmit="return validateForm();">
		<table align='center'>
			<tr>
				<td><div id='insD'></div><input name="insDate" id="insDate" type="text" size="8px" value="Date" /></td>
				<td><div id='insE'></div><input name="insExp" id="insExp" type="text" size="8px" /></td>
				<td><div id='insU'></div><input name="uploadedfile" type="file" id="fileUpload" size="10px" /></td>
				<td><input type="submit" value="Upload & Submit" /></td>
			</tr>
		</table>
	</form>
</body>
</html>
