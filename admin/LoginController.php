<?php
/**
 * Process.php
 * 
 * The Process class is meant to simplify the task of processing
 * user submitted forms, redirecting the user to the correct
 * pages if errors are found, or if form is successful. 
 * Also handles the logout procedure.
 */
require_once('../include/autoloader.php');
require_once("session.php");
require_once("mailer.php");
require_once("form.php");
//http://www.yiiframework.com/forum/index.php/topic/10927-a-using-statement-in-php/
$database = new DatabaseController(DB_SERVER, DB_USER, DB_PASS, DB_NAME);


class Process
{
   /* Class constructor */
	function __construct(){
		$this->Mailer = new Mailer();
		$this->Session = new Session();
		$this->Form = new Form();
      	/* User submitted login form */
      	if(isset($_POST['sublogin'])){
        	$this->Login();
      	}
      	/* User submitted registration form */
      	else if(isset($_POST['subjoin'])){
        	$this->procRegister();
      	}
      	/* User submitted forgot password form */
      	else if(isset($_POST['subforgot'])){
        	$this->procForgotPass();
      	}
      	/* User submitted edit account form */
      	else if(isset($_POST['subedit'])){
         	$this->procEditAccount();
      	}
      	/**
       	* The user should be directed here
       	* is if he wants to logout, which means user is
       	* logged in currently.
       	*/
      	else if($this->Session->logged_in){
        	$this->procLogout();
      	} else {
		/**
       	* Should not get here, which means user is viewing this page
       	* by mistake and therefore is redirected.
       	*/
       		header("Location: http://".$_SERVER['SERVER_NAME']."/admin/login.php");
      	}

   	}

	/**
	* procLogin - Processes the user submitted login form, if errors
	* are found, the user is redirected to correct the information,
	* if not, the user is effectively logged in to the system.
	*/
	function Login(){
		$loggedIn = false;
		$loggedIn = $this->Session->checkLogin();
      	/* Login attempt */
		if($loggedIn == false) {
			$loggedIn = $this->Session->Login($_POST['user'], $_POST['pass'], isset($_POST['remember']));
		}
      	
      	/* Login successful */
      	if($loggedIn) {
        	header("Location: http://".$_SERVER['SERVER_NAME']."/admin/login.php");
      	} else {
			/* Login failed */
         	$_SESSION['value_array'] = $_POST;
         	$_SESSION['error_array'] = $this->Form->getErrorArray();
         	header("Location: ".$this->Session->referrer);	
      	}
   	}
   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
	function procLogout(){
    	global $session;
      	$retval = $session->logout();
      	header("Location: ".$session->referrer);
   	}
   
   /**
    * procRegister - Processes the user submitted registration form,
    * if errors are found, the user is redirected to correct the
    * information, if not, the user is effectively registered with
    * the system and an email is (optionally) sent to the newly
    * created user.
    */
	function procRegister(){
		global $session, $form;
      	/* Registration attempt */
      	$retval = $session->register($_POST['user'], $_POST['pass'], $_POST['email']);
      	//echo $retval;
      	/* Registration Successful */
      	if($retval == 0){
        	$_SESSION['reguname'] = $_POST['user'];
         	$_SESSION['regsuccess'] = true;
         	header("Location: ".$session->referrer);
      	}else if($retval == 1){
			/* Error found with form */
         	$_SESSION['value_array'] = $_POST;
         	$_SESSION['error_array'] = $form->getErrorArray();
         	header("Location: ".$session->referrer);
		}else if($retval == 2){
			/* Registration attempt failed */
         	$_SESSION['reguname'] = $_POST['user'];
         	$_SESSION['regsuccess'] = false;
         	header("Location: ".$session->referrer);
   		}
   	}
   
   /**
    * procForgotPass - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
	function procForgotPass(){
    	global $database, $session, $mailer, $form;
      	/* Username error checking */
      	$subuser = $_POST['user'];
      	$field = "user";  //Use field name for username
      	if(!$subuser || strlen($subuser = trim($subuser)) == 0){
        	$form->setError($field, "* Username not entered<br>");
      	} else {
        	/* Make sure username is in database */
         	if(strlen($subuser) < 5 || strlen($subuser) > 30 || !ctype_alnum($subuser) || (!$database->usernameTaken($subuser))){
            	$form->setError($field, "* Username does not exist<br>");
         	}
      	}
      	if($form->getNumErrors() > 0){ /* Errors exist, have user correct them */
         	$_SESSION['value_array'] = $_POST;
         	$_SESSION['error_array'] = $form->getErrorArray();
      	} else {
			$newpass = $session->getPasswordSalt(); /* Generate new password */
			
			$usrinf = $database->getUserInfo($subuser); /* Get email of user */
			$email  = $usrinf['email'];
			/* Attempt to send the email with new password */
			if($mailer->sendNewPass($subuser,$email,$newpass)){
				/* Email sent, update database */
				$hash = $session->getPasswordHash($newpass);
				$database->updateUserPassword($subuser, $hash);
				$_SESSION['forgotpass'] = true;
			} else {
				/* Email failure, do not change password */
				$_SESSION['forgotpass'] = false;
			}
		}
		header("Location: ".$session->referrer);
	}
   
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password, which must be verified
    * before a change is made.
    */
	function procEditAccount(){
		global $session, $form;
      	/* Account edit attempt */
      	$retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email']);
      	/* Account edit successful */
      	if($retval){
        	$_SESSION['useredit'] = true;
         	header("Location: ".$session->referrer);
      	} else {
			/* Error found with form */
         	$_SESSION['value_array'] = $_POST;
         	$_SESSION['error_array'] = $form->getErrorArray();
         	header("Location: ".$session->referrer);
   		}
   	}
}
$process = new Process();
?>