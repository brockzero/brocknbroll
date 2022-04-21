<?php
    require_once '../controllers/WordController.php';
    $Word = new WordController();
    print $Word->GetArticle();
?>