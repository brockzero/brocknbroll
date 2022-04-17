<?php
require_once('../include/constants.php');
class DataAccess extends mysqli {
	private $num_active_users;   //Number of active users viewing site
	private $num_active_guests;  //Number of active guests viewing site
	private $num_members;        //Number of signed-up users


	//https://websitebeaver.com/php-pdo-vs-mysqli
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
}
?>