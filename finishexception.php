<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
	
	/* Check if the form has been submitted. If not, redirect to main page */
	if (isset($_POST['submit'])) {
		if (isset($_POST['delexception'])) {
			$rs->delete_exception($_POST['action']);
		}
		else {
			$rs->update_exception($_POST['early'], $_POST['late'], $_POST['newexceptiondate'], $_POST['action']);
		}
	}
	
	header("Location: exception.php");
	ob_end_flush();
	exit;
?>
