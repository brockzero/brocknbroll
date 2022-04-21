<?php
    require_once '../controllers/ComicController.php';
    $Comic = new ComicController();
    print $Comic->GetComic();
?>