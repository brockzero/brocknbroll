<?php
require_once('../include/autoloader.php');
$database = new DatabaseController(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$comic = new ComicController($database);
switch((isset($_GET['action']) ? $_GET['action'] : ''))
{
	default:
		print $comic->Display();
	break;
	case 'display':
		print $comic->Display();
	break;
	case 'archive':
		print $comic->Archive();
	break;
	case 'search':
		$displayData = $comic->comicSearch();
	break;
}
?>
