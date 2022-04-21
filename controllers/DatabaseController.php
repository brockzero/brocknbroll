<?php
require_once("../admin/constants.php");
class DatabaseController {
    private $dbname = DB_NAME;
    private $dbserver = DB_SERVER;
    private $dbuser = DB_USER;
    private $dbpass = DB_PASS;
	function __construct(){
        $dsn = "mysql:dbname=" . $this->dbname . ";host=" . $this->dbserver . ";charset=utf8";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];

        try{
            $this->conn = new PDO($dsn, $this->dbuser, $this->dbpass, $options);
        }
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
	}
}
?>