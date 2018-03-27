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

 <script type="text/javascript">
	var count = "125";   //Example: var count = "175";
	function limiter() {
		var tex = document.notify.message.value;
		var len = tex.length;
		if(len > count){
			tex = tex.substring(0,count);
			document.notify.message.value = tex;
			return false;
		}
		document.notify.limit.value = count  - len;
	}
 </script>
 
<table width="100%" border="0">
 <tr>
  <td width="200"><img src="images/pace-logo.png"></td>
  <td><h1 class="header">Continental Rideshare Notification System</h1>
  <?php
	printf("<h2 class=\"username\">User: %s %s</h2>\n", $rs->getFirstName($_COOKIE['user_id']), $rs->getLastName($_COOKIE['user_id']));
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
	ob_end_flush();
   ?>
  </td>
  <td>
  <h1 class="header">Submit a rider notification.</h1>
  <form method="POST" enctype="application/x-www-form-urlencoded" action="messaging.php" name="notify">
  Message<br>
  <textarea rows="6" columns="100" wrap="virtual" name="message" onKeyUp="limiter();"></textarea><br>
  <script type="text/javascript">
   document.write("<input type=text name=limit size=4 readonly value="+count+">");
  </script>
	<input name="submit" id="submit" value="Send" type="submit">
  </form>
  </td>
 </tr>
</table>
</body>
</html>
