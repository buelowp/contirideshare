<?php
	ob_start();
	require_once("database/rideshare.class.php");
	$rs = new Rideshare();
	$rs->init();
	$rs->is_allowed($_COOKIE['user_id']);
?>

<html>
<head>
<title>Continental Rideshare Notification System</title>
</head>
<link rel="stylesheet" href="css/rideshare.css">

<body>

<table width="100%" border="0">
 <tr>
  <td width="200"><img src="images/pace-logo.png"></td>
  <td><h1 class="header">Continental Rideshare Notification System</h1>
  <?php
	if (isset($_COOKIE['user_id'])) {
		printf("<h2 class=\"username\">User: %s %s</h2>\n", $rs->getFirstName($_COOKIE['user_id']), $rs->getLastName($_COOKIE['user_id']));
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
		$rs->print_menu($_COOKIE['user_id'], FALSE);
	}
   ?>
  </td>
  <td>
   <?php 
		$rs->msg_history(30); 
		ob_end_flush();
	?>
  </td>
 </tr>
</table>
</body>
</html>
