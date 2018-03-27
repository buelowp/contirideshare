<?php
//start session
session_start();

class Rideshare 
{
	var $hostname = 'mysql.peterbuelow.net';		//Database server LOCATION
	var $database = 'contirideshare';		//Database NAME
	var $username = 'contirideshare';		//Database USERNAME
	var $password = 'Scrub12';		//Database PASSWORD
	
	//table fields
	var $user_table = 'users';			//Users table name
	var $exception_table = 'exceptions';
	var $contact_table = 'contacts';
	var $messages_table = 'messages';
	
	var $user_column = 'username';		//USERNAME column (value MUST be valid email)
	var $pass_column = 'password';		//PASSWORD column
	var $user_level = 'admin';			//(optional) userlevel column
	var $user_id = 'id';				// User id. Store this instead of the hashed pass.

	var $error = "none";
	var $connected = FALSE;
	var $db;
	var $mysql;
	var $email_account = "admin@contirideshare.net";
//	var $email_account = "admin@northseminary.net";
	
	//connect to database
	function init()
	{
		date_default_timezone_set('America/Chicago');
		
		$this->mysql = mysql_connect($this->hostname, $this->username, $this->password) or die ('Unabale to connect to the database');
		$this->db = mysql_select_db($this->database, $this->mysql);
		if (!$this->db) {
    			die ('Can\'t use rideshare database : ' . mysql_error());
		}
		$this->connected = TRUE;
		return;
	}
	
	function is_allowed($var)
	{
		if (!isset($var)) {
			header("Location: index.php");
		}
	}
	
	function encode_password($password)
	{
		return md5($password);
	}
	
