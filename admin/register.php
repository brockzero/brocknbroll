<?php
require("session.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Register User</title>
</head>
<body>
<?php
/**
 * The user is already logged in, not allowed to register.
 */
if($session->logged_in)
{
	echo '<h1>Registered</h1>
	<p>We&#39;re sorry <b>',$session->username,'</b>, but you&#39;ve already registered. <a href="../main.php">Main</a>.</p>';
}
/**
 * The user has submitted the registration form and the
 * results have been processed.
 */
else if(isset($_SESSION['regsuccess']))
{
   /* Registration was successful */
	if($_SESSION['regsuccess']) {
		echo "<h1>Registered!</h1>";
      	echo "<p>Thank you <b>".$_SESSION['reguname']."</b>, your information has been added to the database, you may now <a href=\"../main.php\">log in</a>.</p>";
   } else {
	   echo "<h1>Registration Failed</h1>";
	   echo "<p>We're sorry, but an error has occurred and your registration for the username <b>".$_SESSION['reguname']."</b>, could not be completed.<br>Please try again at a later time.</p>";
   	}
   	unset($_SESSION['regsuccess']);
   	unset($_SESSION['reguname']);
}
/**
 * The user has not filled out the registration form yet.
 * Below is the page with the sign-up form, the names
 * of the input fields are important and should not
 * be changed.
 */
else {
?>
<h1>Register</h1>
<?php
	if($form->getNumErrors() > 0)
	{
		echo '<td><span style="background-color: #ff0000">',$form->getNumErrors(),' error(s) found</span></td>';
	}
	?>
	<form action="process.php" method="POST">
	<table align="left" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td>Username:</td><td><input type="text" name="user" maxlength="30" value="<?php echo $form->getValue("user"); ?>"></td>
	<td><?php echo $form->getError("user"); ?></td>
	</tr>
	<tr>
	<td>Password:</td><td><input type="password" name="pass" maxlength="30" value="<?php echo $form->getValue("pass"); ?>"></td>
	<td><?php echo $form->getError("pass"); ?></td>
	</tr>
	<tr>
	<td>Email:</td><td><input type="text" name="email" maxlength="50" value="<?php echo $form->getValue("email"); ?>"></td>
	<td><?php echo $form->getError("email"); ?></td>
	</tr>
	<tr>
	<td colspan="2" align="right"><input type="hidden" name="subjoin" value="1"><input type="submit" value="Join!"></td>
	</tr>
	<tr>
	<td colspan="2" align="left"><a href="../main.php">Back to Main</a></td>
	</tr>
	</table>
	</form>
<?php
}
?>
</body>
</html>