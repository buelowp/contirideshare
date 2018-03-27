<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
		
	/* Check if the form has been submitted. If not, redirect to main page */
	if (!isset($_POST['submit'])) {
		header("Location: index.php");
		exit;
	}

	$rs->insert_email($_POST['action'], $_POST['email']);

	header("Location: account.php");
	ob_end_flush();
?>
