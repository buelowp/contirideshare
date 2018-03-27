<?php
	ob_start();
	try {
		require_once("database/rideshare.class.php");
		$rs = new Rideshare();
		$rs->init();
		$rs->is_allowed($_COOKIE['user_id']);
		/* Check if the form has been submitted. If not, redirect to login page */
		if(!isset($_POST['submit'])) {
			header("Location: index.php");
			exit;
		}
		else {	
			$username = $_POST['username'];
			$password = $_POST['password'];		
		
			/* Verify the login details are correct and redirect to secure.php */
			$rs->verifyLogin($username, $password);
		}
	}
	catch(Exception $error) {
		print $error->getMessage();
	}
	ob_end_flush();
?>
