<?php
require_once "../include/constants.php";
require_once "session.php";

/*
if(!$session->logged_in)
{
	header("Location: http://".$_SERVER['SERVER_NAME']."/admin/main.php");
}
*/
require_once "update_class.php";

$update = new Update;

require_once "update_header.php";
echo $session->referrer;
echo $update->editing_menu;
switch($_GET['page'])
{
	default:
	echo '<h2>Update Site</h2>';
		echo $update->getComicIdeas();
	break;
	case 'article_update': 
		echo $update->articleUpdate();
	break;
	case 'article_view':
		echo $update->articleView();
	break;
	case 'article_edit':
		echo $update->articleEdit();
	break;
	
	case 'article_editor':
		echo $update->articleEditForm();
	break;
	case 'comic_update':
		echo $update->comicUpdate();
	break;
	case 'comic_view':
		echo $update->comicView();
	break;
	case 'comic_edit':
		echo $update->comicEdit();
	break;
	/*
	//redundant
	case 'delete_article':
		echo $update->deleteArticle();
	break;
	*/
	case 'batch_delete':
		echo $update->batchDelete();
	break;
} 
$database->closeDB();
require_once "update_footer.php";
?>