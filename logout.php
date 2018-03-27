<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->logout();
	header("Location: index.php");
	ob_end_flush();
?>
