<?php
/*** nullify any existing autoloads ***/
spl_autoload_register(null, false);

/**
* autoload
*
* @author Joe Sexton <joe.sexton@bigideas.com>
* @param  string $class
* @param  string $dir
* @return bool
*/
spl_autoload_register( 'controllers' );
spl_autoload_register( 'models' );
function controllers( $class, $dir = null )
{
	if ( is_null( $dir ) )
	{
		$dir = '../controllers/';
	}

	foreach ( scandir( $dir ) as $file )
	{
		// directory?
		if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
		{
			controllers( $class, $dir.$file.'/' );
		}

		// php file?
		if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) )
		{
			// filename matches class?
			if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class )
			{
				include $dir . $file;
			}
		}
	}
}

function models( $class, $dir = null )
{
	if ( is_null( $dir ) )
	{
		$dir = '../models/';
	}

	foreach ( scandir( $dir ) as $file )
	{
		// directory?
		if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
		{
			models( $class, $dir.$file.'/' );
		}

		// php file?
		if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) )
		{
			// filename matches class?
			if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class )
			{
				include $dir . $file;
			}
		}
	}
}

?>