<?php

require_once("conexion.php");
include("commons.php");

session_start();

//p_array($_POST);

$queryUser =
	"SELECT
		*
	FROM
		users
	WHERE
		userName = '".$_POST['user']."' AND
		userPass = '".md5($_POST['pass'])."'";
$userList = mysql_query($queryUser,$conexion);
$userCount = mysql_num_rows($userList);
if($userCount==0)
{
	header("Location:../index.php?t=e");
}else
{
	$userData = mysql_fetch_assoc($userList);
	$user = new stdClass;
	$user->id = $userData['userId'];
	$user->name = $userData['userName'];
	$user->Fname = $userData['userFirstName'];
	$user->Lname = $userData['userLastName'];
	$_SESSION['user'] = $user;
	header("Location:../index.php");
}
?>
