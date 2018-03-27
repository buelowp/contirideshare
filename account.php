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
		printf("<h2 class=\"username\">%s</h2>\n", $rs->get_username($_COOKIE['user_id']));
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
   <h1 class="main">Change your password<h1>
   	<form name="userupdate" method="post" id="register" class="register" enctype="application/x-www-form-urlencoded" action="passchange.php">
	<table width="100%" border="0" cellpadding="5" id="messaging">
	 <tr>
	  <td width="200"><label class="update" for="pass1">Password</label></td>
	  <td><input name="pass1" id="pass1" type="password"></td>
	 </tr>
	  <td><label class="update" for="pass2">Retype Password</label></td>
	  <td><input name="pass2" id="pass2" type="password"></td>
	 </tr>
	 <tr>
   	  <td><input name="action" id="action" value="passchange" type="hidden">
	     <input name="submit" id="submit" value="Submit" type="submit"></td>
	  <td></td>
	 </tr>
	</table>
	</form>
	<hr>
	<h1 class="main">Update or create your permanent contact email address here. Note, this address is seperate from your rideshare contact details, but can be one of them. This email address
	will allow you to recover a lost username or password should you need to.</h1>
	<?php $rs->update_contact_email($_COOKIE['user_id']); ?>
	<hr>
	<h1 class="main">You can enable/disable or delete from your notification contact list here. You may include any number of email addresses or SMS capable phone #'s. SMS rates apply.</h1>
    <?php $rs->print_contacts($_COOKIE['user_id']);	?>
	<hr>
	<h1 class="main">If your carrier isn't listed below, click on the support link and create a ticket to add the new carrier.</h1>
	<?php
		$rs->create_contact_form($_COOKIE['user_id']);
		ob_end_flush();
	?>
	</td>
 </tr>
</table>
</body>
</html>
