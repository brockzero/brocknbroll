<?php
require_once("admin/database.php");
$database = new MySQLDB(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
require_once("include/words_class.php");
$article = new Words();
$pageTitle = $article->title;
$articleData = $article->getArticle();
require_once("include/variables.php");
require_once("header.php");
echo '<hr>';
echo $articleData;
require_once("footer.php");
$database->closeDB();
?>