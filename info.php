<?php

$salt = getPasswordSalt();
$pass = "NickleBee19";
//echo getPasswordHash($salt, $pass);

	function getPasswordSalt()
	{
		return substr( str_pad( dechex( mt_rand() ), 8, '0', STR_PAD_LEFT ), -8 );
	}
	
	// calculate the hash from a salt and a password
	function getPasswordHash( $salt, $password )
	{
		return $salt . ( hash('sha256', $salt . $password ) );
	}

// Show all information, defaults to INFO_ALL
phpinfo();

// Show just the module information.
// phpinfo(8) yields identical results.
//phpinfo(INFO_MODULES);

?>
