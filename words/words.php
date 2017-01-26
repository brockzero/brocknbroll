<?php
require "admin/database.php";
$database = new MySQLDB(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
require "include/words_class.php";
$article = new Words();
$pageTitle = $article->title;
$articleData = $article->getArticle();
require "include/variables.php";
require "layout/header.php";
echo '<hr>';
echo $articleData;
require "layout/footer.php";
$database->closeDB();
?>