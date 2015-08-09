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
	
	$('#trailerInspectionDate').addClass("watermarkOn")
	.val("Trailer inspection date")
	.focus(function(){
		if($(this).val() == "Trailer inspection date"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("Trailer inspection date").addClass("watermarkOn");
		}
	});
});

function validateForm(){
	
	if($('#trailerInspectionDate').hasClass("watermarkOn")){
		alert("Please type the trailer inspection date");
		$('#trailerInspectionDate').focus();
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
	<form enctype="multipart/form-data" action="uploader.php?truckId=<?echo$_GET['truckId'];?>&queryType=trailerInspec" method="POST" onsubmit="return validateForm();">
		<table align='center'>
			<tr>
				<td><div id='tpn'></div><input name="trailerInspectionDate" id="trailerInspectionDate" type="text" size="18px" /></td>
				<td><div id='insU'></div><input name="uploadedfile" type="file" id="fileUpload" size="10px" /></td>
				<td><input type="submit" value="Upload & Submit" /></td>
			</tr>
		</table>
	</form>
</body>
</html>
