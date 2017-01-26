<?php
/**
 * UserEdit.php
 *
 * This page is for users to edit their account information
 * such as their password, email address, etc. Their
 * usernames can not be edited. When changing their
 * password, they must first confirm their current password.
 */
require("session.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Edit User</title>
</head>
<body>

<?php
/**
 * User has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['useredit'])){
   unset($_SESSION['useredit']);
   echo '<h1>User Account Edit Success!</h1>
   <p><b>',$session->username,'</b>, your account has been successfully updated. <a href="../main.php">Main</a>.</p>';
} else {
?>

<?php
	/**
	 * If user is not logged in, then do not display anything.
	 * If user is logged in, then display the form to edit
	 * account information, with the current email address
	 * already in the field.
	 */
	if($session->logged_in){
?>
	<h1>User Account Edit : <?php echo $session->username; ?></h1>
<?php
	if($form->getNumErrors() > 0){
	   echo '<td><span style="background-color:#ff0000">',$form->getNumErrors(),' error(s) found</span></td>';
	}
?>
    <form action="process.php" method="POST">
    <table align="left" border="0" cellspacing="0" cellpadding="3">
    <tr>
    <td>Current Password:</td>
    <td><input type="password" name="curpass" maxlength="30" value="<?php echo $form->getValue("curpass"); ?>"></td>
    <td><?php echo $form->getError("curpass"); ?></td>
    </tr>
    <tr>
    <td>New Password:</td>
    <td><input type="password" name="newpass" maxlength="30" value="<?php echo $form->getValue("newpass"); ?>"></td>
    <td><?php echo $form->getError("newpass"); ?></td>
    </tr>
    <tr>
    <td>Email:</td>
    <?php
	$email = "";
    if($form->getValue("email") == ""){
       $email = $session->userinfo['email'];
    }else{
       $email = $form->getValue("email");
    }
    ?>
    <td><input type="text" name="email" maxlength="50" value="<?php echo $email; ?>">
    
    </td>
    <td><?php echo $form->getError("email"); ?></td>
    </tr>
    <tr><td colspan="2" align="right">
    <input type="hidden" name="subedit" value="1">
    <input type="submit" value="Edit Account"></td></tr>
    <tr><td colspan="2" align="left"></td></tr>
    </table>
    </form>

<?php
	}
}
?>
</body>
</html>