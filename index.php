<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	
	if(!array_key_exists('action', $_REQUEST)) {
		$_REQUEST['action'] = null;
	}
?>

<html>
<head>
<title>Continental Rideshare Notification System</title>
</head>
<link rel="stylesheet" href="css/rideshare.css">
<body>

<?php
	if($_REQUEST['action'] == "login") {
		if($rs->login($_REQUEST['username'], $_REQUEST['password']) == true) {
			header("Location: index.php");
		}
		else {
			header("Location: failedlogin.php");
		}
    }
?>

<table width="100%" border="0">
 <tr>
  <td width="200"><img src="images/pace-logo.png"></td>
  <td><h1 class="header">Continental Rideshare Notification System</h1>
  <?php
	if (isset($_COOKIE['user_id'])) {
		printf("<h2 class=\"username\">%s</h2>\n", $rs->get_username($_COOKIE['user_id']));
	}
  ?>
  </td>
 </tr>
</table>

<table width="100%" border="0">
 <tr>
  <td width="200" valign="top">
  <?php
	if (isset($_COOKIE['user_id'])) {
		$rs->print_menu($_COOKIE['user_id'], TRUE);
	}
	else {
		$rs->loginform("loginformname", "loginformid", "index.php");
	}
   ?>
  </td>
  <td>
   <h1 class="main">This is the online notification system to allow Conti rideshare users to get timely and managed updates
   in the event of schedule changes and updates.</h1>
	<h2 class="header">The last 5 messages sent by the system</h2>
   <?php 
		$rs->msg_history(5);
	?>
	<br>
	<h2 class="header">Schedule changes for the next two weeks.</h2>
	<?php
		$rs->driver_exceptions();
		ob_end_flush();
	?>
  </td>
 </tr>
</table>
</body>
</html>
