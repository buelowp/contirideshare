<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
		
	/* Check if the form has been submitted. If not, redirect to main page */
	if (!isset($_POST['submit'])) {
		header("Location: index.php");
		exit;
	}

	$rs->recover_username($_POST['emailaddr']);

	header("Location: index.php");
	ob_end_flush();
?>
