<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
	$active = FALSE;
	$admin = FALSE;

	/* Check if the form has been submitted. If not, redirect to main page */
	if (!isset($_POST['submit'])) {
		header("Location: index.php");
		exit;
	}

	if (isset($_POST['delete'])) {
		$rs->delete_rider($_POST['rowid']);
	}
	else {
		if (isset($_POST['active'])) {
			$active = TRUE;
		}
		if (isset($_POST['admin'])) {
			$admin = TRUE;
		}
		$rs->update_rider($_POST['rowid'], $_POST['fname'], $_POST['lname'], $active, $admin, $_POST['type']);
	}

	header("Location: admin.php");
	ob_end_flush();
?>
