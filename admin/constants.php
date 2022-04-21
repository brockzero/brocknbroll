<?php
//make address
//http://',$_SERVER['SERVER_NAME'],$_SERVER['REQUEST_URI']
	// Local
if ($_SERVER['SERVER_NAME'] == "localhost") {
	define("DB_SERVER", "127.0.0.1");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "db382481423");
	define("SITE_URL", "http://localhost/brocknbroll/");
	// Remote
} else {
	define("DB_SERVER", "db382481423.db.1and1.com");
	define("DB_USER", "dbo382481423");
	define("DB_PASS", "NickleBee19");
	define("DB_NAME", "db382481423");
	define("SITE_URL", "https://brocknbroll.com/");
}

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "Brock");
define("GUEST_NAME", "Mingebag");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL",  1);
define("GUEST_LEVEL", 0);

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 15);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Brock");
define("EMAIL_FROM_ADDR", "robert.wayne.brock@gmail.com");
define("EMAIL_WELCOME", false);
define("EMAIL_SUBJECT", "Welcome to Brock's Site");
define("EMAIL_SITE", "awfulcontent.com");

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);
//menu for updates

/*
Performance Tester: time to generate a page
###########################################
//beginning of page
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

//end of page
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);
echo "This page was created in ".$totaltime." seconds";
*/
?>