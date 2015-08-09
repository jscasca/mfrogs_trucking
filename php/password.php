<?php

function check_loged(){
	global $_SESSION;
	if(!isset($_SESSION["user"])){
		header("Location: /trucking/index.php");
	}
}

session_start();
check_loged();

?>