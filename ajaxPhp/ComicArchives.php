<?php
    require_once '../controllers/ComicController.php';
    $Comic = new ComicController();
        if (!empty($_POST['keywords'])){
        $keywords = "%".$_POST['keywords']."%";
        print $Comic->ComicSearch($keywords);
    } else {
        print $Comic->ComicArchives();
    }
?>