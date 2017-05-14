<?php
require_once('../include/autoloader.php');
require_once("mailer.php");
require_once("form.php");
require_once("is_email.php");
//http://www.yiiframework.com/forum/index.php/topic/10927-a-using-statement-in-php/

class Session
{
	public $logged_in;    //True if user is logged in, false otherwise
	public $userinfo = array();  //The array holding all user info
	public $username;     //Username given on sign-up
	//public $referrer;     //Last recorded site page viewed
	private $url;          //The page url current being viewed
	private $userid;       //Random value generated on current login
	private $userlevel;    //The level to which the user pertains
	private $time;         //Time user was last active (page loaded)

	function __construct() {
		$this->time = time();
		$this->database = new DatabaseController(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		$this->Form = new Form();
		session_start();
	}

	function checkLogin() {
		$isLoggedIn = false;

		if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid']) && $_SESSION['username'] != GUEST_NAME){
			$this->username = $_SESSION['username'] = $_COOKIE['cookname'];
			$this->userid   = $_SESSION['userid']   = $_COOKIE['cookid'];
		}

		if(isset($_SESSION['username']) && isset($_SESSION['userid'])){
			if($this->database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0){
				unset($_SESSION['username']);
				unset($_SESSION['userid']);
				$isLoggedIn = false;
			}

			if(isset($_SESSION['username'])){
				$this->userinfo  = $this->database->getUserInfo($_SESSION['username']);
				$this->username  = $this->userinfo['username'];
				$this->userid    = $this->userinfo['userid'];
				$this->userlevel = $this->userinfo['userlevel'];
				$isLoggedIn = true;
			}
		} else {
			/* User not logged in */
			$isLoggedIn = false;
		}
		return $isLoggedIn;
	}
	
	function Login($subuser, $subpass, $subremember) {

		$isLoggedIn = false;
		
		//global $form; //figure out how to stop globals

		$field = "user";  //Use field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) == 0) {
			$this->Form->setError($field, "* Username not entered");
		} else {
			/* Check if username is not alphanumeric */
			if(!ctype_alnum($subuser)){
				$this->Form->setError($field, "* Username not alphanumeric");
			}
		}
		/* Password error checking */
		$field = "pass";  //Use field name for password
		if(!$subpass){
			$this->Form->setError($field, "* Password not entered");
		}
		/* Return if form errors exist */
		if($this->Form->getNumErrors() > 0){
			$isLoggedIn = false;
		}
		/* Checks that username is in database and password is correct */
		$hash = $this->database->getUserPass($subuser);
		if ($hash == 0) {
			$field = "user";
			$this->Form->setError($field, "* Username not found");
		}
		$result = $this->comparePassword($subpass, $hash);
		/* Check error codes */
		if($result == 0) {
			$field = "pass";
			$this->Form->setError($field, "* Invalid password");
		}
		
		/* Return if form errors exist */
		if($this->Form->getNumErrors() > 0){
			$isLoggedIn = false;
		}
		/* Username and password correct, register session variables */
		$this->userinfo  = $this->database->getUserInfo($subuser);
		$this->username  = $_SESSION['username'] = $this->userinfo['username'];
		$this->userid    = $_SESSION['userid']   = $this->generateRandID();
		$this->userlevel = $this->userinfo['userlevel'];
		/* Insert userid into database and update active users table */
		$this->database->updateUserID($this->username,$this->userid);
		$this->database->addActiveUser($this->username, $this->time);
		
		/**
		* The user has requested that his password be remembered.
		* He's logged in, so we set two cookies. One to hold his username,
		* and one to hold his random value userid. It expires by the time
		* specified in constants.php. Now, next time he comes to our site, we will
		* log him in automatically, but only if he didn't log out before he left.
		* And only if the userid matches what's in the database currently.
		*/
		if($subremember){
			setcookie("cookname", $this->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
			setcookie("cookid",   $this->userid,   time()+COOKIE_EXPIRE, COOKIE_PATH);
		}
		/* Login completed successfully */
		if($_SERVER['SERVER_NAME'] != 'localhost')
		{
			$this->database->closeDB();
		}
		return true;
	}
	
