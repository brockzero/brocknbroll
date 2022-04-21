<?php
    require_once '../controllers/WordController.php';
    $Word = new WordController();
    if (!empty($_POST['keywords'])){
        $keywords = "%".$_POST['keywords']."%";
        print $Word->WordSearch($keywords);
    } else {
        print $Word->WordArchives();
    }
?>