	//login function
	function login($username, $password)
	{
		if (!$this->connected) {
			session_destroy();
			header("Location: index.php");
			return false;
		}
		
		$password = $this->encode_password($password);
		
		//execute login via qry function that prevents MySQL injections
		$query = sprintf("SELECT * FROM users WHERE username ='%s' AND password = '%s'", $username, $password);
		$result = $this->qry($query);
		
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_assoc($result);
			//register sessions
			//you can add additional sessions here if needed
			$_SESSION['loggedin'] = $row['userid'];
			setcookie("user_id", $row['userid'], time() + 3600);
			return true;
		}	
		else {
			session_destroy();
			header("Location: index.php");
			return false;
		}
	}
	
	//prevent injection
	function qry($query) 
	{
		if (strlen($query) < 1) {
			$this->error = "Error";
			return FALSE;
		}
		if ($this->connected) {
			$args  = func_get_args();
			$query = array_shift($args);
			$query = str_replace("?", "%s", $query);
			$args  = array_map('mysql_real_escape_string', $args);
			array_unshift($args,$query);
			$query = call_user_func_array('sprintf',$args);
			$result = mysql_query($query) or die("Query: $query: " . mysql_error());
			if ($result) {
				return $result;
			}
			else {
				$this->error = "Error";
				return $result;
			}
		}
    }
	
	//logout function 
	function logout()
	{
		session_destroy();
		setcookie("user_id", "", time() - 3600);
	}
	
	//check if loggedin
	function logincheck()
	{
		if ($this->connected) {
			$query = sprintf("SELECT userid FROM users WHERE userid=%d", $_COOKIE['user_id']);
			if (isset($_COOKIE['user_id'])) {
				$result = $this->qry($query);
				if (mysql_num_rows($result) == 1) {
					return true;
				}
			}
			return false;			
		}
	}
	
	//create random password with 8 alphanumerical characters
	function create_password() 
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
	
	//login form
	function loginform($formname, $formclass, $formaction)
	{
		printf("<form name=\"%s\" method=\"post\" id=\"%s\" class=\"%s\" enctype=\"application/x-www-form-urlencoded\" action=\"%s\">\n",
				$formname, $formname, $formclass, $formaction);
		printf(" <div><label class=\"login\" for=\"username\">Username</label>\n");
		printf("  <input name=\"username\" id=\"username\" type=\"text\"></div>\n");
		printf(" <div><label class=\"login\" for=\"password\">Password</label>\n");
		printf("  <input name=\"password\" id=\"password\" type=\"password\"></div>\n");
		printf("  <input name=\"action\" id=\"action\" value=\"login\" type=\"hidden\">\n");
		printf(" <div><input name=\"submit\" id=\"submit\" value=\"Login\" type=\"submit\"></div>\n");
		printf("</form>\n");
		printf("<h2><a class=\"menu\" href=\"register.php\">Register</a></h2>\n");
	}
	
	//reset password form
	function resetform($formname, $formclass, $formaction){
		//conect to DB
		$this->connect();
		echo'<form name="'.$formname.'" method="post" id="'.$formname.'" class="'.$formclass.'" enctype="application/x-www-form-urlencoded" action="'.$formaction.'">
				<div><label for="username">Username</label>
				<input name="username" id="username" type="text"></div>
				<input name="action" id="action" value="resetlogin" type="hidden">
				<div><input name="submit" id="submit" value="Reset Password" type="submit"></div>
			</form>';
	}
	
	function is_admin($id) {
		$result = $this->qry("SELECT admin FROM users WHERE userid=".$id);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		if ($row['admin'] == 1) {
			return TRUE;
		}
		return FALSE;
	}
	
	function is_driver($id) {
		$result = $this->qry("SELECT type FROM users WHERE userid=".$id);
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		if ($row['type'] > 0) {
			return TRUE;
		}
		return FALSE;
	}
	
	function getFirstName($id) {
		$result = $this->qry("SELECT fname FROM users where userid=".$id);
		$row = mysql_fetch_row($result);
		
		return $row[0];
	}

	function getLastName($id) {
		$result = $this->qry("SELECT lname FROM users where userid=".$id);
		$row = mysql_fetch_row($result);
		
		return $row[0];
	}
	
	function show_users($active) {
		if ($active) {
			$result = $this->qry("SELECT * FROM users WHERE active=TRUE ORDER BY lname");
		}
		else {
			$result = $this->qry("SELECT * FROM users ORDER BY lname");
		}
		printf("<table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n   <tr><th>Username<th>Name<th>Active<th>Admin<th>Driver</tr>\n");
		while ($row = mysql_fetch_assoc($result)) {
			printf("    <tr>\n      <td width=\"100\">%s</td><td width=\"200\">%s %s</td><td align=\"center\">%d</td><td align=\"center\">%d</td><td align=\"center\">%d</td>\n    </tr>\n",
				$row['username'], $row['fname'], $row['lname'], $row['active'], $row['admin'], $row['driver']);
		}
		mysql_free_result($result);
		printf("    </table>");
	}

	function validate_message($m) {
		if (strlen($m) == 0) {
			return FALSE;
		}
		$msg = stripslashes($m);
		$msg_len = strlen($msg);

		if ($msg_len > 130) {
			return FALSE;
		}
		return TRUE;
	}

	function process_message($id, $msg) {
		$email_count = 0;
		$sms_count = 0;
		$query = sprintf("SELECT userid FROM users WHERE active=1");
		$result = $this->qry($query);

		while ($row = mysql_fetch_assoc($result)) {
			$query = sprintf("SELECT value,provider FROM contact WHERE userid=%d AND enabled=1", $row['userid']);
			$user = $this->qry($query);
			while ($info = mysql_fetch_assoc($user)) {
				if (strlen($info['provider']) > 0) {
					$sms_count += $this->send_sms($info['provider'], $info['value'], $msg);
				}
				else {
					$email_count += $this->send_email($info['value'], $msg);
				}
			}
			mysql_free_result($user);
		}
		mysql_free_result($result);

		$this->save_message($msg, $id);
	}

	function save_message($msg, $id) {
		$query = sprintf("INSERT INTO messages VALUES (0, '%s', TIME(CONVERT_TZ(UTC_TIMESTAMP, 'GMT', 'US/Central')), CURDATE(), %d)", $msg, $id);
		$this->qry($query);
	}

	function send_email($to, $msg) {
		$subject = sprintf("Rideshare Notification");
		$headers = "From: ".$this->email_account."\r\n";
		$headers .= "Reply-To: ".$this->email_account."\r\n";
		$headers .= "Return-Path: <".$this->email_account.">\r\n";
		$headers .= "X-Mailer: PHP/".phpversion();

		if (empty($msg) || empty($to)) {
			printf("<h2>Email headers are empty</h2>");
			return 0;
		}

		if (!mail($to, $subject, $msg, $headers)) {
			printf("<h2>Unable to send mail</h2>");
			return 0;
		}
		return 1;
	}

	function send_exception_email($id, $date, $early, $late)
	{
		$date_string = date("l, \\t\h\e jS \of F", strtotime($date));
		if ($early == 0 && $late == 0) {
			$msg = sprintf("Updated schedule notice. On %s, the early shuttle is canceled, and the late shuttle is canceled.", $date_string);
		}
		else if ($early == 0) {
			$msg = sprintf("Updated schedule notice. On %s, the early shuttle is canceled, and %s is driving the late shuttle.",
					$date_string, $this->get_username($late));
		}
		else if ($late == 0) {
			$msg = sprintf("Updated schedule notice. On %s, %s is driving the early shuttle, and the late shuttle is canceled.",
					$date_string, $this->get_username($early));
		}
		else {
			$msg = sprintf("Updated schedule notice. On %s, %s is driving the early shuttle, and %s is driving the late shuttle.",
					$date_string, $this->get_username($early), $this->get_username($late));
		}
		$this->process_message($id, $msg);
	}

	function send_sms($carrier, $to, $msg) {
		$headers = "From: ".$this->email_account."\r\n";
		
		if ((empty($carrier)) || (empty($to)) || (empty($msg))) {
			printf("<h2>Empty to for SMS</h2>");
			return 0;
		}
		else if ($carrier == "verizon") {
			$formatted_number = $to."@vtext.com";
		}
		else if ($carrier == "tmobile") {
			$formatted_number = $to."@tmomail.net";
		}
		else if ($carrier == "sprint") {
			$formatted_number = $to."@messaging.sprintpcs.com";
		}
		else if ($carrier == "att") {
			$formatted_number = $to."@mms.att.net";
		
		else if ($carrier == "virgin") {
			$formatted_number = $to."@vmobl.com";
		}
		else if ($carrier == "cricket") {
			$formatted_number = $to."@mms.cricketwireless.net";
		}
		else if ($carrier == "boost") {
			$formatted_number = $to."@myboostmobile.com";
		}
		else if ($carrier == "alltel") {
			$formatted_number = $to."@mms.alltelwireless.com";
		}
		else if ($carrier == "uscellular") {
			$formatted_number = $to."@mms.uscc.com";
		}
		else if ($carrier == "republic") {
			$formatted_number = $to."@text.republicwireless.com";
		}
		mail($formatted_number, "", $msg, $headers, "-fadmin@contirideshare.net"); 
		return 1;
	}

	function msg_history($count) {
		$query = sprintf("SELECT * from messages ORDER BY (id) DESC LIMIT %d", $count);
		$result = $this->qry($query);

		printf("<table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n   <tr><th>Date<th>Time<th>Message<th>From</tr>\n");
		while ($row = mysql_fetch_assoc($result)) {
			printf("    <tr>\n     <td align=\"center\">%s</td><td>%s</td><td width=\"300\">%s</td><td>%s</td>\n    </tr>\n",
				$row['mdate'], $row['mtime'], $row['message'], $this->get_username($row['userid']));
		}
		printf("   </table>\n");
		mysql_free_result($result);
	}

	function process_registration($username, $pass, $fname, $lname, $email)
	{
		$query = sprintf("INSERT INTO users VALUES(0,'%s','%s','%s','%s',MD5('%s'),0,0,0)",
						$fname, $lname, $email, $username, $pass);
		$result = $this->qry($query);
		$this->login($username, $pass);
		header("Location: index.php");
	}
	
	function check_username($username)
	{
		$query = sprintf("SELECT userid FROM users WHERE username='%s'", $username);
		$result = $this->qry($query);
		
		$count = mysql_num_rows($result);
		mysql_free_result($result);
		return $count;
	}
	
	function get_username($id)
	{
		if ($id == 0) {
			return "Canceled";
		}

		$query = sprintf("SELECT fname,lname FROM users WHERE userid=%d", $id);
		$result = $this->qry($query);

		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_assoc($result);
			$driver = sprintf("%s %s", $row['fname'], $row['lname']);
			mysql_free_result($result);
			return $driver;
		}
	}

	function print_contacts($id)
	{
		$contact = NULL;
		$query = sprintf("SELECT * from contact WHERE userid=%d", $id);
		$results = $this->qry($query);
		
		if (mysql_num_rows($results) >= 1) {
			printf("    <table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n");
			printf("        <tr><th>Contact detail<th>Enabled<th>Delete</tr>\n");
			while ($info = mysql_fetch_assoc($results)) {
				if (strlen($info['provider']) > 0) {
					if ($info['provider'] == "verizon") {
						$contact = $info['value']."@vtext.com";
					}
					else if ($info['provider'] == "tmobile") {
						$contact = $info['value']."@tmomail.net";
					}
					else if ($info['provider'] == "sprint") {
						$contact = $info['value']."@messaging.sprintpcs.com";
					}
					else if ($info['provider'] == "att") {
						$contact = $info['value']."@txt.att.net";
					}
					else if ($info['provider'] == "virgin") {
						$contact = $info['value']."@vmobl.com";
					}
					else if ($info['provider'] == "alltel") {
						$contact = $info['value']."@mms.alltelwireless.com";
					}
					else if ($info['provider'] == "boost") {
						$contact = $info['value']."@myboostmobile.com";
					}
					else if ($info['provider'] == "cricket") {
						$contact = $info['value']."@mms.cricketwireless.com";
					}
					else if ($info['provider'] == "uscellular") {
						$contact = $info['value']."@email.uscc.com";
					}
					else if ($info['provider'] == "republic") {
						$contact = $info['value']."@text.republicwireless.com";
					}
				}
				else {
					$contact = $info['value'];
				}
				if ($info['enabled'] == 0) {
					$enabled = "";
				}
				else {
					$enabled = "checked";
				}

				printf("<tr>\n");
				printf(" <form method=\"post\" name=\"setenabled\" id=\"register\" class=\"register\" enctype=\"application/x-www-form-urlencoded\" action=\"changestate.php\">\n");
				printf("  <td width=\"200\">%s</td>\n", $contact);
				printf("  <td width=\"50\" align=\"center\"><input type=\"checkbox\" name=\"enabled\" value=\"%s\" %s/></td>\n", $enabled, $enabled);
				printf("  <td width=\"50\" align=\"center\"><input type=\"checkbox\" name=\"delete\"/></td>\n");
				printf("  <td><input name=\"action\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $info['id']);
				printf("  <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\"></td>\n");
				printf(" </form>\n</tr>\n");
			}
			printf("       </table>\n");
		}
		mysql_free_result($results);
	}
	
	function update_pass($id, $pass1, $pass2)
	{
		$rval = 0;
		if (strcmp($pass1, $pass2) != 0) {
			return 1;
		}
		
		$query = sprintf("UPDATE users SET password=MD5('%s') WHERE userid=%d", $pass1, $id);
		$result = $this->qry($query);
		if ($this->error == "Error") {
			$rval = 2;
		}
		return $rval;
	}
	
	function insert_phone_number($userid, $carrier, $phone)
	{
		$query = sprintf("INSERT INTO contact VALUES(0,%d,'%s','%s',TRUE)", $userid, $carrier, $phone);
		$this->qry($query);
	}

	function insert_email($userid, $email)
	{
		$query = sprintf("INSERT INTO contact VALUES(0,%d,'','%s',TRUE)", $userid, $email);
		$this->qry($query);
	}

	function insert_contact($id, $email, $phone, $carrier)
	{
		if (strlen($phone) > 0) {
			$query = sprintf("INSERT INTO contact VALUES(0, %d, '%s', '%s', TRUE)",	$id, $carrier, $phone);
			$rval = $phone;
		}
		else  {
			$query = sprintf("INSERT INTO contact VALUES(0, %d, '', '%s', TRUE)", $id, $email);
			$rval = $email;
		}
		$result = $this->qry($query);
		if ($this->error == "Error") {
			$rval = $this->error;
		}
		return $rval;
	}
	
	function create_contact_form($userid)
	{
		printf("<table border=\"0\" cellpadding=\"5\" id=\"messaging\">\n");
		printf(" <tr>\n");
		printf("  <form name=\"createcontact\" method=\"post\" id=\"contact\" class=\"contact\" enctype=\"application/x-www-form-urlencoded\" action=\"addemail.php\">\n");
		printf("  <td width=\"200\"><label class=\"update\" for=\"email\">New Email Address</label></td>\n");
		printf("  <td><input name=\"email\" id=\"email\" type=\"text\" width=\"200\"></td>\n");
		printf("  <td width=\"200\"></td>\n");
		printf("  <td><input name=\"action\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $userid);
		printf("      <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\">\n");
		printf("  </td>\n </form>\n </tr>\n");
		printf(" <tr>\n");
		printf("  <form name=\"createcontact\" method=\"post\" id=\"contact\" class=\"contact\" enctype=\"application/x-www-form-urlencoded\" action=\"addphone.php\">\n");
		printf("  <td><label class=\"update\" for=\"phone\">Phone # (no dashes or braces)</label></td>\n");
		printf("  <td><input name=\"phone\" id=\"phone\" type=\"text\"></td>\n");
		printf("  <td><select name=\"carrier\" id=\"carrier\">\n");
		printf("      <option value=\"verizon\">Verizon</option>\n");
		printf("      <option value=\"tmobile\">T-Mobile</option>\n");
		printf("      <option value=\"sprint\">Sprint</option>\n");
		printf("      <option value=\"att\">ATT</option>\n");
		printf("      <option value=\"virgin\">Virgin Mobile</option>\n");
		printf("      <option value=\"boost\">Boost Mobile</option>\n");
		printf("      <option value=\"cricket\">Cricket Wireless</option>\n");
		printf("      <option value=\"uscellular\">US Cellular</option>\n");
		printf("      <option value=\"alltel\">Alltel Wireless</option>\n");
		printf("      <option value=\"republic\">Republic Wireless</option>\n");
		printf("      </select>\n  </td>\n");
		printf("  <td><input name=\"action\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $userid);
		printf("      <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\">\n");
		printf("  </td>\n </form>\n </tr>\n</table>\n");
	}

	function enable_contact($id, $value)
	{
		$query = sprintf("UPDATE contact SET enabled=%d WHERE id=%d", $value, $id);
		$this->qry($query);
		
		return $this->error;
	}
	
	function delete_contact($value)
	{
		$query = sprintf("DELETE FROM contact WHERE id=%d", $value);
		$this->qry($query);
	}

	function driver_exceptions()
	{
		$query = sprintf("SELECT * FROM exceptions WHERE date<DATE_ADD(CURDATE(), INTERVAL '14' DAY) AND date>=CURDATE() ORDER BY date ASC LIMIT 10");
		$result = $this->qry($query);

		printf("<table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n   <tr><th>Date<th>DOW<th>Early Shuttle<th>Late Shuttle</tr>\n");
		while ($row = mysql_fetch_assoc($result)) {
			$dow = date("l", strtotime($row['date']));
			$earlydriver = $this->get_username($row['early']);
			$latedriver = $this->get_username($row['late']);
			$ld = "driver";
			$ed = "driver";
			if ($row['early'] == 0) {
				$ed = "nodriver";
			}
			if ($row['late'] == 0) {
				$ld = "nodriver";
			}

			printf("  <tr>\n   <td>%s</td>\n   <td>%s</td>\n   <td class=\"%s\">%s</td>\n   <td class=\"%s\">%s</td>\n  </tr>\n", $row['date'], $dow, $ed, $earlydriver, $ld, $latedriver);
		}

		printf("</table>");
		mysql_free_result($result);
	}

	function print_current_exceptions()
	{
		$count = 1;
		$query = sprintf("SELECT * FROM exceptions WHERE date<DATE_ADD(CURDATE(), INTERVAL '14' DAY) AND date>=CURDATE() ORDER BY date ASC");
		$result = $this->qry($query);

		printf("<table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n   <tr><th>Early Driver<th>Late Driver<th>Date<th>Remove</tr>\n");
		while ($row = mysql_fetch_assoc($result)) {
			$dow = date("l", strtotime($row['date']));
			$earlydriver = $this->get_username($row['early']);
			$latedriver = $this->get_username($row['late']);
			$ld = "driver";
			$ed = "driver";
			if ($row['early'] == 0) {
				$ed = "nodriver";
			}
			if ($row['late'] == 0) {
				$ld = "nodriver";
			}

			printf("   <tr>\n");
			printf("    <form name=\"e%d\" method=\"post\" id=\"e%d\" class=\"exception\" enctype=\"application/x-www-form-urlencoded\" action=\"editexception.php\">\n", $row['id'], $row['id']);
			printf("    <td><select name=\"early\" id=\"earlydriver\">\n");
			$this->driver_exception_dropdown($row['early']);
			printf("    </td>\n    <td><select name=\"late\" id=\"latedriver\">\n");
			$this->driver_exception_dropdown($row['late']);
			printf("    </td>\n");
			printf("    <td><input name=\"newexceptiondate\" id=\"newexceptiondate\" type=\"text\" value=\"%s\"></td>\n", $row['date']);
			printf("    <td><center><input name=\"delete\" id=\"delete\" value=\"%d\" type=\"checkbox\"></center></td>\n", $row['id']);
			printf("    <td><input name=\"rowid\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $row['id']);
			printf("        <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\"></td>\n");
			printf("    </form>\n    </tr>\n");
		}

		printf("</table>\n");

		mysql_free_result($result);
	}

	function print_newest_exceptions()
	{
		$count = 10;
		$query = sprintf("SELECT * FROM exceptions ORDER BY id DESC LIMIT 10");
		$result = $this->qry($query);

		printf("<table border=\"1\" cellpadding=\"5\" id=\"messaging\">\n   <tr><th>ID<th>Date<th>DOW<th>Early Driver<th>Late Driver</tr>\n");
		while ($row = mysql_fetch_assoc($result)) {
			$dow = date("l", strtotime($row['date']));
			$earlydriver = $this->get_username($row['early']);
			$latedriver = $this->get_username($row['late']);
			$ld = "driver";
			$ed = "driver";
			if ($row['early'] == 0) {
				$ed = "nodriver";
			}
			if ($row['late'] == 0) {
				$ld = "nodriver";
			}

			printf("  <tr>\n   <td><a href=\"editexception.php?exceptionid=%d\">%d</a></td>\n", $row['userid'], $count--);
			printf("   <td>%s</td>\n   <td>%s</td>\n   <td class=\"%s\">%s</td>\n   <td class=\"%s\">%s</td>\n  </tr>\n", $row['date'], $dow, $ed, $earlydriver, $ld, $latedriver);
		}

		printf("</table>");
		mysql_free_result($result);
	}

	function driver_exception_dropdown($id)
	{
		$query = sprintf("SELECT * FROM users WHERE userid!=%d AND active=1 AND type>0 ORDER BY type DESC", $id);
		$result = $this->qry($query);

		printf("<option value=\"%d\">%s</option>\n", $id, $this->get_username($id));
		while ($row = mysql_fetch_assoc($result)) {
			printf("<option value=\"%d\">%s</option>\n", $row['userid'], $this->get_username($row['userid']));
		}
		mysql_free_result($result);
	}

	function driver_exception_form()
	{
		$query = sprintf("SELECT userid FROM users WHERE type=4");
		$result = $this->qry($query);
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			$earlyid = $row[0];
		}
		mysql_free_result($result);

		$query = sprintf("SELECT userid FROM users WHERE type=3");
		$result = $this->qry($query);
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			$lateid = $row[0];
		}
		mysql_free_result($result);
		
		printf("	<table  border=\"1\" cellpadding=\"5\" id=\"messaging\">\n");
		printf("     <tr><th>Early Shuttle<th>Late Shuttle<th>Date (YYYY-MM-DD)</tr>\n      <tr>\n");
		printf("      <td><select name=\"early\" id=\"earlydriver\">\n");
		$this->driver_exception_dropdown($earlyid);
		printf("      </td>\n");
		printf("      <td><select name=\"late\" id=\"latedriver\">\n");
		$this->driver_exception_dropdown($lateid);
		printf("      <td><input name=\"exceptiondate\" id=\"exceptiondate\" type=\"text\"></td>\n");
		printf("     </tr>\n    </table>\n");
	}

	function add_driver_exception($id, $early, $late, $date)
	{
		$query = sprintf("INSERT INTO exceptions (id,date,early,late) VALUES(0,'%s',%d,%d)", $date, $early, $late);
		if ($result = $this->qry($query)) {
			$this->send_exception_email($id, $date, $early, $late);
		}
	}

	function edit_exception_form($id)
	{
		$query = sprintf("SELECT * FROM exceptions WHERE id=%d", $id);
		$result = $this->qry($query);

		printf("	<table  border=\"1\" cellpadding=\"5\" id=\"messaging\">\n     <tr>\n");
		printf("     <tr><th>Early Shuttle<th>Late Shuttle<th>Date (YYYY-MM-DD)<th>Remove Change</tr>\n      <tr>\n");
		printf("      <td><select name=\"early\" id=\"earlydriver\">\n");
		$row = mysql_fetch_assoc($result);
		printf("          <option value=\"%d\">%s</option>\n", $row['early'], $this->get_username($row['early']));

		$query2 = sprintf("SELECT * FROM users WHERE driver=1 AND active=1 AND userid!=%d ORDER BY type DESC", $row['early']);
		$result2 = $this->qry($query2);
		while ($drivers = mysql_fetch_assoc($result2)) {
			printf("          <option value=\"%d\">%s</option>\n", $drivers['userid'], $this->get_username($drivers['userid']));
		}
		mysql_free_result($result2);

		printf("      </td>\n");
		printf("      <td><select name=\"late\" id=\"latedriver\">\n");
		printf("          <option value=\"%d\">%s</option>\n", $row['late'], $this->get_username($row['late']));
		$query3 = sprintf("SELECT * FROM users WHERE driver=1 AND active=1 AND userid!=%d ORDER BY type DESC", $row['late']);
		$result3 = $this->qry($query3);
		while ($drivers = mysql_fetch_assoc($result3)) {
			printf("          <option value=\"%d\">%s</option>\n", $drivers['userid'], $this->get_username($drivers['userid']));
		}
		mysql_free_result($result3);
		printf("      </td>\n");
		printf("      <td><input name=\"newexceptiondate\" id=\"newexceptiondate\" type=\"text\" value=\"%s\"></td>\n", $row['date']);
		printf("      <td><input name=\"delexception\" id=\"delexception\" type=\"checkbox\" value=\"%d\"></td>\n", $id);
		printf("     </tr>\n    </table>\n");
	}

	function update_exception($userid, $id, $early, $late, $date)
	{
		$query = sprintf("UPDATE exceptions SET early=%d,late=%d,date='%s' WHERE id=%d", $early, $late, $date, $id);
		if ($result = $this->qry($query)) {
			$this->send_exception_email($userid, $date, $early, $late);
		}
	}

	function delete_exception($id)
	{
		$query = sprintf("DELETE FROM exceptions WHERE id=%d", $id);
		$result = $this->qry($query);
	}

	/* Print the navigation menu on the side. This is the only way to
	 * print a menu to avoid inconsistencies.
	 */
	function print_menu($id, $home)
	{
		if (!$home) {
			printf("   <h2><a class=\"menu\" href=\"index.php\">Home</a></h2>\n");
		}
		printf("  <h2><a class=\"menu\" href=\"account.php\">Manage Account</a></h2>\n");
		if ($this->is_admin($id)) {
			printf("  <h2><a class=\"menu\" href=\"admin.php\">Manage Users</a></h2>\n");
		}
		if ($this->is_driver($id) || $this->is_admin($id)) {
			printf("  <h2><a class=\"menu\" href=\"notify.php\">Send a Notification</a></h2>\n");
			printf("    <h2><a class=\"menu\" href=\"exception.php\">Driver Change</a></h2>\n");
		}
		printf("   <h2><a class=\"menu\" href=\"history.php\">View History</a></h2>\n");
		printf("   <h2><a class=\"menu\" href=\"http://northseminary.net/projects/rideshare\">Support</a></h2>\n");
		printf("   <h2><a class=\"menu\" href=\"logout.php\">Logout</a></h2>\n");
	}

	function print_admin_user_list($id)
	{
		if (!$this->is_admin($id)) {
			printf("<h1>You must be an admin to use this page</h1>\n");
			return;
		}
		
		$query = sprintf("SELECT * FROM users");
		$results = $this->qry($query);

		printf("<table  border=\"1\" cellpadding=\"5\" id=\"messaging\">\n");
		printf(" <tr><th>User ID<th>Username<th>First Name<th>Last Name<th>Active<th>Admin<th>Rider Type<th>Remove</tr>\n");
		while ($row = mysql_fetch_assoc($results)) {
			if ($row['userid'] == 0) {
				continue;
			}
			printf(" <tr>\n");
			printf("  <form name=\"rider_%d\" method=\"post\" id=\"rider_%d\" class=\"rider\" enctype=\"application/x-www-form-urlencoded\" action=\"updaterider.php\">\n", $row['userid'], $row['userid']);
			printf("   <td><center>%d</center></td>\n", $row['userid']);
			printf("   <td>%s</td>\n", $row['username']);
			printf("   <td><input name=\"fname\" id=\"fname\" type=\"text\" value=\"%s\"/></td>\n", $row['fname']);
			printf("   <td><input name=\"lname\" id=\"lname\" type=\"text\" value=\"%s\"/></td>\n", $row['lname']);
			if ($row['active'] == 1) {
				printf("   <td><center><input name=\"active\" id=\"active\" type=\"checkbox\" value=\"1\" checked/></center></td>\n");
			}
			else {
				printf("   <td><center><input name=\"active\" id=\"active\" type=\"checkbox\" value=\"1\"/></center></td>\n");
			}
			if ($row['admin'] == 1) {
				printf("   <td><center><input name=\"admin\" id=\"admin\" type=\"checkbox\" value=\"1\" checked/></center></td>\n");
			}
			else {
				printf("   <td><center><input name=\"admin\" id=\"admin\" type=\"checkbox\" value=\"1\"/></center></td>\n");
			}
			printf("   <td><select name=\"type\" id=\"type\">\n");
			switch ($row['type']) {
			case 0:
				printf("       <option value=\"0\">Rider</option>\n");
				printf("       <option value=\"2\">Alternate</option>\n");
				printf("       <option value=\"4\">Primary Early</option>\n");
				printf("       <option value=\"3\">Primary Late</option>\n");
				break;
			case 2:
				printf("       <option value=\"2\">Alternate</option>\n");
				printf("       <option value=\"0\">Rider</option>\n");
				printf("       <option value=\"4\">Primary Early</option>\n");
				printf("       <option value=\"3\">Primary Late</option>\n");
				break;
			case 3:
				printf("       <option value=\"3\">Primary Late</option>\n");
				printf("       <option value=\"0\">Rider</option>\n");
				printf("       <option value=\"2\">Alternate</option>\n");
				printf("       <option value=\"4\">Primary Early</option>\n");
				break;
			case 4:
				printf("       <option value=\"4\">Primary Early</option>\n");
				printf("       <option value=\"0\">Rider</option>\n");
				printf("       <option value=\"2\">Alternate</option>\n");
				printf("       <option value=\"3\">Primary Late</option>\n");
				break;
			default:
				printf("       <option value=\"0\">Error</option>\n");
				break;
			}
			printf("       </select></td>\n");
			printf("   <td><center><input name=\"delete\" id=\"delete\" type=\"checkbox\" value=\"1\"/></center></td>\n");
			printf("   <td><input name=\"rowid\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $row['userid']);
			printf("       <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\"></td>\n");
			printf("  </form>\n");
			printf(" </tr>\n");
		}
		printf("</table>\n");
		mysql_free_result($results);
	}

	function update_rider($userid, $fname, $lname, $active, $admin, $type) 
	{
		$query = sprintf("UPDATE users SET fname='%s',lname='%s',active=%d,admin=%d,type=%d WHERE userid=%d", $fname, $lname, $active, $admin, $type, $userid);
		$this->qry($query);
	}

	function delete_rider($userid)
	{
		$query = sprintf("DELETE FROM users WHERE userid=%d", $userid);
		$this->qry($query);
	}

	function recover_username($email)
	{
		$query = sprintf("SELECT username FROM users WHERE email='%s'", $email);
		$results = $this->qry($query);

		if (mysql_num_rows($results) == 1) {
			$row = mysql_fetch_assoc($results);
			$msg = sprintf("Your Conti Rideshare username is '%s'.", $row['username']);
			$this->send_email($email, $msg);
		}
		mysql_free_result($results);
	}

	function reset_password($email)
	{
		$newpass = $this->create_password();
		$query = sprintf("UPDATE users SET password=MD5('%s') WHERE email='%s'", $newpass, $email);
		$results = $this->qry($query);

		$msg = sprintf("The new password for your account is '%s'. Please change it as soon as you login.", $newpass);
		$this->send_email($email, $msg);
	}

	function update_contact_email($userid)
	{
		$query = sprintf("SELECT email FROM users WHERE userid='%s'", $userid);
		$results = $this->qry($query);
		if (mysql_num_rows($results) == 1) {
			$row = mysql_fetch_assoc($results);

			printf("<table border=\"0\" cellpadding=\"5\" id=\"messaging\">\n");
			printf(" <tr>\n");
			printf("  <form name=\"updatecontactemail\" method=\"post\" id=\"updatecontact\" class=\"contact\" enctype=\"application/x-www-form-urlencoded\" action=\"updatecontactemail.php\">\n");
			printf("  <td width=\"200\"><label class=\"update\" for=\"emailaddr\">Primary Contact</label></td>\n");
			printf("  <td><input name=\"emailaddr\" id=\"emailaddr\" type=\"text\" width=\"200\" value=\"%s\"></td>\n", $row['email']);
			printf("  <td width=\"200\"></td>\n");
			printf("  <td><input name=\"action\" id=\"action\" value=\"%d\" type=\"hidden\">\n", $userid);
			printf("      <input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\">\n");
			printf("  </td>\n </form>\n </tr>\n</table>\n");
		}
		mysql_free_result($results);
	}

	function update_email_address($userid, $email)
	{
		$query = sprintf("UPDATE users SET email='%s' WHERE userid=%d", $email, $userid);
		$results = $this->qry($query);
	}
}
?>
