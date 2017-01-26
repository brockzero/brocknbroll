<?php
require_once('../include/constants.php');
class DatabaseController extends mysqli {
	private $num_active_users;   //Number of active users viewing site
	private $num_active_guests;  //Number of active guests viewing site
	private $num_members;        //Number of signed-up users

    public function __construct($host, $user, $pass, $db) {
        parent::__construct($host, $user, $pass, $db);
		$this->set_charset("utf8");
        if ($this->connect_error) {
            die('Connect Error (' . $this->connect_errno . ') '
                    . $this->connect_error);
        }
    }
	
	function closeDB(){
		$this->close();
	}
	
	function addNewUser($user, $password, $email) {
		$userLevel = USER_LEVEL;
		$query = "INSERT INTO ".TBL_USERS." (`username`, `password`, `userlevel`, `email`) VALUES (?,?,?,?)";
		$stmt = $this->prepare($query);
		$stmt->bind_param("ssis", $user, $password, $userLevel, $email);
		$stmt->execute();
		if(!$stmt->error){
			$bool = TRUE;
		} else {
			$bool = FALSE;	
		}
		$stmt->close();
		return $bool;
		//insert data into a database
		//username		password 		userid 		userlevel 		email 		timestamp
		//gen'd		level 1  					updated
	}
	
	function addActiveUser($username, $time){
		$query = "UPDATE ".TBL_USERS." SET timestamp = ? WHERE username = ?";
		$stmt = $this->prepare($query);
		$stmt->bind_param("is", $time, $username);
		$stmt->execute();
		$stmt->close();
		if(!TRACK_VISITORS) {
			return;
		}
		$query = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES (?, ?)";
		$stmt = $this->prepare($query);
		$stmt->bind_param("si", $username, $time);
		$stmt->execute();
		$stmt->close();
		$this->calcNumActiveUsers();
	}
	/* removeActiveUser */
   	function removeActiveUser($username){
		if(!TRACK_VISITORS){
			return;
		}
		$query = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = ?";
		$stmt = $this->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->close();
		$this->calcNumActiveUsers();
	}
   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
	function calcNumActiveUsers(){
		/* Calculate number of users at site */
		$query = "SELECT * FROM ".TBL_ACTIVE_USERS;
		$stmt = $this->query($query);
		$this->num_active_users = $this->num_rows;
		$this->close();
	}
	function getUserPass($username){
		$db_password = 0;
		$stmt = $this->prepare("SELECT password FROM ".TBL_USERS." WHERE username = ?");
		$stmt->bind_param("s", $username);
	  	$stmt->execute();
		$stmt->bind_result($db_password);
		$stmt->fetch();
		$stmt->close();
		if($db_password == 0){
        	return 0; //Indicates username failure
      	} else {
			return $db_password;
	  	}
	}
	/**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
	function getUserInfo($username){
		$dbarray = array();
		$stmt = $this->prepare("SELECT password, userid, userlevel, email, timestamp FROM ".TBL_USERS." WHERE username = ?");
		$stmt->bind_param("s", $username);
	  	$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($dbarray['password'],$dbarray['userid'],$dbarray['userlevel'],$dbarray['email'],$dbarray['timestamp']);
		if ($stmt->num_rows == 1) {
			$stmt->fetch();
			$dbarray['username'] = $username;
			return $dbarray;
		} else {
			return NULL;	
		}
		$stmt->close();
	}

	function confirmUserID($username, $userid){
     	/* Verify that user is in database */
		$query = "SELECT userid FROM ".TBL_USERS." WHERE username = ?";
		$stmt = $this->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($db_userid);
		$stmt->fetch();
		$stmt->close();
	  	if(!$db_userid) {
		 	return 1; //Indicates username failure
	  	}
      	/* Validate that userid is correct */
      	if($userid == $db_userid) {
        	return 0; //Success! Username and userid confirmed
      	} else {
         	return 2; //Indicates userid invalid
      	}
   	}
	//used for register.php
	function usernameTaken($subusername) {
		$query = "SELECT username FROM ".TBL_USERS." WHERE username = ?";
		$stmt = $this->prepare($query);
		$stmt->bind_param("s", $subusername);
		$stmt->execute();
		$stmt->bind_result($username);
		$stmt->fetch();
		$stmt->close();
		if (empty($username)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
	function getNumMembers(){
	  if($this->num_members < 0){
		 $query = "SELECT * FROM ".TBL_USERS;
		 $stmt = $this->query($query);
		 $this->num_members = $this->num_rows;
		 $this->close();
	  }
	  return $this->num_members;
	}
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
	function updateUserID($username, $value){
		$stmt = $this->prepare("UPDATE ".TBL_USERS." SET userid = ? WHERE username = ?");
		$stmt->bind_param("ss", $value, $username);
		return $stmt->execute();
	}
	function updateUserPassword($username, $value){
		$stmt = $this->prepare("UPDATE ".TBL_USERS." SET password = ? WHERE username = ?");
		$stmt->bind_param("ss", $value, $username);
		return $stmt->execute();
	}
	function updateUserEmail($username, $value){
		$stmt = $this->prepare("UPDATE ".TBL_USERS." SET email = ? WHERE username = ?");
		$stmt->bind_param("ss", $value, $username);
		return $stmt->execute();
	}
	function updateUserLevel($username, $value){
		$stmt = $this->prepare("UPDATE ".TBL_USERS." SET userlevel = ? WHERE username = ?");
		$stmt->bind_param("is", $value, $username);
		return $stmt->execute();
	}
}
?>