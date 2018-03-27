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
	else {
		if (strcmp($_POST['pass1'], $_POST['pass2']) != 0) {
			printf("<h1>Passwords don't match (%s, %s)</h1>\n", $_POST['pass1'], $_POST['pass2']);
			exit;
		}
		if (strcmp($_POST['email1'], $_POST['email2']) != 0) {
			printf("<h1>Email addresses don't match.</h1>\n");
			exit;
		}
		if ($rs->check_username($_POST['username']) != 0) {
			printf("<h1>Username is taken</h1>\n");
			exit;
		}
		/* this is broken, fix it. We don't return a value, so this is irrelevant until we do */
		if (!$rs->process_registration($_POST['username'], $_POST['pass1'], $_POST['fname'], $_POST['lname'], $_POST['email1'])) {
			printf("<h1>Unable to register at this time.</h1>\n");
			exit;
		}
	}
	ob_end_flush();
?>