	function logout() {
		/**
		* Delete cookies - the time must be in the past which autmatically invalidates them
		*/
		if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
			 setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
			 setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
		}
		session_unset();  //frees seesion variables
		session_destroy(); //destroys all of the data associated with the current session. Does not affect $_SESSION globals or cookies
		$_SESSION = array(); //reintializes $_SESSION so that all pre-existing data is cleared/overwritten
		$this->logged_in = false; // sets the logged in var to false
		$this->database->removeActiveUser($this->username); // remove from active_users and add to active guests
		$this->username  = GUEST_NAME;
		$this->userlevel = GUEST_LEVEL;
	}
	
	/**
	* register - Gets called when the user has just submitted the
	* registration form. Determines if there were any errors with
	* the entry fields, if so, it records the errors and returns 1.
	* If no errors were found, it registers the new user and
	* returns 0. Returns 2 if registration failed.
	*/
	function register($subuser, $subpass, $subemail) {
		global $form, $mailer;
		/* Username error checking */
		$field = "user";  //Use field name for username
		if(!$subuser || strlen($subuser = trim($subuser)) == 0){
			$form->setError($field, "* Username not entered");
		} else {
		 	/* Spruce up username, check length */
			if(strlen($subuser) < 5){
				$form->setError($field, "* Username below 5 characters");
		 	}
		 	else if(strlen($subuser) > 30) {
				$form->setError($field, "* Username above 30 characters");
		 	}
		 	/* Check if username is not alphanumeric */
		 	else if(!ctype_alnum($subuser)){
				$form->setError($field, "* Username not alphanumeric");
		 	}
		 	/* Check if username is reserved */
		 	else if(strcasecmp($subuser, GUEST_NAME) == 0){
				$form->setError($field, "* Username reserved word");
		 	}
		 	/* Check if username is already in use */
		 	else if($database->usernameTaken($subuser)){
				$form->setError($field, "* Username already in use");
		 	}
		}
		
		/* Password error checking */
		$field = "pass";  //Use field name for password
		if(!$subpass){
			$form->setError($field, "* Password not entered");
		} else {
		 	/* Spruce up password and check length*/
		 	if(strlen($subpass) < 8){
				$form->setError($field, "* Password too short");
		 	} else if(!ctype_alnum($subpass = trim($subpass))){
				$form->setError($field, "* Password not alphanumeric");
			}
		}
		
		/* Email error checking
		* using IS_EMAIL
		* http://www.dominicsayers.com/isemail
		*/
		$field = "email";  //Use field name for email
		if(!$subemail || strlen($subemail = trim($subemail)) == 0){
			$form->setError($field, "* Email not entered");
		} else {
			$result = is_email($subemail, true, true);
			if ($result === ISEMAIL_VALID) {
				// if true do nothing skip the other steps
			} else if ($result < ISEMAIL_THRESHOLD) {
				$form->setError($field, "Warning! ".$subemail." has unusual features (result code ".$result.")");
			} else {
				$form->setError($field, $subemail." is not a valid email address (result code ".$result.")");
			}
		}
	
		/* Errors exist, have user correct them */
		if($form->getNumErrors() > 0){
			return 1;  //Errors with form
		} else {
			$password = $this->getPasswordHash($subpass);
			if($database->addNewUser($subuser, $password, $subemail)){
				if(EMAIL_WELCOME){
			   		$mailer->sendWelcome($subuser,$subemail,$subpass);
				}
				return 0;  //New user added succesfully
			} else {
				return 2;  //Registration attempt failed
			}
		}
	}
	
	/**
	* editAccount - Attempts to edit the user's account information
	* including the password, which it first makes sure is correct
	* if entered, if so and the new password is in the right
	* format, the change is made. All other fields are changed
	* automatically.
	*/
	function editAccount($subcurpass, $subnewpass, $subemail) {
		global $form;
		/* New password entered */
		if($subnewpass){
			/* Current Password error checking */
			$field = "curpass";  //Use field name for current password
			if(!$subcurpass){
				$form->setError($field, "* Current Password not entered");
			}  else {
			/* Check if password too short or is not alphanumeric */
				if(strlen($subcurpass) < 8 || !ctype_alnum($subcurpass = trim($subcurpass))){
					$form->setError($field, "* Current Password too short or not alphanum");
				}
			/* Password entered is incorrect */
				if($this->confirmUserPass($this->username,$subcurpass) == 0){
			   		$form->setError($field, "* Current Password incorrect");
				}
			}
			/* New Password error checking */
			$field = "newpass";  //Use field name for new password
			/* Spruce up password and check length*/
			if(strlen($subnewpass) < 8){
				$form->setError($field, "* New Password too short");
			}
			/* Check if password is not alphanumeric */
			else if(!ctype_alnum($subnewpass = trim($subnewpass))){
				$form->setError($field, "* New Password not alphanumeric");
			}
		}
		/* Change password attempted */
		else if($subcurpass){
	 	/* New Password error reporting */
	 		$field = "newpass";  //Use field name for new password
	 		$form->setError($field, "* New Password not entered");
		}
	
		// Email error checking 
		$field = "email";  //Use field name for email
		if($subemail && strlen($subemail = trim($subemail)) > 0){
	 		$result = is_email($subemail, true, true);
			if ($result === ISEMAIL_VALID) {
				// if true do nothing skip the other steps
			} else if ($result < ISEMAIL_THRESHOLD) {
				$form->setError($field, "Warning! ".$subemail." has unusual features (result code ".$result.")");
			} else {
				$form->setError($field, $subemail." is not a valid email address (result code ".$result.")");
			}
		}
	
		// Errors exist, have user correct them
		if($form->getNumErrors() > 0){
	 		return false;  //Errors with form
		}
	
		// Update password since there were no errors
		if($subcurpass && $subnewpass){
			$database->updateUserPassword($this->username, $this->getPasswordHash($subnewpass));
		}
		
		// Change Email
				if($subemail){
			$database->updateUserEmail($this->username,$subemail);
		}
		// Success!
		return true;
	}
	
	/**
	* isAdmin - Returns true if currently logged in user is
	* an administrator, false otherwise.
	*/
	function isAdmin(){
		return ($this->userlevel == ADMIN_LEVEL || $this->username  == ADMIN_NAME);
	}
	
	/**
	* generateRandID - Uses the random generation
	* of the getPasswordSalt function coupled with 
	* the hash function to generate a userid.
	*/
	
	function generateRandID(){
		$userid = $this->getPasswordSalt();
		return hash('sha256',$userid);
	}
	
	/**
	* getPasswordSal - Generates a randomized string for
	* for use in password slat and userid.
	*/
	function getPasswordSalt(){
		return substr( str_pad( dechex( mt_rand() ), 8, '0', STR_PAD_LEFT ), -8 );
	}
	
	/**
	* getPasswordHash -  Combines the salt with the password
	* to generate the encrypted password.
	*/
	function getPasswordHash($password){
		$salt = $this->getPasswordSalt();
		return $salt . ( hash('sha256', $salt . $password ) );
	}
	
	/** comparePassword - Compares the user supplied password with the
	encrypted password in the database. The salt is copied from the hash
	and added to the user password. Then it's encrypted in the same
	method as the password. If the resulting hash matches the existing
	hash then the password matches and the function returns true, false if
	otherwise.
	*/
	function comparePassword($password, $hash){	
		$salt = substr($hash, 0, 8);
		return $hash === $salt . ( hash('sha256', $salt . $password ));
	}
	
	/** confirmUserPass - Selects the encrypted password based on the
	username supplied. Checks to see if the supplied password matches
	the encrypted password.
	*/
	function confirmUserPass($username,$password) {
		$query = "SELECT password FROM ".TBL_USERS." WHERE username = ?";
		$stmt = $database->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($hash);
		$stmt->fetch();
		$stmt->close();
		if($hash == null){
			return 0;
		}
		$result = $this->comparePassword($password, $hash);
		return $result;
	}
}

//$mailer = new Mailer;
//$session = new Session($database);
//$form = new Form;
?>