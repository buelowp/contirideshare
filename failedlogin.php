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

<table width="100%" border="0">
 <tr>
  <td width="200"><img src="images/pace-logo.png"></td>
  <td><h1 class="header">Continental Rideshare Notification System</h1></td>
 </tr>
</table>

<table width="100%" border="0">
 <tr>
  <td width="200" valign="top">
  </td>
  <td>
   <table width="100%" border="0">
    <tr>
     <td><h1 class="header">Either your username or password were incorrect. Please try again</h1>
      <?php	$rs->loginform("loginformname", "loginformid", "index.php"); ?>
     </td>
    </tr>
    <tr>
     <td>
	  <h1 class="main">Recover your username</h1>
       <form name="recoverusername" method="post" id="recoverusername" class="register" enctype="application/x-www-form-urlencoded" action="recoverusername.php">
	   <table width="100%" border="0" class="register">
		<tr>
		<td width="200"><label class="login" for="emailaddr">Contact Email Address</label></td>
		<td><input name="emailaddr" id="emailaddr" type="text"></td>
		</tr>
		<tr>
		<td></td>
		<td><input name="action" id="action" value="login" type="hidden">
			<input name="submit" id="submit" value="Recover" type="submit"></td>
		</tr>
	  </table>
	  </form>
     </td>
    </tr>
    <tr>
     <td>
	  <h1 class="main">Reset your password</h1>
       <form name="resetpassword" method="post" id="resetpassword" class="register" enctype="application/x-www-form-urlencoded" action="resetpassword.php">
	   <table width="100%" border="0" class="register">
		<tr>
		<td width="200"><label class="login" for="emailaddr">Contact Email Address</label></td>
		<td><input name="emailaddr" id="emailaddr" type="text"></td>
		</tr>
		<tr>
		<td></td>
		<td><input name="action" id="action" value="login" type="hidden">
			<input name="submit" id="submit" value="Reset" type="submit"></td>
		</tr>
	  </table>
	  </form>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</body>
</html>
