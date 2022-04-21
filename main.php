<?php
require_once("admin/session.php");
require_once("header.php");
echo '<div id="comicContainer">';

/*
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in){
   echo "<h2>Logged In</h2>";
   echo "<p>Welcome <b>$session->username</b>, you are logged in. <br><br>"
       ."[<a href=\"admin/userinfo.php?user=$session->username\">My Account</a>] &nbsp;&nbsp;"
       ."[<a href=\"admin/useredit.php\">Edit Account</a>] &nbsp;&nbsp;"
	   ."[<a href=\"update\">Update AwfulContent</a>] &nbsp;&nbsp;";
   if($session->isAdmin()){
      echo '[<a href="admin/admin.php">Admin Center</a>] &nbsp;&nbsp;';
   }
   echo "[<a href=\"admin/process.php\">Logout</a>]</p>";
} else {// else 1
?>

<h2>Login</h2>
<?php
/**
 * User not logged in, display the login form.
 * If user has already tried to login, but errors were
 * found, display the total number of errors.
 * If errors occurred, they will be displayed.
 */
if($form->getNumErrors() > 0){
   echo '<p>'.$form->getNumErrors().' error(s) found</p>';
}
?>
<form action="admin/process.php" method="POST">
<table>
<tr>
    <td>Username:</td>
    <td><input type="text" name="user" maxlength="30" value="<?php echo $form->getValue("user"); ?>"></td>
    <td><?php echo $form->getError("user"); ?></td>
</tr>
<tr>
	<td>Password:</td>
    <td><input type="password" name="pass" maxlength="30" value="<?php echo $form->getValue("pass"); ?>"></td>
    <td><?php echo $form->getError("pass"); ?></td>
</tr>
<tr>
	<td colspan="2"><input type="checkbox" name="remember" <?php if($form->getValue("remember") != ""){ echo "checked"; } ?>>
    Remember me next time
    <input type="hidden" name="sublogin" value="1">
    <input type="submit" value="Login"></td>
</tr>
<tr>
    <td colspan="2"><br>[<a href="admin/forgotpass.php">Forgot Password?</a>]</td>
    <td>&nbsp;</td>
</tr>
</table>
</form>
<?php
} //else 1 end
echo '</div>';
require_once("footer.php");
?>