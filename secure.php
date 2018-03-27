<?
	ob_start();
	try {
		require_once("database/rideshare.class.php");
		$rs = new Rideshare();
		$rs->init();
		$rs->is_allowed($_COOKIE['user_id']);

		/* Verify the user has login, otherwise redirect to login page */
		if($rs->verifyAccess()) {
			/* User is logged in, display welcome message */
			print "Welcome " . $_SESSION['name'];
		}
	}
	catch(Exception $error) {
		print $error->getMessage();
	}
	ob_end_flush();
?>
