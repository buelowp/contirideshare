<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
	
	/* Check if the form has been submitted. If not, redirect to main page */
	if (isset($_POST['submit'])) {
		$rs->add_driver_exception($_COOKIE['user_id'], $_POST['early'], $_POST['late'], $_POST['exceptiondate']);
	}
	
	header("Location: exception.php");
	ob_end_flush();
	exit;
?>
