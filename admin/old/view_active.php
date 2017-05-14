<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>View Active Users</title>
</head>
<body>
<?php
require("constants.php");
require("database.php");
$database = new MySQLDB(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if(!defined('TBL_ACTIVE_USERS')) {
  die("Error processing page");
}
$query = "SELECT username FROM ".TBL_ACTIVE_USERS." ORDER BY timestamp DESC,username";
$stmt = $database->query($query);
/* Error occurred, return given name by default */
$num_rows = $stmt->num_rows;
if($num_rows < 0){
   echo "Error displaying info";
}
else if ($num_rows > 0) {
   /* Display active users, with link to their info */
   echo '<table align="left" border="1" cellspacing="0" cellpadding="3"><tr><td>';
   while($r = $stmt->fetch_assoc()){
	   echo '<a href="userinfo.php?user=',$r[username],'">',$r[username],'</a> / ';
   }
   echo '</td></tr></table><br>';
}
?>
</body>
</html>