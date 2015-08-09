<?php
include("../commons.php");

session_start();

$type = $_GET['queryType'];
$truckId = $_GET['truckId'];

//fileName.substring(fileName.lastIndexOf('.') + 1);
$fileExtension = file_extension(basename( $_FILES['uploadedfile']['name']));
$fileName = uniqueFilename();
$fileName.=".".$fileExtension;

//echo $hardPath.$fileName;
//$target_path = "../../archive/";

$target_path = $hardPath . $fileName;
/*
if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
	$excel='uploads/'.$_FILES['uploadedfile']['name'];
	$sqlite='downloads/offices.db';
	convertToSqlite($excel,$sqlite);
} else{
    echo "There was an error uploading the file, please try again!";
}*/

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'],$target_path)){
	
}else{
	switch($_FILES['uploadedfile']['error']){
    case 0: //no error; possible file attack!
      echo "There was a problem with your upload.".$HTTP_POST_FILES['uploadedfile']['error'];
      break;
    case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
      echo "The file you are trying to upload is too big.";
      break;
    case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
      echo "The file you are trying to upload is too big.";
      break;
    case 3: //uploaded file was only partially uploaded
      echo "The file you are trying upload was only partially uploaded.";
      break;
    case 4: //no file was uploaded
      echo "You must select an image for upload.";
      break;
    default: //a default error, just in case!  :)
      echo "There was a default problem with your upload.";
      break;

	}
	mysql_close($conexion);
	header ("Location:uFail.html");
}

if(move_uploaded_file($FILES['uploadedfile']['tmp_name'],$remotePath.$fileName)){
	//
}

$filePathId = insertFilePath($fileName,$target_path,$remotePath.$fileName, $conexion);

switch($type){
	case 'truckIcc':
		$insertQuery = "insert into truckicc (truckId, truckICCNumber, filepathId) values ($truckId,'".$_REQUEST['iccNumber']."',$filePathId)";
	break;
	
	case 'truckIns':
		$insertQuery = "insert into insurance (truckId, insuranceDate, insuranceExp, filepathId) values ($truckId,'".to_YMD($_REQUEST['insDate'])."','".to_YMD($_REQUEST['insExp'])."',$filePathId)";
	break;
	
	case 'trailerPl':
		$insertQuery = "insert into trailerplates (truckId, trailerPlatesNumber, filepathId) values ($truckId,'".$_REQUEST['trailerPlatesNumber']."',$filePathId)";
	break;
	
	case 'truckPl':
		$insertQuery = "insert into truckplates (truckId, truckPlatesNumber, filepathId) values ($truckId,'".$_REQUEST['truckPlatesNumber']."',$filePathId)";
	break;
	
	case 'appCard':
		$insertQuery = "insert into appidcard (truckId, appCardExp, filepathId) values ($truckId,'".$_REQUEST['appCardExp']."',$filePathId)";
	break;
	
	case 'truckSwp':
		$insertQuery = "insert into swp (truckId, swpNumber, swpExp filepathId) values ($truckId,'".$_REQUEST['swpNumber']."','".to_YMD($_REQUEST['swpExp'])."',$filePathId)";
	break;
	
	case 'trailerInspec':
		$insertQuery = "insert into trailerinspection (truckId, trailerInspectionDate, filepathId) values ($truckId,'".to_YMD($_REQUEST['trailerInspectionDate'])."',$filePathId)";
	break;
	
	case 'trailerReg':
		$insertQuery = "insert into trailerregistration (truckId, trailerRegistrationAct, filepathId) values ($truckId,1,$filePathId)";
	break;
	
	case 'trailerVin':
		$insertQuery = "insert into trailervin (truckId, trailerVinNumber, filepathId) values ($truckId,'".$_REQUEST['trailerVinNumber']."',$filePathId)";
	break;
	
	case 'truckInspec':
		$insertQuery = "insert into truckinspection (truckId, truckInspectionDate, filepathId) values ($truckId,'".$_REQUEST['truckInspectionDate']."',$filePathId)";
	break;
	
	case 'truckReg':
		$insertQuery = "insert into truckregistration (truckId, truckRegistrationAct, filepathId) values ($truckId,1,$filePathId)";
	break;
	
	case 'truckVin':
		$insertQuery = "insert into truckvin (truckId, truckVinNumber, filepathId) values ($truckId,'".$_REQUEST['truckVinNumber']."',$filePathId)";
	break;
	
	case 'fuelCard':
		$insertQuery = "insert into truckvin (truckId, fuelCardNumber, filepathId) values ($truckId,'".$_REQUEST['fuelCardNumber']."',$filePathId)";
	break;
	
	case 'ucr':
		$insertQuery = "insert into truckvin (truckId, truckUcrExp, filepathId) values ($truckId,'".to_YMD($_REQUEST['truckUcrExp'])."',$filePathId)";
	break;
	
	
}

mysql_query($insertQuery,$conexion);

function insertFilePath($name, $local, $remote, $conexion){
	$queryFile = "insert into filepath (filepathName,filepathLocal,filepathRemote,filepathDate) values ('$name','$local','$remote',now())";
	mysql_query($queryFile,$conexion);
	return mysql_insert_id();
}

	mysql_close($conexion);
header ("Location:uSuccess.html");

/**/
?>

