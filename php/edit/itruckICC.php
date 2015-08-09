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
	
	$('#iccNumber').addClass("watermarkOn")
	.val("ICC Number")
	.focus(function(){
		if($(this).val() == "ICC Number"){
			$(this).removeClass("watermarkOn").val("");
		}
	}).blur(function(){
		if($(this).val() == ""){
			$(this).val("ICC Number").addClass("watermarkOn");
		}
	});
});

function validateForm(){
	
	if($('#iccNumber').hasClass("watermarkOn")){
		alert("Please type an ICC number");
		$('#insDate').focus();
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
<html>
	<form enctype="multipart/form-data" action="uploader.php?truckId=<?echo$_GET['truckId'];?>&queryType=truckIcc" method="POST" onsubmit="return validateForm();">
		<table align='center'>
			<tr>
				<td><div id='iccN'></div><input name="iccNumber" id="iccNumber" type="text" size="14px" /></td>
				<td><div id='iccU'></div><input name="uploadedfile" type="file" id="fileUpload" size="10px" /></td>
				<td><input type="submit" value="Upload & Submit" /></td>
			</tr>
		</table>
	</form>

</html>
