<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
	
	/* Check if the form has been submitted. If not, redirect to main page */
	if (isset($_POST['submit'])) {
		if ($rs->validate_message($_POST['message'])) {
			$rs->process_message($_COOKIE['user_id'], $_POST['message']);
		}
	}
	
	header("Location: index.php");
	ob_end_flush();
	exit;
?>
