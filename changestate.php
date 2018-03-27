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
	else {
		if (isset($_POST['delete'])) {
			$rs->delete_contact($_POST['action']);
		}
		else {
			if (isset($_POST['enabled'])) {
				$rs->enable_contact($_POST['action'], 1);
			}
			else {
				$rs->enable_contact($_POST['action'], 0);
			}
		}
	}

	header("Location: account.php");
	ob_end_flush();
?>
