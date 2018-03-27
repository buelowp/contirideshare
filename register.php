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
  </td>
 </tr>
</table>

<table width="100%" border="0">
 <tr>
  <td width="200" valign="top">
  <h2><a class="menu" href="index.php">Home</a></h2>
  </td>
  <td>
   <h1 class="main">Register ridership details here. Note that after you perform this action, an admin will have to enable you. This is to protect
   from random people registering themselves, while not requiring a ton of work in code maintenance. Username must be unique, and probably should
   be your Conti username, but this isn't required. This system isn't tied to Conti in any meaningful way, so do what you would like. Once you
   are enabled, you can add phone numbers and email addresses to the contact list, enabled or disable each one, and change your password.</h1>

   <form name="register" method="post" id="register" class="register" enctype="application/x-www-form-urlencoded" action="finishreg.php">
   <table width="100%" border="0" class="register">
    <tr>
	 <td width="200"><label class="login" for="username">Username</label></td>
	 <td><input name="username" id="username" type="text"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="pass1">Password</label></td>
	 <td><input name="pass1" id="password" type="password"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="pass2">Retype Password</label></td>
	 <td><input name="pass2" id="password2" type="password"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="email1">Email</label></td>
	 <td><input name="email1" id="password2" type="text"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="email2">Retype Email</label></td>
	 <td><input name="email2" id="password2" type="text"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="fname">First Name</label></td>
	 <td><input name="fname" id="fname" type="text"></td>
	</tr>
	<tr>
	 <td width="200"><label class="login" for="lanme">Last Name</label></td>
	 <td><input name="lname" id="lname" type="text"></td>
	</tr>
	<tr>
   	 <td></td>
	 <td><input name="action" id="action" value="login" type="hidden">
	     <input name="submit" id="submit" value="Register" type="submit"></td>
	</tr>
   </table>
   </form>
  </td>
 </tr>
</table>
</body>
</html>
