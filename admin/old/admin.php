<?php
/**
 * Admin.php
 *
 * This is the Admin Center page. Only administrators
 * are allowed to view this page. This page displays the
 * database table of users and banned users. Admins can
 * choose to delete specific users, delete inactive users,
 * ban users, update user levels, etc.
 */
require("session.php");
/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers(){
   global $database;
   $query = "SELECT username,userlevel,email,timestamp FROM ".TBL_USERS." ORDER BY userlevel DESC,username";
   $stmt = $database->query($query);
   /* Display table contents */
   echo '<table align="left" border="1" cellspacing="0" cellpadding="3"><tr>
   <td><b>Username</b></td>
   <td><b>Level</b></td>
   <td><b>Email</b></td>
   <td><b>Last Active</b></td>
   </tr>';
   while($r=$stmt->fetch_assoc()) {
      echo '<tr>
	  <td>',$r['username'],'</td>
	  <td>',$r['userlevel'],'</td>
	  <td>',$r['email'],'</td>
	  <td>',$r['timestamp'],'</td>
	  </tr>';
   }
   echo '</table><br>';
}

/**
 * displayBannedUsers - Displays the banned users
 * database table in a nicely formatted html table.
 */
function displayBannedUsers(){
   global $database;
   $q = "SELECT username,timestamp FROM ".TBL_BANNED_USERS." ORDER BY username";
   $stmt = $database->query($q);
   /* Display table contents */
   echo '<table align="left" border="1" cellspacing="0" cellpadding="3"><tr>
   <td><b>Username</b></td><td><b>Time Banned</b></td></tr>';
   while($r=$stmt->fetch_assoc()) {
      echo '<tr>
	  <td>',$uname,'</td>
	  <td>',$time,'</td>
	  </tr>';
   }
   echo '</table><br>';
}
   
/**
 * User not an administrator, redirect to main page
 * automatically.
 */
if(!$session->isAdmin()){
   header("Location: http://".$_SERVER['SERVER_NAME']."/main.php");
} else {
/**
 * Administrator is viewing page, so display all
 * forms.
 */
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - AwfulContent.com</title>
</head>
<body>
<h1>Admin Center</h1>
<hr>
<p>Logged in as <b><?php echo $session->username; ?></b></p>
<p>Back to [<a href=<?php "http://".$_SERVER['SERVER_NAME']."/main.php" ?>>Main Page</a>]</p>
<?php
if($form->getNumErrors() > 0){
   echo '<p><span class="error">'
       ."!*** Error with request, please fix</span></p>";
}
?>
<table align="left" border="0" cellspacing="5" cellpadding="5">
<tr><td>
<?php
/**
 * Display Users Table
 */
?>
<h3>Users Table Contents:</h3>
<?php
displayUsers();
?>
</td></tr>
<tr>
<td>
<br>
<h3>Update User Level</h3>
<?php echo $form->getError("upduser"); ?>
<table>
<form action="adminprocess.php" method="POST">
<tr><td>
Username:<br>
<input type="text" name="upduser" maxlength="30" value="<?php echo $form->getValue("upduser"); ?>">
</td>
<td>
Level:<br>
<select name="updlevel">
<option value="1">1</option>
<option value="9">9</option>
</select>
</td>
<td>
<br>
<input type="hidden" name="subupdlevel" value="1">
<input type="submit" value="Update Level">
</td></tr>
</form>
</table>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>

<h3>Delete User</h3>
<?php echo $form->getError("deluser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="deluser" maxlength="30" value="<?php echo $form->getValue("deluser"); ?>">
<input type="hidden" name="subdeluser" value="1">
<input type="submit" value="Delete User">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<h3>Delete Inactive Users</h3>
This will delete all users (not administrators), who have not logged in to the site<br>
within a certain time period. You specify the days spent inactive.<br><br>
<table>
<form action="adminprocess.php" method="POST">
<tr><td>
Days:<br>
<select name="inactdays">
<option value="3">3</option>
<option value="7">7</option>
<option value="14">14</option>
<option value="30">30</option>
<option value="100">100</option>
<option value="365">365</option>
</select>
</td>
<td>
<br>
<input type="hidden" name="subdelinact" value="1">
<input type="submit" value="Delete All Inactive">
</td>
</form>
</table>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<h3>Ban User</h3>
<?php echo $form->getError("banuser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="banuser" maxlength="30" value="<?php echo $form->getValue("banuser"); ?>">
<input type="hidden" name="subbanuser" value="1">
<input type="submit" value="Ban User">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr><td>

<h3>Banned Users Table Contents:</h3>
<?php
displayBannedUsers();
?>
</td></tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>

<h3>Delete Banned User</h3>
<?php echo $form->getError("delbanuser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="delbanuser" maxlength="30" value="<?php echo $form->getValue("delbanuser"); ?>">
<input type="hidden" name="subdelbanned" value="1">
<input type="submit" value="Delete Banned User">
</form>
</td>
</tr>
</table>
</body>
</html>
<?php
}
?>