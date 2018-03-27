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
		if (!isset($_COOKIE['user_id'])) {
			header("Location: index.php");
		}
	?>
	<h2 class="header">Edit schedule exceptions</h2>
<!--	<form name="editexception" method="post" id="editexception" class="editexception" enctype="application/x-www-form-urlencoded" action="editexception.php"> -->
	<?php
		$rs->print_current_exceptions();
	?>
<!--	</form> -->
	<h2 class="header">Add a new schedule update</h2>
  	<form name="newexception" method="post" id="exception" class="exception" enctype="application/x-www-form-urlencoded" action="addexception.php">
  	<?php
		$rs->driver_exception_form();
		ob_end_flush();
	?>
	<input name="action" id="action" value="exception" type="hidden">
	<input name="submit" id="submit" value="Submit" type="submit">
	</form>
  </td>
 </tr>
</table>
</body>
</html>
